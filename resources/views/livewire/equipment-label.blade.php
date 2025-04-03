<div style="font-family: 'Space Grotesk', sans-serif;">
    @foreach($equipmentDataChunks as $chunkIndex => $chunk)
        <div class="relative container mx-4 py-4 w-[8.5in] h-[11in]">
            <div class="grid grid-cols-3 gap-x-[0.2in] gap-y-[0.1in]">
                @foreach($chunk as $equipment)
                    <div class="pt-8 w-[2.7in] h-[1.5in] bg-white rounded-sm px-3 border border-gray-300 relative" style="background-image: url('{{ asset('images/Label BG.png') }}'); background-repeat: no-repeat; background-size: contain; background-position: center center;">
                        <div class="relative flex flex-col text-[9px] p-1 text-gray-700">
                            <div class="flex">
                                <span class="w-3/10 text-gray-800">Client Name</span>
                                <span class="w-1/10">-</span>
                                <span class="w-6/10 capitalize" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; white-space: normal;">
                                    {{ $equipment['customer_name'] }}
                                </span>
                            </div>
                            <div class="flex">
                                <span class="w-3/10 text-gray-800">Equipment ID</span>
                                <span class="w-1/10">-</span>
                                <span class="w-6/10 truncate">{{ $equipment['equipment_id'] }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-3/10 text-gray-800">Certificate No</span>
                                <span class="w-1/10">-</span>
                                <span class="w-6/10 truncate">40-{{ $equipment['id'] }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-3/10 text-gray-800">Date Received</span>
                                <span class="w-1/10">-</span>
                                <span class="w-6/10 truncate">{{ date('F d, Y', strtotime($equipment['inDate'])) }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-3/10 text-gray-800">Accessory</span>
                                <span class="w-1/10">-</span>
                                <span class="w-6/10 truncate">{{ $equipment['has_accessory'] ? 'Included' : 'None' }}</span>
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 px-4">
                            <hr class="border-t-[0.5px] border-gray-700">
                            <div class="flex text-[6px] justify-around font-bold text-gray-700">
                                <span>info@pmsi-cal.com</span>
                                <span>(0997) 410-6031</span>
                                <span>(046) 889-0673</span>
                            </div>
                            <hr class="border-t-[0.5px] border-gray-700">
                            <p class="text-[6px] mt-[0.5px] mb-1 font-bold text-gray-800">DCN 5-5.8.2-1 rev.1</p>
                        </div>
                        <img src="{{ asset('storage/qrcodes/equipment_' . $equipment['id'] . '.png') }}" alt="Your Image" class="absolute bottom-7 right-4 w-9 h-9">
                    </div>
                @endforeach
            </div>
            <div class="text-[10px] text-gray-500 text-center mt-2">
                Page {{ $chunkIndex + 1 }} of {{ $equipmentDataChunks->count() }}
            </div>
        </div>
    @endforeach
    <style>
        @media print {
            @page {
                size: Letter portrait; /* Set the page size and orientation */
            }
        }
    </style>
</div>

<script>
    window.onload = function() {
        window.print();
    };
</script>