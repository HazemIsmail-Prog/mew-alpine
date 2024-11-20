<?php

namespace App\Livewire\Pages\Contracts;

use Livewire\Attributes\Layout;
use Livewire\Component;

class ContractIndex extends Component
{

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.pages.contracts.contract-index');
    }
}
