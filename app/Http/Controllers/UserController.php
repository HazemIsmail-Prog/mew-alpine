<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Stakeholder;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $contracts = Contract::query()
            ->orderBy('name')
            ->get();

        $stakeholders = Stakeholder::query()
            ->orderBy('name')
            ->get();

        $roles = [
            ['id' => 'superAdmin', 'name' => __('Super Admin')],
            ['id' => 'admin', 'name' => __('Admin')],
            ['id' => 'user', 'name' => __('User')],
        ];

        return view('pages.users.index', [
            'contracts' => $contracts,
            'stakeholders' => $stakeholders,
            'roles' => $roles,
        ]);
    }

    public function getData(Request $request)
    {
        $filters = $request->query('filters');
        $users = User::query()
            ->with('contracts:id')
            ->when($filters['search'], function (Builder $q) use ($filters) {
                $q->where('name', 'like', "%" . $filters['search'] . "%");
            })
            ->latest()
            ->paginate(10)
        ;
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'contract_ids' => 'required|array',
            'stakeholder_id' => 'required',
            'role' => 'required',
        ]);
        $user = User::create($request->except('contract_ids'));
        $user->contracts()->sync($request->contract_ids);
        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'contract_ids' => 'required|array',
            'stakeholder_id' => 'required',
            'role' => 'required',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->except('contract_ids','password'));
        if ($request->password) {
            $user->password = $request->password;
            $user->save();
        }
        $user->contracts()->sync($request->contract_ids);
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function toggleActive(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->is_active = $request->input('is_active');
        $user->save();
        return response()->json($user);
    }
}
