<div>
    @foreach ($customerData as $customerIndex => $customer)
        @php
            // Chunk the equipment into pages of 8 items each
            $equipmentChunks = collect($customer['equipment'])->chunk(8);
        @endphp

        @foreach ($equipmentChunks as $chunkIndex => $equipmentChunk)
            <div class="relative w-[8.5in] h-[11in] bg-white px-12 print:page-break pt-[180px] bg-cover bg-no-repeat mx-auto" style="background-image: url('{{ asset('images/templates/CalibrationRecall.jpg') }}');">
                <div class="absolute top-[54px] left-12 w-28 h-28">
                    <img src="{{ asset('images/PMSi Logo(transparent).png') }}" alt="PMSi Logo">
                </div>
                <!-- Customer Details -->
                <div class="flex justify-between mb-4">
                    <div class="flex flex-col gap-1 w-[60%]">
                        @if (!empty($customer['contact_persons']))
                            <p class="text-sm font-semibold text-gray-700 truncate capitalize">{{ $customer['contact_persons'][0]['name'] }}</p>
                        @endif
                        <p class="text-sm font-bold text-gray-700 truncate">Client: <span class="uppercase">{{ $customer['name'] }}</span></p>
                        @if (!empty($customer['telephone']))
                            <p class="text-sm font-semibold text-gray-700">Telephone: {{ $customer['telephone'] }}</p>
                        @endif
                        @if (!empty($customer['mobile']))
                            <p class="text-sm font-semibold text-gray-700">Mobile: {{ $customer['mobile'] }}</p>
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
                <p class="text-sm text-gray-800 my-2 text-justify">
                    Dear Sir/Madam:
                    <br><br>
                    Please take note that the following list of <span class="underline italic">equipment will come due</span> for calibration service. Please contact our office at your convenience to schedule calibration service of this equipment.
                </p>
                <div class="border-b border-white rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-white table-auto">
                        @if ($equipmentChunks->count() > 1)
                        <caption class="caption-bottom text-xs text-gray-500 font-mono mt-4">
                            Number of equipment in this page: {{ $equipmentChunk->count() }}
                        </caption>
                    @endif
                        <thead class="bg-gray-700 text-center">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-[10px] font-medium text-white uppercase tracking-wider w-2/14 text-nowrap">
                                    Cal Due
                                </th>
                                <th scope="col" class="px-4 py-2 text-[10px] font-medium text-white uppercase tracking-wider w-2/14 text-nowrap">
                                    Equipment ID
                                </th>
                                <th scope="col" class="px-4 py-2 text-[10px] font-medium text-white uppercase tracking-wider w-2/14 text-nowrap">
                                    Make
                                </th>
                                <th scope="col" class="px-4 py-2 text-[10px] font-medium text-white uppercase tracking-wider w-2/14 text-nowrap">
                                    Model
                                </th>
                                <th scope="col" class="px-4 py-2 text-[10px] font-medium text-white uppercase tracking-wider w-2/14 text-nowrap">
                                    Description
                                </th>
                                <th scope="col" class="px-4 py-2 text-[10px] font-medium text-white uppercase tracking-wider w-2/14 text-nowrap">
                                    Serial
                                </th>
                                <th scope="col" class="px-4 py-2 text-[10px] font-medium text-white uppercase tracking-wider w-2/14 text-nowrap">
                                    Owner
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-left">
                            @foreach ($equipmentChunk as $equipment)
                                <tr class="items-center">
                                    <td class="px-2 py-2 text-[10px] text-gray-800 w-2/14">
                                        @if ($equipment['calibrationDue'])
                                            {{ \Carbon\Carbon::parse($equipment['calibrationDue'])->format('M j, Y') }}
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 text-[10px] text-gray-800 w-2/14">
                                        {{ $equipment['equipment_id'] }}
                                    </td>
                                    <td class="px-2 py-2 text-[10px] text-gray-800 w-2/14">
                                        {{ $equipment['make'] }}
                                    </td>
                                    <td class="px-2 py-2 text-[10px] text-gray-800 w-2/14">
                                        {{ $equipment['model'] }}
                                    </td>
                                    <td class="px-2 py-2 text-[10px] text-gray-800 w-2/14">
                                        {{ $equipment['description'] }}
                                    </td>
                                    <td class="px-2 py-2 text-[10px] text-gray-800 w-2/14">
                                        {{ $equipment['serial'] }}
                                    </td>
                                    <td class="px-2 py-2 text-[10px] text-gray-800 w-2/14">
                                        40-{{ $equipment['transaction_id'] }}
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
                        Total number of equipment in this page: <span class="font-semibold">{{ count($customer['equipment']) }}</span>
                    </p>
                    @else
                    <p class="text-xs mt-2 text-gray-700 text-center">
                        Total number of equipment: <span class="font-semibold">{{ count($customer['equipment']) }}</span>
                    </p>
                    @endif
                </div>
                @endif
                
                <div class="space-y-2 text-sm text-gray-800 my-2 absolute bottom-9 left-12 right-12 text-justify">
                    <div class="pb-2">
                        We at PMS<span class="text-red-500 italic">i</span> are commited to provide an impartial high-quality calibration, maintenance and repair service of test and measurement equipment. We offer an OEM level of service & expertise at a competitive rate.
                    </div>
                    <div class="pb-2">
                        For outher capabilities, questions, or a quote, please contact us at <span class="font-semibold italic">(046) 889-0679</span> or <span class="font-semibold italic">(0997) 410 6031</span>.<br>
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
                <!-- Footer -->
                <div class="absolute bottom-6 left-12 text-left text-[11px] font-sans font-semibold text-gray-800">
                    DCN 5-5.10.2-3 rev.3
                </div>
                <div class="absolute bottom-6 right-12 text-right text-[11px] font-sans font-semibold text-gray-800">
                    Page {{ $chunkIndex + 1 }} of {{ $equipmentChunks->count() }}
                </div>
            </div>
        @endforeach
    @endforeach
    <style>
        @media print {
            @page {
                size: Letter portrait;
                margin: 0.5in;
            }
            .print\:page-break {
                page-break-after: always;
            }
        }
    </style>
</div>
