<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use Livewire\Attributes\Layout;

class CalibrationRecall extends Component
{

    #[Layout('components.layouts.vanilla')]

    public $customerData = [];

    public function mount()
    {
        // Retrieve the data from the session
        $this->customerData = session()->pull('calibrationRecallData', []);
    }

    public function render()
    {
        return view('livewire.calibration-recall', [
            'customerData' => $this->customerData,
        ]);
    }
}
