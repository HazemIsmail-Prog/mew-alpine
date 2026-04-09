<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Action;
use Illuminate\Support\Facades\Auth;

class ActionController extends Controller
{
    public function index(Contract $contract)
    {
        $actions = $contract->actions()->with('creator')->orderBy('date')->get();
        return response()->json($actions);
    }

    public function store(Request $request, Contract $contract)
    {
        $request->validate([
            'date' => 'required|date',
            'authority' => 'nullable|string',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $is_milestone = $request->has('is_milestone') ? true : false;
        $action = $contract->actions()->create([
            'date' => $request->date,
            'authority' => $request->authority ?? null,
            'description' => $request->description ?? null,
            'notes' => $request->notes ?? null,
            'created_by' => Auth::id(),
            'is_milestone' => $is_milestone,
        ]);
        return response()->json($action->load('creator'));
    }

    public function update(Request $request, Action $action)
    {
        $request->validate([
            'date' => 'required|date',
            'authority' => 'nullable|string',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $is_milestone = $request->has('is_milestone') ? true : false;
        $action->update([
            'date' => $request->date,
            'authority' => $request->authority,
            'description' => $request->description,
            'notes' => $request->notes,
            'is_milestone' => $is_milestone,
        ]);
        return response()->json($action->load('creator'));
    }

    public function destroy(Action $action)
    {
        $action->delete();
        return response()->json($action->load('creator'));
    }
}
