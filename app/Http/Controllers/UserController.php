<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Contract;
use App\Models\Stakeholder;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {

        if (Auth::user()->cannot('viewAny', User::class)) {
            abort(404);
        }

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
        if (Auth::user()->cannot('viewAny', User::class)) {
            abort(403, 'Unauthorized action.');
        }
        $filters = $request->query('filters');
        $users = User::query()
            ->when(isset($filters['search']), function (Builder $q) use ($filters) {
                $q->where('name', 'like', "%" . $filters['search'] . "%");
            })
            ->latest()
            ->paginate(10);

        return UserResource::collection($users);
    }

    public function store(Request $request)
    {
        if (Auth::user()->cannot('create', User::class)) {
            abort(403, 'Unauthorized action.');
        }

        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'contract_ids' => 'required|array',
            'stakeholder_id' => 'required',
            'role' => 'required',
        ]);
        $user = User::create($request->except('contract_ids','isTrusted'));
        $user->contracts()->sync($request->contract_ids);
        return new UserResource($user);

    }

    public function update(Request $request, User $user)
    {
        if (Auth::user()->cannot('update', $user)) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'contract_ids' => 'required|array',
            'stakeholder_id' => 'required',
            'role' => 'required',
        ]);

        $user->update($request->except('contract_ids', 'password'));
        if ($request->password) {
            $user->password = $request->password;
            $user->save();
        }
        $user->contracts()->sync($request->contract_ids);
        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        if (Auth::user()->cannot('delete', $user)) {
            abort(403, 'Unauthorized action.');
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function toggleActive(Request $request, User $user)
    {
        if (Auth::user()->cannot('update', $user)) {
            abort(403, 'Unauthorized action.');
        }
        $user->is_active = $request->input('is_active');
        $user->save();
        return response()->json($user);
    }
}
