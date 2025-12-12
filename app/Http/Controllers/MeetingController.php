<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Contract;
class MeetingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            if ($request->has('month') && $request->has('year')) {

                // Calculate the previous, current, and next months
                $currentMonth = (int) $request->month;
                $currentYear = (int) $request->year;

                // Previous month
                $prevMonthDate = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->subMonth();
                // Next month
                $nextMonthDate = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->addMonth();

                $rangeStart = $prevMonthDate->copy()->startOfMonth()->toDateString();
                $rangeEnd = $nextMonthDate->copy()->endOfMonth()->toDateString();

                $meetings = Meeting::query()
                    ->with('contracts')
                    ->where(function ($q) use ($rangeStart, $rangeEnd) {
                        // Meeting starts in range, ends in range, or overlaps the entire range
                        $q->whereBetween('date', [$rangeStart, $rangeEnd])
                            ->orWhere(function ($q) use ($rangeStart, $rangeEnd) {
                                $q->whereDate('date', '<=', $rangeStart)
                                    ->whereDate('date', '>=', $rangeEnd);
                            });
                    })
                    ->orderBy('date')
                    ->orderBy('start_time')
                    ->get();

                return response()->json($meetings);
            }
        }
        $locations = Meeting::query()
            ->select('location')
            ->distinct()
            ->pluck('location');
        $contracts = Contract::query()
            ->orderBy('name')
            ->get();
        return view('pages.meetings.index', [
            'locations' => $locations,
            'contracts' => $contracts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|afterOrEqual:start_time',
            'location' => 'required|string|max:255',
            'attendees' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'agenda' => 'nullable|string',
            'contract_ids' => 'nullable|array|exclude',
        ]);

        DB::beginTransaction();
        $meeting = Meeting::create($validated);
        try {
            if($request->has('contract_ids')){
                $meeting->contracts()->sync($request->contract_ids);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Meeting creation failed', 'error' => $e->getMessage()], 500);
        }
        return response()->json(['message' => 'Meeting created successfully', 'data' => $meeting->load('contracts')]);
    }

    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|afterOrEqual:start_time',
            'location' => 'required|string|max:255',
            'attendees' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'agenda' => 'nullable|string',
            'contract_ids' => 'nullable|array|exclude',
        ]);
        DB::beginTransaction();
        try {
            if($request->has('contract_ids')){
                $meeting->contracts()->sync($request->contract_ids);
            }
            $meeting->update($validated);
            DB::commit();
            return response()->json(['message' => 'Meeting updated successfully', 'data' => $meeting->load('contracts')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Meeting update failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Meeting $meeting)
    {
        DB::beginTransaction();
        try {
            if($meeting->contracts()->count() > 0){
                $meeting->contracts()->detach();
            }
            $meeting->delete();
            DB::commit();
            return response()->json(['message' => 'Meeting deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Meeting deletion failed', 'error' => $e->getMessage()], 500);
        }
    }
}
