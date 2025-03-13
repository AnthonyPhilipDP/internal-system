<div class="relative w-[11in] h-[8.5in] bg-cover bg-no-repeat pt-45 mx-auto px-12" style="background-image: url('{{ asset('images/templates/AcknowledgmentReceipt - Landscape.jpg') }}');">
    <hr class="mb-4 mt-[-18px] border-t-2 border-gray-700">
    @if ($equipment && $equipment->count())
        <!-- Display customer and equipment details -->
        <div class="mb-4">
            <div class="grid grid-cols-2 gap-2">
                <p class="text-xs font-bold text-gray-700 underline">{{ $equipment->first()->customer->name }}</p>
                <p class="text-xs font-semibold text-gray-700 pl-36">DR Number: 401-{{ $equipment->first()->ar_id }}</p>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-2">
                <p class="text-xs font-semibold text-gray-700">B1 L3 Macaria Business Center, Governor’s Dr., Carmona, 4116, Cavite, Philippines</p>
                <p class="text-xs font-semibold text-gray-700 pl-36">{{ $equipment->first()->created_at->format('F j, Y, g:i a') }}</p>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-2">
                @if (!is_null($equipment->first()->customer->phone) && $equipment->first()->customer->phone !== 'N/A')
                    <p class="text-xs font-semibold text-gray-700">Phone: {{ $equipment->first()->customer->phone }}</p>
                @endif
                @if (!is_null($equipment->first()->customer->landline) && $equipment->first()->customer->landline !== 'N/A')
                    <p class="text-xs font-semibold text-gray-700 pl-36">Landline: {{ $equipment->first()->customer->landline }}</p>
                @endif
            </div>
        </div>

        <!-- Table Title -->
        <div class="text-lg font-bold text-gray-800 mb-4 text-center uppercase">
            Acknowledgment Receipt
        </div>

        <div class="border-b border-white rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-white table-auto">
                @if ($equipment->count() == 1)
                    <caption class="caption-bottom text-xs text-gray-500 font-mono shadow">
                        Total number of equipment: {{ $equipment->count() }}
                    </caption>
                @else
                    <caption class="caption-bottom text-xs text-gray-500 font-mono">
                        Total number of equipments: {{ $equipment->count() }}
                    </caption>
                @endif
                <thead class="bg-gray-700 text-center">
                    <tr>
                        <th scope="col" class="px-2 py-1 text-[10px] font-medium text-white uppercase tracking-wider w-1/7">
                            Transaction
                        </th>
                        <th scope="col" class="px-2 py-1 text-[10px] font-medium text-white uppercase tracking-wider w-1/7">
                            Equipment ID
                        </th>
                        <th scope="col" class="px-2 py-1 text-[10px] font-medium text-white uppercase tracking-wider w-1/7">
                            Make
                        </th>
                        <th scope="col" class="px-2 py-1 text-[10px] font-medium text-white uppercase tracking-wider w-1/7">
                            Description
                        </th>
                        <th scope="col" class="px-2 py-1 text-[10px] font-medium text-white uppercase tracking-wider w-1/7">
                            Serial
                        </th>
                        <th scope="col" class="px-2 py-1 text-[10px] font-medium text-white uppercase tracking-wider w-1/7">
                            Inspection
                        </th>
                        <th scope="col" class="px-7 py-1 text-[10px] font-medium text-white uppercase tracking-wider w-1/7">
                            Accessories
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-center">
                    @foreach ($equipment as $record)
                        <tr>
                            <td class="px-1 py-0.5 text-[10px] text-gray-800 w-1/14 break-words">
                                {{ $record->id }}
                            </td>
                            <td class="px-1 py-0.5 text-[10px] text-gray-800 w-3/14 break-words">
                                {{ $record->equipment_id }}
                            </td>
                            <td class="px-1 py-0.5 text-[10px] text-gray-800 w-3/14 break-words">
                                {{ $record->manufacturer }}
                            </td>
                            <td class="px-1 py-0.5 text-[10px] text-gray-800 w-4/14 break-words">
                                {{ $record->description }}
                            </td>
                            <td class="px-1 py-0.5 text-[10px] text-gray-800 w-3/14 break-words">
                                {{ $record->serial }}
                            </td>
                            <td class="px-1 py-0.5 text-[10px] text-gray-800 capitalize w-1/14">
                                @if(is_array($record->inspection))
                                    {!! implode('<br>', $record->inspection) !!}
                                @else
                                    {{ $record->inspection }}
                                @endif
                            </td>
                            <td class="px-1 py-0.5 text-[10px] text-gray-800 w-1/14 break-words capitalize">
                                @if(isset($record->accessory) && $record->accessory->pluck('name')->filter()->isNotEmpty())
                                    {!! implode(', ', $record->accessory->pluck('name')->toArray()) !!}
                                @else
                                    <span class="text-red-600">None</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Static positioned section -->
        @php
            $deliveryRider = session('name');
            $currentUser = Auth::user()->name;
        @endphp
        <div class="absolute bottom-12 left-0 w-full flex justify-start px-12">
            <div class="flex w-full justify-between">
                <div class="flex flex-col items-start gap-8">
                    <p class="text-xs font-semibold text-gray-700">Delivered By:</p>
                    <p class="text-sm font-semibold text-gray-700 uppercase underline">{{ $deliveryRider }}</p>
                </div>
                <div class="flex flex-col items-end gap-8">
                    <p class="text-xs font-semibold text-gray-700">Received By:</p>
                    <p class="text-sm font-semibold text-gray-700 uppercase underline">{{ $currentUser }}</p>
                </div>
            </div>
        </div>

    @else
        <p class="text-center text-gray-800">No equipment data found.</p>
    @endif
</div>

<script>
    window.onload = function() {
        window.print();
    };
</script>