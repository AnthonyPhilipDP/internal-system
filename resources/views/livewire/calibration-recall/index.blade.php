<div>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <button wire:loading.attr="disabled" wire:click="downloadFiles" class="absolute top-4 left-4 bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700">
        <span wire:loading.remove wire:target="downloadFiles">
            <svg class="h-5 w-5 mr-2 inline" viewBox="0 0 24 24">
                {{ svg('css-software-download') }}
            </svg>
        </span>
        <div wire:loading>
            <svg class="animate-spin h-5 w-5 mr-2 inline" viewBox="0 0 24 24">
                {{ svg('css-spinner') }}
            </svg>
        </div>
        <span>Download</span>
    </button>

    @foreach ($customerData as $customerIndex => $customer)
        @php
            // Chunk the equipment into pages of 12 items each
            $equipmentChunks = collect($customer['equipment'])->chunk(9);
        @endphp

        @foreach ($equipmentChunks as $chunkIndex => $equipmentChunk)
            <div class="border relative w-[8.5in] h-[11in] bg-white px-12 print:page-break pt-[180px] bg-cover bg-no-repeat mx-auto">
                <div class="absolute top-[45px] inset-0 flex flex-col items-center tracking-normal -space-y-1" style="font-family: 'Times New Roman', Times, serif;">
                    <div class="flex">
                        <h1 class="text-[26px] text-center font-bold">Precision Measurement Specialists, <span class="italic text-red-500">i</span>nc.</h1>
                    </div>
                    <div class="flex">
                        <p class="text-[12px] text-center font-semibold italic">'a metrology company'</p>
                    </div>
                    <div class="flex">
                        <p class="text-[12px] text-center font-semibold italic">( since 1998 )</p>
                    </div>
                    <div class="flex">
                        <p class="text-[11px] text-center font-semibold mt-1">B1 L3 Macaria Business Center, Governor's Dr., Carmona, Cavite, 4116 Philippines</p>
                    </div>
                    <div class="flex">
                        <p class="text-[11px] text-center font-semibold">Telefax:(046) 889-0673 | Mobile: (0997) 410-6031</p>
                    </div>
                    <div class="flex">
                        <p class="text-[11px] text-center font-semibold">E-mail: info@pmsi-cal.com ~ pmsical@yahoo.com | Website: www.pmsi-cal.com</p>
                    </div>
                </div>
                <div class="absolute top-[54px] left-12 w-28 h-28">
                    <img src="{{ asset('images/PMSi Logo(transparent).png') }}" alt="PMSi Logo">
                </div>
                <!-- Customer Details -->
                <div class="flex justify-between mb-4">
                    <div class="flex flex-col gap-1 w-[60%]">
                        @if (!empty($customer['contact_persons']))
                            @if ( $customer['contact_persons'][0]['identity'] == 'male' )
                                <p class="text-sm font-semibold text-gray-700 truncate capitalize">Mr. {{ $customer['contact_persons'][0]['name'] }}</p>
                            @else
                                <p class="text-sm font-semibold text-gray-700 truncate capitalize">Ms. {{ $customer['contact_persons'][0]['name'] }}</p>
                            @endif
                        @endif
                        <p class="text-sm font-bold text-gray-700 truncate">Client: <span class="uppercase">{{ $customer['name'] }}</span></p>
                        @if (!empty($customer['telephone']))
                            <p class="text-sm font-semibold text-gray-700">
                                Telephone: {{ $customer['telephone'] }}
                            </p>
                        @endif
                        @if (!empty($customer['mobile']))
                            <p class="text-sm font-semibold text-gray-700">
                                Mobile: {{ $customer['mobile']}}
                            </p>
                        @endif
                        @if (!empty($customer['email']))
                            <p class="text-sm font-semibold text-gray-700">Email: {{ $customer['email'] }}</p>
                        @endif
                    </div>
                    <div class="flex flex-col w-[40%] items-end">
                        <div class="items-center">
                            <div class="text-3xl font-bold text-gray-700 text-center">Calibration Recall</div>
                            <div class="mt-4 text-lg text-center font-bold text-gray-800 border border-red-400 bg-yellow-400 py-4 px-12">
                                DUE: {{ \Carbon\Carbon::parse($equipmentChunk->first()['calibrationDue'])->format('F, Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equipment Table -->
                <hr class="border-gray-800 my-2">
                @if ($chunkIndex === 0)
                <p class="text-sm text-gray-800 my-2 text-justify">
                    @if (!empty($customer['contact_persons']))
                            @if ( $customer['contact_persons'][0]['identity'] == 'male' )
                                Dear Sir,
                            @else
                                Dear Madam,
                            @endif
                    @else
                        Dear Sir/Madam:
                    @endif
                        <br><br>
                        Please take note that the following list of <span class="underline italic">equipment will come due</span> for calibration service. Please contact our office at your convenience to schedule calibration service of this equipment.
                </p>
                @endif
                <div class="border-b border-white rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-white table-fixed">
                        @if ($equipmentChunks->count() > 1)
                        <caption class="caption-bottom text-xs text-gray-500 font-mono mt-2">
                            Number of equipment in this page: {{ $equipmentChunk->count() }}
                        </caption>
                        @endif
                        <thead class="bg-gray-700">
                            <tr>
                                <th scope="col" class="py-2 text-[10px] font-medium text-white uppercase tracking-wider max-w-16 min-w-16 pl-4 pr-2 text-left">
                                    Cal Due
                                </th>
                                <th scope="col" class="py-2 text-[10px] font-medium text-white uppercase tracking-wider max-w-16 min-w-16 px-2 text-left">
                                    Equipment ID
                                </th>
                                <th scope="col" class="py-2 text-[10px] font-medium text-white uppercase tracking-wider max-w-16 min-w-16 px-2 text-left">
                                    Make
                                </th>
                                <th scope="col" class="py-2 text-[10px] font-medium text-white uppercase tracking-wider max-w-16 min-w-16 px-2 text-left">
                                    Model
                                </th>
                                <th scope="col" class="py-2 text-[10px] font-medium text-white uppercase tracking-wider max-w-16 min-w-16 px-2 text-left">
                                    Description
                                </th>
                                <th scope="col" class="py-2 text-[10px] font-medium text-white uppercase tracking-wider max-w-16 min-w-16 px-2 text-left">
                                    Serial No.
                                </th>
                                <th scope="col" class="py-2 text-[10px] font-medium text-white uppercase tracking-wider max-w-16 min-w-16 px-2 text-left pl-2 pr-4">
                                    Owner
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-left">
                            @foreach ($equipmentChunk as $equipment)
                                <tr class="items-center">
                                    <td class="py-2 text-[10px] text-gray-800 max-w-16 min-w-16 truncate pl-4 pr-2">
                                        @if ($equipment['calibrationDue'])
                                            {{ \Carbon\Carbon::parse($equipment['calibrationDue'])->format('d-M-Y') }}
                                        @endif
                                    </td>
                                    <td class="py-2 text-[10px] text-gray-800 max-w-16 min-w-16 truncate px-2">
                                        {{ $equipment['equipment_id'] }}
                                    </td>
                                    <td class="py-2 text-[10px] text-gray-800 max-w-16 min-w-16 truncate px-2">
                                        {{ $equipment['make'] }}
                                    </td>
                                    <td class="py-2 text-[10px] text-gray-800 max-w-16 min-w-16 truncate px-2">
                                        {{ $equipment['model'] }}
                                    </td>
                                    <td class="py-2 text-[10px] text-gray-800 max-w-16 min-w-16 truncate px-2">
                                        {{ $equipment['description'] }}
                                    </td>
                                    <td class="py-2 text-[10px] text-gray-800 max-w-16 min-w-16 truncate px-2">
                                        {{ $equipment['serial'] }}
                                    </td>
                                    <td class="py-2 text-[10px] text-gray-800 max-w-16 min-w-16 truncate pl-2 pr-4">
                                        Not Applicable
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($chunkIndex === $equipmentChunks->count() - 1)
                <div>
                    @if (count($customer['equipment']) > 1)
                    <p class="text-xs mt-2 text-gray-700 text-center">
                        Total number of equipments: <span class="font-semibold">{{ count($customer['equipment']) }}</span>
                    </p>
                    @else
                    <p class="text-xs mt-2 text-gray-700 text-center">
                        Total number of equipment: <span class="font-semibold">{{ count($customer['equipment']) }}</span>
                    </p>
                    @endif
                </div>
                <!-- Letter Content -->
                <div class="space-y-2 text-sm text-gray-800 my-2 absolute bottom-12 left-12 right-12 text-justify">
                    <div class="pb-2">
                        We at PMS<span class="text-red-500 italic">i</span> are committed to provide an impartial high-quality calibration, maintenance and repair service of test and measurement equipment. We offer an OEM level of service & expertise at a competitive rate.
                    </div>
                    <div class="pb-2">
                        For other capabilities, questions, or a quote, please contact us at <span class="font-semibold italic">(046) 889-0673</span> or <span class="font-semibold italic">(0997) 410 6031</span>.<br>
                        You may also email us at <span class="font-semibold italic">info@pmsi-cal.com</span> or <span class="font-semibold italic">pmsical@yahoo.com</span>.
                    </div>
                    <div class="pb-4">
                        Please disregard this reminder if recalled equipment has been attended to.
                    </div>
                    <div class="pb-4">
                        Best Regards,
                    </div>
                    <div>
                        <span class="font-semibold">Elvia N. Mendez</span><br>
                        <span class="underline">PMS<span class="text-red-500 italic">i</span></span>
                    </div>
                </div>
                @endif

                <!-- Continued Message -->
                @if ($chunkIndex < $equipmentChunks->count() - 1)
                <div class="text-center text-sm text-gray-500 mt-4">
                    (Continued on the next page)
                </div>
                @endif
                
                <!-- Footer -->
                <hr class="absolute left-12 right-12 bottom-12 border-gray-800 mb-1">
                <div class="absolute bottom-9 left-12 text-left text-[11px] font-[500] text-gray-800" style="font-family: 'Times New Roman', Times, serif;">
                    DCN 5-5.10.4.4-3 rev.1
                </div>
                <div class="absolute bottom-9 right-12 text-right text-[11px] font-[500] text-gray-800" style="font-family: 'Times New Roman', Times, serif;">
                    Page {{ $chunkIndex + 1 }} of {{ $equipmentChunks->count() }}
                </div>
            </div>
        @endforeach
    @endforeach
    <style>
        @media print {
            @page {
                size: Letter portrait;
            }
            .print\:page-break {
                page-break-after: always;
            }
        }
    </style>
    <script>
        document.addEventListener('livewire:init', () => {
            // Success notification
            Livewire.on('download-complete', () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Download started!',
                    showConfirmButton: false,
                    timer: 3500
                });
            });

            // Error notification
            Livewire.on('download-error', (message) => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Download failed: ' + message,
                    showConfirmButton: false,
                    timer: 5000
                });
            });
        });
    </script>
</div>