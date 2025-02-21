<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Equipment; // Ensure the Equipment model exists and follows your organization's conventions

class AcknowledgmentReceipt extends Component
{
    // Holds the equipment records with the maximum ar_id
    public $equipment;

    /**
     * Mount the component and fetch all equipment records with the maximum ar_id.
     *
     * Since ar_id is stored as a string but contains numeric values,
     * we cast it to UNSIGNED for proper numerical ordering.
     */
    public function mount()
    {
        // Get the maximum ar_id (casting to UNSIGNED for proper comparison)
        $maxArId = Equipment::orderByRaw('CAST(ar_id AS UNSIGNED) DESC')->value('ar_id');

        // Retrieve all records that have the maximum ar_id value
        $this->equipment = Equipment::where('ar_id', $maxArId)->get();
    }

    /**
     * Render the view with equipment data.
     *
     * The view 'livewire.acknowledgment-receipt' can now access the equipment data via the $equipment variable.
     */
    public function render()
    {
        return view('livewire.acknowledgment-receipt', [
            'equipment' => $this->equipment,
        ]);
    }
}