<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vacation;
use Illuminate\Http\Request;

class VacationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            // For calendar view, fetch all vacations that overlap with the month
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

                $vacations = Vacation::query()
                    ->where(function ($q) use ($rangeStart, $rangeEnd) {
                        // Vacation starts in range, ends in range, or overlaps the entire range
                        $q->whereBetween('start_date', [$rangeStart, $rangeEnd])
                            ->orWhereBetween('end_date', [$rangeStart, $rangeEnd])
                            ->orWhere(function ($q) use ($rangeStart, $rangeEnd) {
                                $q->whereDate('start_date', '<=', $rangeStart)
                                    ->whereDate('end_date', '>=', $rangeEnd);
                            });
                    })
                    ->orderBy('start_date')
                    ->get();

                return response()->json($vacations);
            }
            
        }
        $users = User::query()
            ->orderBy('name')
            ->get();
        return view('pages.vacations.index', [
            'users' => $users,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date|beforeOrEqual:end_date',
            'end_date' => 'required|date|afterOrEqual:start_date',
            'reason' => 'nullable|string',
            'type' => 'required|string',
        ]);
        $vacation = Vacation::create($validated);
        return response()->json($vacation);
    }

    public function destroy(Vacation $vacation)
    {
        $vacation->delete();
        return response()->json(['message' => 'Vacation deleted successfully']);
    }

    public function update(Request $request, Vacation $vacation)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date|beforeOrEqual:end_date',
            'end_date' => 'required|date|afterOrEqual:start_date',
            'reason' => 'nullable|string',
            'type' => 'nullable|string',
        ]);
        $vacation->update($validated);
        return response()->json($vacation);
    }
}
