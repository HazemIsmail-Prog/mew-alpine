<?php

namespace App\Http\Controllers;

use App\Models\Stakeholder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StakeholderController extends Controller
{
    public function index()
    {
        if (Auth::user()->cannot('viewAny', Stakeholder::class)) {
            abort(404);
        }
        return view('pages.stakeholders.index');
    }

    public function getData(Request $request)
    {
        if (Auth::user()->cannot('viewAny', Stakeholder::class)) {
            abort(403, 'Unauthorized action.');
        }
        $limit = 10;
        $filters = $request->query('filters');

        $query = Stakeholder::query()
            ->latest();

        if ($filters['search']) {
            $query->where('name', 'like', "%" . $filters['search'] . "%");
        }

        $stakeholders = $query->paginate($limit);
        return response()->json($stakeholders);
    }

    public function store(Request $request)
    {
        if (Auth::user()->cannot('create', Stakeholder::class)) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $stakeholder = Stakeholder::create($request->all());

        return response()->json($stakeholder, 201);
    }

    public function update(Request $request, Stakeholder $stakeholder)
    {
        if (Auth::user()->cannot('update', $stakeholder)) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $stakeholder->update($request->all());

        return response()->json($stakeholder);
    }

    public function destroy(Stakeholder $stakeholder)
    {
        if (Auth::user()->cannot('delete', $stakeholder)) {
            abort(403, 'Unauthorized action.');
        }
        $stakeholder->delete();

        return response()->json(['message' => 'Stakeholder deleted successfully']);
    }

    public function toggleActive(Request $request, Stakeholder $stakeholder)
    {
        if (Auth::user()->cannot('update', $stakeholder)) {
            abort(403, 'Unauthorized action.');
        }
        $stakeholder->is_active = $request->input('is_active');
        $stakeholder->save();

        return response()->json($stakeholder);
    }
}
