<?php

use Livewire\Volt\Component;
use App\Models\Equipment;

new class extends Component {
    public $selectedMonth;
    public $selectedYear;
    public $equipment;

    public function mount()
    {
        $this->equipment = collect(); // Initialize as an empty collection
    }

    public function filterEquipment()
    {
        // Ensure both month and year are selected
        if (!$this->selectedMonth || !$this->selectedYear) {
            $this->equipment = collect(); // Reset to an empty collection
            return;
        }

        // Query equipment based on the selected month and year
        $this->equipment = Equipment::whereMonth('calibrationDue', $this->selectedMonth)
            ->whereYear('calibrationDue', $this->selectedYear)
            ->get();
    }
}; ?>

<div>
    <form wire:submit.prevent="filterEquipment" class="space-y-4">
        <div class="flex items-center space-x-4 justify-center">
            <!-- Month Selector -->
            <select wire:model="selectedMonth" class="border-gray-300 rounded-full shadow-sm px-12 py-3">
                <option value="">Select Month</option>
                @foreach (range(1, 12) as $month)
                    <option value="{{ $month }}">{{ \Carbon\Carbon::create()->month($month)->format('F') }}</option>
                @endforeach
            </select>

            <!-- Year Selector -->
            <select wire:model="selectedYear" class="border-gray-300 rounded-full shadow-sm px-12 py-3">
                <option value="">Select Year</option>
                @foreach (range(now()->year + 1, now()->year - 27, -1) as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>

            <!-- Submit Button -->
            <button type="submit" class="uppercase transition duration-300 ease-in-out hover:-translate-y-1 hover:scale-110 text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 font-medium rounded-full text-sm px-5 py-3 text-center me-2">
                Filter
            </button>
        </div>
    </form>

    <div class="mt-6 justify-center items-center text-center">
        @if ($equipment->isEmpty())
            <p class="text-blue-600 border border-blue-200 bg-blue-200 rounded-full mx-64 py-4 text-xl font-semibold">
                Please select desired month and year to recall the equipment
            </p>
        @else
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2">Equipment ID</th>
                        <th class="border border-gray-300 px-4 py-2">Make</th>
                        <th class="border border-gray-300 px-4 py-2">Model</th>
                        <th class="border border-gray-300 px-4 py-2">Calibration Due</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($equipment as $item)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{ $item->equipment_id }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $item->make }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $item->model }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ \Carbon\Carbon::parse($item->calibrationDue)->format('F j, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
