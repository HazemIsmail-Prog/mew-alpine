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
