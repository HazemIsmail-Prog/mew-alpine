<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{

    public function index()
    {
        if (Auth::user()->cannot('viewAny', Contract::class)) {
            abort(404);
        }
        return view('pages.contracts.index');
    }

    public function getData(Request $request)
    {
        if (Auth::user()->cannot('viewAny', Contract::class)) {
            abort(403, 'Unauthorized action.');
        }
        $limit = 10;
        $filters = $request->query('filters');

        $query = Contract::query()
            ->latest();

        if ($filters['search']) {
            $query->where('name', 'like', "%" . $filters['search'] . "%");
        }

        $contracts = $query->paginate($limit);
        return response()->json($contracts);
    }

    public function store(Request $request)
    {
        if (Auth::user()->cannot('create', Contract::class)) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $contract = Contract::create($request->all());

        return response()->json($contract, 201);
    }

    public function update(Request $request, Contract $contract)
    {
        if (Auth::user()->cannot('update', $contract)) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $contract->update($request->all());

        return response()->json($contract);
    }

    public function destroy(Contract $contract)
    {
        if (Auth::user()->cannot('delete', $contract)) {
            abort(403, 'Unauthorized action.');
        }
        $contract->delete();

        return response()->json(['message' => 'Contract deleted successfully']);
    }

    public function toggleActive(Request $request, Contract $contract)
    {
        if (Auth::user()->cannot('update', $contract)) {
            abort(403, 'Unauthorized action.');
        }
        $contract->is_active = $request->input('is_active');
        $contract->save();

        return response()->json($contract);
    }
}
