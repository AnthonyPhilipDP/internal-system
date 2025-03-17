<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Equipment;
use Illuminate\Support\Facades\Session;

class AcknowledgmentReceipt extends Component
{
    public $equipment;

    public function mount()
    {
        // Retrieve the current selected ar_id from the session
        $currentArId = Session::get('ar_id');

        // $maxArId = Equipment::orderByRaw('CAST(ar_id AS UNSIGNED) DESC')->value('ar_id');
        // $this->equipment = Equipment::with('accessory')
        //     ->where('ar_id', $maxArId)
        //     ->get();
        
        // Use the currentArId to filter the equipment
        $this->equipment = Equipment::with(['accessory', 'customer.contactPerson'])
            ->where('ar_id', $currentArId)
            ->get();

        $this->totalEquipmentCount = $this->equipment->count();
    }

    public function render()
    {
        // Split the equipment into chunks of 10
        $equipmentChunks = $this->equipment->chunk(10);

        return view('livewire.acknowledgment-receipt', [
            'equipmentChunks' => $equipmentChunks,
            'totalEquipmentCount' => $this->totalEquipmentCount,
        ]);
    }
}