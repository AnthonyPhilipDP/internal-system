<?php

namespace App\Livewire;
use App\Models\User;

use Livewire\Component;
use App\Models\Equipment;

class Worksheet extends Component
{

    public $users;
    public $equipment;

    public function mount()
    {
        $this->users = User::all();
        $this->equipment = Equipment::all();
    }
    public function render()
    {
        return view('livewire.worksheet');
    }
}
