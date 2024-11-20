<?php

namespace App\Livewire;

use App\Livewire\Forms\StakeholderFrom;
use App\Models\Stakeholder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class StakeholderIndex extends Component
{
    use WithPagination;

    public StakeholderFrom $stakeholderForm;

    #[Computed()]
    public function stakeholders() {
        return Stakeholder::query()
        ->paginate(10)
        ;
    }

    public function toggleStakeholderIsActive(Stakeholder $stakeholder, $val)
    {
        $stakeholder->update(['is_active' => $val]);
    }

    public function saveStakeholder()
    {
        $stakeholder = $this->stakeholderForm->updateOrCreate();
        $this->dispatch('stakeholderCreated', $stakeholder);
    }

    public function deleteStakeholder(Stakeholder $stakeholder)
    {
        $stakeholder->delete();
        $this->dispatch('stakeholderDeleted', $stakeholder);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.stakeholder-index');
    }
}
