<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use Livewire\Attributes\Layout;

class Certificate extends Component
{
    #[Layout('components.layouts.vanilla')]

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
        if($this->equipmentData[0]['withPabLogo'] === true)
        {
            if($this->equipmentData[0]['withCalibrationDue'] === true)
            {
                return view('livewire.certificates.withPabLogo.withCalibrationDue', [
                    'equipmentData' => $this->equipmentData,
                ]);
            }
    
            else if($this->equipmentData[0]['withCalibrationDue'] === false)
            {
                return view('livewire.certificates.withPabLogo.withoutCalibrationDue', [
                    'equipmentData' => $this->equipmentData,
                ]);
            }
        }

        else 
        {
            if($this->equipmentData[0]['withCalibrationDue'] === true)
            {
                return view('livewire.certificates.withCalibrationDue', [
                    'equipmentData' => $this->equipmentData,
                ]);
            }
    
            else if($this->equipmentData[0]['withCalibrationDue'] === false)
            {
                return view('livewire.certificates.withoutCalibrationDue', [
                    'equipmentData' => $this->equipmentData,
                ]);
            }
        }

    }
}
