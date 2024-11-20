<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserFrom extends Form
{
    public $id;

    #[Validate('required')]
    public $name;

    #[Validate('required')]
    public $type = 'user';

    #[Validate('required')]
    public $username;

    #[Validate('required_if:id,=,null')]
    public $password;

    public $is_active;

    #[Validate('required')]
    public $stakeholder_id;

    #[Validate('required')]
    public $contract_ids = [];

    public function updateOrCreate()
    {
        $this->validate();
        if ($this->id) {
            $user = User::updateOrCreate(['id' => $this->id], $this->except('contract_ids', 'password'));
            if ($this->password) {
                $user->password = $this->password;
                $user->save();
            }
        } else {
            $user = User::updateOrCreate(['id' => $this->id], $this->except('contract_ids'));
        }
        $user->contracts()->sync($this->contract_ids);

        $user->load('contracts:id'); // Only fetch user IDs to reduce data load
        $user->contract_ids = $user->contracts->pluck('id');
        return $user;
    }
}
