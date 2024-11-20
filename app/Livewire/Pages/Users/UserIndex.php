<?php

namespace App\Livewire\Pages\Users;

use App\Livewire\Forms\UserFrom;
use App\Models\Contract;
use App\Models\Stakeholder;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{

    use WithPagination;

    public UserFrom $userForm;

    #[Computed()]
    public function users() {
        return User::query()
        ->with('contracts:id') // Only fetch contract IDs to reduce data load
        ->paginate(10)
        ->through(function ($user) {
            $user->contract_ids = $user->contracts->pluck('id');
            return $user;
        })
        ;
    }

    #[Computed()]
    public function contracts()
    {
        return Contract::query()
            // ->orderBy('name') // Optional secondary ordering by name
            ->get();
    }

    #[Computed()]
    public function stakeholders()
    {
        return Stakeholder::query()
            ->orderBy('name') // Optional secondary ordering by name
            ->get();
    }

    public function toggleUserIsActive(User $user, $val)
    {
        $user->update(['is_active' => $val]);
    }

    #[Computed()]
    public function types()
    {
        return [
            ['id' => 'superAdmin', 'name' => __('Super Admin')],
            ['id' => 'admin', 'name' => __('Admin')],
            ['id' => 'user', 'name' => __('User')],
        ];
    }

    public function saveUser()
    {
        // dd($this->userForm);
        $user = $this->userForm->updateOrCreate();
        $this->dispatch('userCreated', $user);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.pages.users.user-index');
    }
}
