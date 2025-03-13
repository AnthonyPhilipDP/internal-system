<div>
    @foreach ($equipmentChunks as $chunkIndex => $equipmentChunk)
        <div class="relative w-[11in] h-[8.5in] bg-cover bg-no-repeat pt-45 mx-auto px-12" style="background-image: url('{{ asset('images/templates/AcknowledgmentReceipt - Landscape.jpg') }}');">
            <hr class="mb-4 mt-[-18px] border-t-2 border-gray-700">
            <!-- Display customer and equipment details -->
            <div class="mb-4">
                <div class="grid grid-cols-2 gap-2">
                    <p class="text-xs font-bold text-gray-700 underline">{{ $equipmentChunk->first()->customer->name }}</p>
                    <p class="text-xs font-semibold text-gray-700 pl-36">DR Number: 401-{{ $equipmentChunk->first()->ar_id }}</p>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <p class="text-xs font-semibold text-gray-700">{{ $equipmentChunk->first()->customer->address }}</p>
                    <p class="text-xs font-semibold text-gray-700 pl-36">{{ $equipmentChunk->first()->created_at->format('F d, Y g:i A') }}</p>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    @if (!is_null($equipmentChunk->first()->customer->phone) && $equipmentChunk->first()->customer->phone !== 'N/A' 
                        && $equipmentChunk->first()->customer->phone !== '' && $equipmentChunk->first()->customer->phone !== 'n/a')
                        <p class="text-xs font-semibold text-gray-700">Phone: {{ $equipmentChunk->first()->customer->phone }}</p>
                    @endif
                    @if (!is_null($equipmentChunk->first()->customer->landline) && $equipmentChunk->first()->customer->landline !== 'N/A' 
                        && $equipmentChunk->first()->customer->landline !== '' && $equipmentChunk->first()->customer->landline !== 'n/a')
                        <p class="text-xs font-semibold text-gray-700 pl-36">Landline: {{ $equipmentChunk->first()->customer->landline }}</p>
                    @endif
                </div>
            </div>

            <!-- Table Title -->
            <div class="text-lg font-bold text-gray-800 mb-4 text-center uppercase">
                Acknowledgment Receipt
            </div>

            <div class="border-b border-white rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-white table-auto">
                    @if ($equipmentChunks->count() > 1)
                        <caption class="caption-bottom text-xs text-gray-500 font-mono">
                            Number of equipment in this page: {{ $equipmentChunk->count() }}
                        </caption>
                    @endif
                    <thead class="bg-gray-700 text-center">
                        <tr>
                            <th scope="col" class="px-7 py-1 text-[10px] font-medium text-white uppercase tracking-wider w-1/14">
                                Transaction
                            </th>
                            <th scope="col" class="px-2 py-1 text-[10px] font-medium text-white uppercase tracking-wider w-2/14">
                                Equipment ID
                            </th>
                            <th scope="col" class="px-2 py-1 text-[10px] font-medium text-white uppercase tracking-wider w-3/14">
                                Make
                            </th>
                            <th scope="col" class="px-2 py-1 text-[10px] font-medium text-white uppercase tracking-wider w-3/14">
                                Description
                            </th>
                            <th scope="col" class="px-2 py-1 text-[10px] font-medium text-white uppercase tracking-wider w-3/14">
                                Serial
                            </th>
                            <th scope="col" class="px-2 py-1 text-[10px] font-medium text-white uppercase tracking-wider w-3/14">
                                Inspection
                            </th>
                            <th scope="col" class="px-7 py-1 text-[10px] font-medium text-white uppercase tracking-wider w-1/14">
                                Accessories
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-center">
                        @foreach ($equipmentChunk as $record)
                            <tr>
                                <td class="px-1 py-0.5 text-[10px] text-gray-800  break-words">
                                    {{ $record->id }}
                                </td>
                                <td class="px-1 py-0.5 text-[10px] text-gray-800  break-words">
                                    {{ $record->equipment_id }}
                                </td>
                                <td class="px-1 py-0.5 text-[10px] text-gray-800  break-words">
                                    {{ $record->manufacturer }}
                                </td>
                                <td class="px-1 py-0.5 text-[10px] text-gray-800  break-words">
                                    {{ $record->description }}
                                </td>
                                <td class="px-1 py-0.5 text-[10px] text-gray-800  break-words">
                                    {{ $record->serial }}
                                </td>
                                <td class="px-1 py-0.5 text-[10px] text-gray-800 capitalize ">
                                    @if(is_array($record->inspection))
                                        {!! implode(', ', $record->inspection) !!}
                                    @else
                                        {{ $record->inspection }}
                                    @endif
                                </td>
                                <td class="px-1 py-0.5 text-[10px] text-gray-800  break-words capitalize">
                                    @if(isset($record->accessory) && $record->accessory->pluck('name')->filter()->isNotEmpty())
                                        {!! implode(', ', $record->accessory->pluck('name')->toArray()) !!}
                                    @else
                                        <span class="text-yellow-600">No Accessory</span>
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

            <!-- Total number of equipment on the last page -->
            @if ($chunkIndex === $equipmentChunks->count() - 1)
                <div class="text-xs font-semibold text-gray-600 mb-4 text-center uppercase">
                    Total Number of Equipment: {{ $totalEquipmentCount }}
                </div>
            @endif

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

            <!-- Page number in the lower right corner -->
            @if ($equipmentChunks->count() > 1)
                <div class="absolute bottom-4 right-4 text-xs font-semibold text-gray-500">
                    Page {{ $chunkIndex + 1 }}
                </div>
            @endif
        </div>
    @endforeach
</div>

<script>
    window.onload = function() {
        window.print();
    };
</script>