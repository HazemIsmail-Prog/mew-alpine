<?php

namespace App\Livewire\Forms;

use App\Models\Stakeholder;
use Livewire\Attributes\Validate;
use Livewire\Form;

class StakeholderFrom extends Form
{
    public $id;

    #[Validate('required')]
    public $name;

    public $is_active;

    public function updateOrCreate()
    {
        $this->validate();
        $stakeholder = Stakeholder::updateOrCreate(['id' => $this->id], $this->all());
        return $stakeholder;
    }
}
