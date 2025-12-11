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
                $monthStart = $request->year . '-' . str_pad($request->month, 2, '0', STR_PAD_LEFT) . '-01';
                $monthEnd = date('Y-m-t', strtotime($monthStart));
                
                $vacations = Vacation::query()
                    ->where(function ($q) use ($monthStart, $monthEnd) {
                        // Vacation starts in this month
                        $q->whereBetween('start_date', [$monthStart, $monthEnd])
                            // Or vacation ends in this month
                            ->orWhereBetween('end_date', [$monthStart, $monthEnd])
                            // Or vacation spans the entire month
                            ->orWhere(function ($q) use ($monthStart, $monthEnd) {
                                $q->whereDate('start_date', '<=', $monthStart)
                                    ->whereDate('end_date', '>=', $monthEnd);
                            });
                    })
                    ->orderBy('start_date')
                    ->get();
                return response()->json($vacations);
            }
            
            // Original paginated list view
            $vacations = Vacation::query()
                ->select('*')
                ->selectRaw('
                    CASE 
                        WHEN start_date <= ? AND end_date >= ? THEN 0
                        WHEN start_date > ? THEN 1
                        ELSE 2
                    END as vacation_status_order
                ', [now(), now(), now()])
                ->when($request->filters['date'], function ($q) use ($request) {
                    $q->where(function ($q) use ($request) {
                        $q->whereDate('start_date', '<=', $request->filters['date'])
                            ->whereDate('end_date', '>=', $request->filters['date']);
                    });
                })
                ->orderBy('vacation_status_order')
                ->orderBy('start_date')
                ->orderByDesc('end_date')
                ->paginate(10);
            return response()->json($vacations);
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
