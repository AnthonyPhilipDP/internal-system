<div class="relative w-[8.5in] h-[11in] bg-cover bg-no-repeat pt-45 mx-auto px-20" style="background-image: url('{{ asset('images/templates/ar.jpg') }}');">
    @if ($equipment && $equipment->count())
        <!-- Display customer and equipment details -->
        <div class="mb-4">
            <div class="grid grid-cols-2 gap-2">
                <p class="text-xs font-bold text-gray-700 underline">{{ $equipment->first()->customer->name }}</p>
                <p class="text-xs font-semibold text-gray-700 pl-28">DR Number: 401-{{ $equipment->first()->ar_id }}</p>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-2">
                <p class="text-xs font-semibold text-gray-700">B1 L3 Macaria Business Center, Governor’s Dr., Carmona, 4116</p>
                <p class="text-xs font-semibold text-gray-700 pl-28">{{ $equipment->first()->created_at->format('F j, Y, g:i a') }}</p>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-2">
                @if (!empty($equipment->first()->customer->phone))
                    <p class="text-xs font-semibold text-gray-700">Phone: {{ $equipment->first()->customer->phone }}</p>
                @endif
                @if (!empty($equipment->first()->customer->landline))
                    <p class="text-xs font-semibold text-gray-700 pl-28">Landline: {{ $equipment->first()->customer->landline }}</p>
                @endif
            </div>
        </div>

        <!-- Table Title -->
        <div class="text-lg font-bold text-gray-800 mb-4 text-center uppercase pt-4">
            Acknowledgment Receipt
        </div>

        <div class="border-b border-white rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-white table-auto">
                @if ($equipment->count() == 1)
                    <caption class="caption-bottom text-sm text-gray-500 font-mono shadow">
                        Total number of equipment: {{ $equipment->count() }}
                    </caption>
                @else
                    <caption class="caption-bottom text-sm text-gray-500 font-mono">
                        Total number of equipments: {{ $equipment->count() }}
                    </caption>
                @endif
                <thead class="bg-gray-700 text-center">
                    <tr>
                        <th scope="col" class="px-6 py-3  text-[11px] font-medium text-white uppercase tracking-wider w-1/6">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3  text-[11px] font-medium text-white uppercase tracking-wider w-1/6">
                            Make
                        </th>
                        <th scope="col" class="px-6 py-3  text-[11px] font-medium text-white uppercase tracking-wider w-1/6">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3  text-[11px] font-medium text-white uppercase tracking-wider w-1/6">
                            Serial
                        </th>
                        <th scope="col" class="px-6 py-3  text-[11px] font-medium text-white uppercase tracking-wider w-1/6">
                            Inspection
                        </th>
                        <th scope="col" class="px-6 py-3  text-[11px] font-medium text-white uppercase tracking-wider w-1/6">
                            Accessories
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-center">
                    @foreach ($equipment as $record)
                        <tr>
                            <td class="px-2 py-2 text-xs text-gray-800 w-1/7 truncate max-w-1">
                                {{ $record->id }}
                            </td>
                            <td class="px-2 py-4 text-xs text-gray-800 w-2/7 truncate max-w-1">
                                {{ $record->manufacturer }}
                            </td>
                            <td class="px-2 py-4 text-xs text-gray-800 w-2/7 truncate max-w-1">
                                Autoranging Multimeter
                            </td>
                            <td class="px-2 py-4 text-xs text-gray-800 w-2/7 truncate max-w-1">
                                {{ $record->serial }}
                            </td>
                            <td class="px-2 py-4 text-xs text-gray-800 capitalize w-1/7 truncate">
                                @if(is_array($record->inspection))
                                    {!! implode('<br>', $record->inspection) !!}
                                @else
                                    {{ $record->inspection }}
                                @endif
                            </td>
                            <td class="px-2 py-4 text-xs text-gray-800 w-1/7 truncate max-w-1">
                                @if($record->accessory->isNotEmpty())
                                    {!! implode('<br>', $record->accessory->pluck('name')->toArray()) !!}
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
        <div class="absolute bottom-12 left-0 w-full flex justify-start px-20">
            <div class="flex w-full justify-between">
                <div class="flex flex-col items-start gap-8">
                    <p class="text-xs font-semibold text-gray-700">Delivered By:</p>
                    <p class="text-sm font-semibold text-gray-700 uppercase underline">Philip Pudan</p>
                </div>
                <div class="flex flex-col items-end gap-8">
                    <p class="text-xs font-semibold text-gray-700">Received By:</p>
                    <p class="text-sm font-semibold text-gray-700 uppercase underline">Rose Ambata</p>
                </div>
            </div>
        </div>

    @else
        <p class="text-center text-gray-800">No equipment data found.</p>
    @endif
</div>