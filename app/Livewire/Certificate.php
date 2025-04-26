<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;

class Certificate extends Component
{
    public $equipmentData;

    public function mount()
    {
        $this->equipmentData = session('selectedCertificateData', []);
        
        // Fetch customer names and transaction IDs for each equipment
        $this->equipmentData = array_map(function ($equipment) {
            $customer = Customer::find($equipment['customer_id']);
            $equipment['customer_name'] = $customer->name;
            $equipment['customer_address'] = $customer->address;

            return $equipment;
        }, $this->equipmentData);
    }

    public function render()
    {
        return view('livewire.certificate', [
            'equipmentData' => $this->equipmentData,
        ])->layout('components.layouts.vanilla');
    }
}
