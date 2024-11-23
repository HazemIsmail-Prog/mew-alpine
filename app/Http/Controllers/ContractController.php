<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{

    public function index()
    {
        return view('pages.contracts.index');
    }

    public function getData(Request $request)
    {
        $limit = 10;
        $filters = $request->query('filters');

        $query = Contract::query()
        ->latest()
        ;

        if ($filters['search']) {
            $query->where('name', 'like', "%" . $filters['search'] . "%");
        }

        $contracts = $query->paginate($limit);
        return response()->json($contracts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $contract = Contract::create($request->all());

        return response()->json($contract, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $contract = Contract::findOrFail($id);
        $contract->update($request->all());

        return response()->json($contract);
    }

    public function destroy($id)
    {
        $contract = Contract::findOrFail($id);
        $contract->delete();

        return response()->json(['message' => 'Contract deleted successfully']);
    }

    public function toggleActive(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);
        $contract->is_active = $request->input('is_active');
        $contract->save();

        return response()->json($contract);
    }
}
