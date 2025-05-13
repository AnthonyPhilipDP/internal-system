<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Customer;
use Livewire\Attributes\Layout;

class CalibrationRecall extends Component
{

    #[Layout('components.layouts.vanilla')]

    public $customerData = [];
    public $filteredMonth;
    public $filteredYear;

    public function mount()
    {
        // Retrieve the data from the session
        $this->customerData = session()->pull('calibrationRecallData', []);

        // Retrieve the filtered month and year from the session
        $filter = session()->pull('calibrationRecallFilter', [
            'month' => null,
            'year' => null,
        ]);

        $this->filteredMonth = $filter['month'];
        $this->filteredYear = $filter['year'];

        // Filter the customerData by calibrationDue
        if ($this->filteredMonth && $this->filteredYear) {
            foreach ($this->customerData as $key => $customer) {
                // Filter the equipment array for the customer
                $filteredEquipment = array_filter($customer['equipment'], function ($equipment) {
                    $calibrationDue = Carbon::parse($equipment['calibrationDue']);
                    return (
                        $calibrationDue->format('m') === $this->filteredMonth &&
                        $calibrationDue->format('Y') === $this->filteredYear
                    );
                });

                // If no equipment matches, remove the customer
                if (empty($filteredEquipment)) {
                    unset($this->customerData[$key]);
                } else {
                    // Otherwise, update the customer's equipment with the filtered list
                    $this->customerData[$key]['equipment'] = array_values($filteredEquipment);
                }
            }

            // Reindex the array to avoid gaps in keys
            $this->customerData = array_values($this->customerData);
        }
    }

    public function render()
    {
        return view('livewire.calibration-recall', [
            'customerData' => $this->customerData,
        ]);
    }
}
