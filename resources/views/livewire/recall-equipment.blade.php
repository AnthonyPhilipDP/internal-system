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
            <div class="flex flex-col">
                <div class="-m-1.5 overflow-x-auto">
                  <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="border border-gray-200 rounded-lg divide-y divide-gray-200 dark:border-neutral-700 dark:divide-neutral-700">
                      <div class="py-3 px-4">
                        <div class="relative max-w-xs">
                          <label for="hs-table-search" class="sr-only">Search</label>
                          <input type="text" name="hs-table-search" id="hs-table-search" class="py-1.5 sm:py-2 px-3 ps-9 block w-full border-gray-200 shadow-2xs rounded-lg sm:text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="Search for items">
                          <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                            <svg class="size-4 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <circle cx="11" cy="11" r="8"></circle>
                              <path d="m21 21-4.3-4.3"></path>
                            </svg>
                          </div>
                        </div>
                      </div>
                      <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                          <thead class="bg-gray-50 dark:bg-neutral-700">
                            <tr>
                              <th scope="col" class="py-3 px-4 pe-0">
                                <div class="flex items-center h-5">
                                  <input id="hs-table-search-checkbox-all" type="checkbox" class="border-gray-200 rounded-sm text-blue-600 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-500 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                                  <label for="hs-table-search-checkbox-all" class="sr-only">Checkbox</label>
                                </div>
                              </th>
                              <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Equipment</th>
                              <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Make</th>
                              <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Model</th>
                              <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Calibration Due</th>
                              <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500"></th>
                            </tr>
                          </thead>
                          <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @foreach ($equipment as $item)
                                <tr>
                                <td class="py-3 ps-4">
                                    <div class="flex items-center h-5">
                                    <input id="hs-table-search-checkbox-1" type="checkbox" class="border-gray-200 rounded-sm text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                                    <label for="hs-table-search-checkbox-1" class="sr-only">Checkbox</label>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $item->equipment_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $item->make }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $item->model }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ \Carbon\Carbon::parse($item->calibrationDue)->format('F j, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-blue-600 hover:text-blue-800 focus:outline-hidden focus:text-blue-800 disabled:opacity-50 disabled:pointer-events-none dark:text-blue-500 dark:hover:text-blue-400 dark:focus:text-blue-400">Print</button>
                                </td>
                                </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        @endif
    </div>
</div>
