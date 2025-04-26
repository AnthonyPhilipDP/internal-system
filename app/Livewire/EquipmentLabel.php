<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Equipment;

class EquipmentLabel extends Component
{
    public $equipmentDataChunks;

    public function mount()
    {
        $equipmentData = session('selectedEquipmentData', []);
        
        // Fetch customer names based on customer_id
        $equipmentData = array_map(function ($equipment) {
            $customer = Customer::find($equipment['customer_id']);
            $equipment['customer_name'] = $customer->name;

            return $equipment;
        }, $equipmentData);
 
        // Chunk the equipment data into groups of 15
        $this->equipmentDataChunks = collect($equipmentData)->chunk(18);
    }

    public function render()
    {
        return view('livewire.equipment-label', [
            'equipmentDataChunks' => $this->equipmentDataChunks,
        ])->layout('components.layouts.vanilla');
    }
}