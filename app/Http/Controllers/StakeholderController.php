<?php

namespace App\Http\Controllers;

use App\Models\Stakeholder;
use Illuminate\Http\Request;

class StakeholderController extends Controller
{
    public function index()
    {
        return view('pages.stakeholders.index');
    }

    public function getData(Request $request)
    {
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
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $stakeholder = Stakeholder::create($request->all());

        return response()->json($stakeholder, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $stakeholder = Stakeholder::findOrFail($id);
        $stakeholder->update($request->all());

        return response()->json($stakeholder);
    }

    public function destroy($id)
    {
        $stakeholder = Stakeholder::findOrFail($id);
        $stakeholder->delete();

        return response()->json(['message' => 'Stakeholder deleted successfully']);
    }

    public function toggleActive(Request $request, $id)
    {
        $stakeholder = Stakeholder::findOrFail($id);
        $stakeholder->is_active = $request->input('is_active');
        $stakeholder->save();

        return response()->json($stakeholder);
    }
}
