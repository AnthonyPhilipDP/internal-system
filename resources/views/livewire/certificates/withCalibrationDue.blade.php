<div style="font-family: 'Times New Roman', Times, serif;">
    @foreach($equipmentData as $equipment)
        <div class="relative w-[8.5in] h-[11in] bg-cover bg-no-repeat mx-auto  page-break border">
            <div class="absolute top-[45px] inset-0 flex flex-col items-center tracking-normal -space-y-1">
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
            <div class="absolute top-[180px] inset-0 left-16 right-16 h-0">
                <hr class="border border-gray-800 mb-[1px]">
                <hr class="border border-red-600">
            </div>
            <div class="absolute top-[195px] w-full">
                <p class="text-3xl font-bold italic text-center">
                  CERTIFICATE of CALIBRATION
                </p>
              </div>              
            <div class="absolute top-[54px] left-16 w-28 h-28">
                <img src="{{ asset('images/PMSi Logo(transparent).png') }}" alt="PMSi Logo">
            </div>
            <div class="flex flex-col pt-[240px] px-12 space-y-2">
                <div class="flex flex-col text-justify">
                    <div class="flex">
                        <p class="w-[85%] font-semibold uppercase text-sm text-right mr-[10px]">Certificate #: </p>
                        <p class="text-sm font-semibold">40-{{ $equipment['transaction_id'] }}</p>
                    </div>
                    <div class="flex">
                        <p class="w-[25%] font-semibold text-sm text-right mr-[20px]">Client Name: </p>
                        <p class="w-[75%] text-sm">{{ $equipment['customer_name'] }}</p>
                    </div>
                    <div class="flex">
                        <p class="w-[25%] text-sm text-right mr-[20px]">Address: </p>
                        <p class="w-[75%] text-sm">{{ $equipment['customer_address'] }}</p>
                    </div>
                </div>
                <h1 class="
                border border-none
                font-bold text-center uppercase
                bg-gray-200
                mx-48
                ">
                    Instrument Information
                </h1>
                <div class="flex flex-col">
                    <div class="flex">
                        <p class="w-[25%] font-semibold uppercase text-sm text-right mr-[20px]">Equipment ID:</p>
                        <p class="w-[75%] text-sm">{{ $equipment['equipment_id'] }}</p>
                    </div>
                    <div class="flex">
                        <p class="w-[25%] font-semibold uppercase text-sm text-right mr-[20px]">Make:</p>
                        <p class="w-[75%] text-sm">{{ $equipment['make'] }}</p>
                    </div>
                    <div class="flex">
                        <p class="w-[25%] font-semibold uppercase text-sm text-right mr-[20px]">Model:</p>
                        <p class="w-[75%] text-sm">{{ $equipment['model'] }}</p>
                    </div>
                    <div class="flex">
                        <p class="w-[25%] font-semibold uppercase text-sm text-right mr-[20px]">Description:</p>
                        <p class="w-[75%] text-sm">{{ $equipment['description'] }}</p>
                    </div>
                    <div class="flex">
                        <p class="w-[25%] font-semibold uppercase text-sm text-right mr-[20px]">Serial No.:</p>
                        <p class="w-[75%] text-sm">{{ $equipment['serial'] }}</p>
                    </div>
                </div>
                <h1 class="
                border border-none
                font-bold text-center uppercase
                bg-gray-200
                mx-48
                ">
                    Calibration Information
                </h1>
                <div class="flex flex-col">
                    <div class="flex justify-between">
                        <div class="flex w-[50%]">
                            <p class="w-[48%] text-sm text-right mr-[20px]">Received Date: </p>
                            <p class="text-sm">{{ date('d-M-Y', strtotime($equipment['inDate'])) }}</p>
                        </div>
                        <div class="flex w-[48%]">
                            <p class="w-[45%] text-sm text-right mr-[20px]">Temperature: </p>
                            <p class="text-sm">{{ $equipment['temperature'] }}</p>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <div class="flex w-[50%]">
                            <p class="w-[48%] text-sm text-right mr-[20px]">CAL Date: </p>
                            <p class="text-sm">{{ date('d-M-Y', strtotime($equipment['calibrationDate'])) }}</p>
                        </div>
                        <div class="flex w-[48%]">
                            <p class="w-[45%] text-sm text-right mr-[20px]">Humidity: </p>
                            <p class="text-sm">{{ $equipment['humidity'] }}</p>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <div class="flex w-[50%]">
                            <p class="w-[48%] text-sm text-right mr-[20px]">Client Req. CAL DUE: </p>
                            <p class="text-sm">{{ date('d-M-Y', strtotime($equipment['calibrationDue'])) }}</p>
                        </div>
                        <div class="flex w-[48%]">
                            <p class="w-[45%] text-sm text-right mr-[20px]">Validation: </p>
                            <p class="text-sm">{{ $equipment['validation'] }}</p>
                        </div>
                    </div>
                    <div class="flex">
                        <p class="w-[25%] text-sm text-right mr-[20px]">CAL Procedure: </p>
                        <p class="w-[75%] text-sm">{{ $equipment['calibrationProcedure'] }}</p>
                    </div>
                </div>
                <h1 class="
                border border-none
                font-bold text-center uppercase
                bg-gray-200
                mx-48
                ">
                    Condition of Instrument
                </h1>
                @php
                    // Define a mapping for the inCondition values
                    $inCondition = [
                        'asFound' => 'As Found',
                        'inTolerance' => 'In Tolerance',
                        'outOfTolerance' => 'Out of Tolerance',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'damaged' => 'Damaged',
                        'rejected' => 'Rejected',
                        'returned' => 'Returned',
                        'defective' => 'Defective',
                        'inoperative' => 'Inoperative',
                        'malfunctioning' => 'Malfunctioning',
                        'brokenDisplay' => 'Broken Display',
                        'calibrated' => 'Calibrated',
                        'forRepair' => 'For Repair',
                        'forEvaluation' => 'For Evaluation',
                        'initialCalibration' => 'Initial Calibration',
                        'limitedCalibration' => 'Limited Calibration',
                        'overdueCalibration' => 'Overdue Calibration',
                        'referToReport' => 'Refer to Report',
                        'seeRemarks' => 'See Remarks',
                    ];
                    // Define a mapping for the outCondition values
                    $outCondition = [
                        'asLeft' => 'As Left',
                        'limitedCalibration' => 'Limited Calibration',
                        'inTolerance' => 'In Tolerance',
                        'outOfTolerance' => 'Out of Tolerance',
                        'pullOut' => 'Pull Out',
                        'brokenDisplay' => 'Broken Display',
                        'calBeforeUse' => 'Calibrated Before Use',
                        'conditionalCal' => 'Conditional Calibration',
                        'defective' => 'Defective',
                        'disposed' => 'Disposed',
                        'ejected' => 'Ejected',
                        'evaluation' => 'Evaluation',
                        'verification' => 'Verification',
                        'forReference' => 'For Reference',
                        'forRepair' => 'For Repair',
                        'forSale' => 'For Sale',
                        'forSpareParts' => 'For SpParts',
                        'inoperative' => 'Inoperative',
                        'missing' => 'Missing',
                        'operational' => 'Operational',
                        'noCapability' => 'Rejected - No Capability',
                        'returned' => 'Rejected - Returned',
                        'disposed' => 'Rejected - Disposed',
                        'referToReport' => 'Refer to Report',
                        'seeRemarks' => 'See Remarks',
                    ];
                @endphp
                <div class="flex flex-col items-center">
                    <div class="flex w-full justify-center">
                        <p class="w-[50%] font-semibold uppercase text-sm text-right mr-[20px]">Condition In: </p>
                        <p class="w-[50%] text-sm">{{ $inCondition[$equipment['inCondition']] ?? '' }}</p>
                    </div>
                    <div class="flex w-full justify-center">
                        <p class="w-[50%] font-semibold uppercase text-sm text-right mr-[20px]">Condition Out: </p>
                        <p class="w-[50%] text-sm">{{ $outCondition[$equipment['outCondition']] ?? '' }}</p>
                    </div>
                </div>
                <div class="absolute bottom-[170px] left-16 right-16">
                    <p class="text-xs text-gray-800 text-justify pt-2">
                        Precision Measurement Specialists, inc. (PMSi) certifies that the instrument specified above was calibrated according to PMSi's Management System which is intended to meet PNS ISO/IEC 17025:2017.
                        This instrument was calibrated with working standards whose measurement results are traceable to either the National Institute of Standards and Technology (NIST, USA), to National Metrology Laboratory of the Philippines (NML), 
                        to other international accredited metrology labs, to NIST accepted intrinsic standards of measurement, to fundamental or physical constants, or by comparison or consensus standards.
                        Calibration data or report is issued under separate cover that includes specific or applicable traceability.
                    </p>
                    <p class="text-xs text-gray-800 text-justify pt-2">
                        Precision Measurement Specialists, inc. maintains traceability records on all instruments under contract. Records are on file, available for inspection by authorized representatives of the contracting
                        party and/or representatives of the cognizant government agency.
                    </p>
                    <p class="text-[8.5px] text-justify uppercase italic font-bold pt-2">
                        This certificate and report:
                    </p>
                    <div class="grid grid-cols-1 text-xs uppercase italic font-bold">
                        <div class="flex">
                            <p class="pl-[140px] text-[8.5px]">May not be published or reproduced except in full without written authorization from PMS<span class="lowercase">i</span></p>
                        </div>
                        <div class="flex">
                            <p class="pl-[140px] text-[8.5px]">May not be used to claim endorsement by PMS<span class="lowercase">i</span> and by accrediting or traceability organizations mentioned</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-[160px] left-16 right-16 h-0">
                <hr class="border border-gray-800 mb-[1px]">
                <hr class="border border-red-600">
            </div>
            <div class="absolute bottom-[130px] w-full">
                <h1 class="text-center text-xs border border-gray-800 mx-64">Certificate not valid without embossed dry seal.</h1>
            </div>
            <div class="absolute bottom-[75px] left-0 w-full flex justify-around px-12">
                <div class="flex w-full justify-around">
                    <div class="flex flex-col items-center">
                        <p class="text-xs font-normal text-black">&emsp;</p>
                        <p class="text-xs font-normal text-black overline">&emsp;Calibrated by:&emsp;</p>
                    </div>
                    <div class="flex flex-col items-start">
                    </div>
                    <div class="flex flex-col items-center">
                        <p class="text-sm font-normal text-black">{{ now()->format('d-M-Y') }}</p>
                        <p class="text-xs font-normal text-black overline">&emsp;&emsp;&emsp;Date&emsp;&emsp;&emsp;</p>
                    </div>
                    <div class="flex flex-col items-start">
                    </div>
                    <div class="flex flex-col items-center">
                        <p class="text-xs font-normal text-black">&emsp;</p>
                        <p class="text-xs font-normal text-black overline">&emsp;Reviewed by: J. Tenorio&emsp;</p>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-14 left-16 text-left text-[11px] font-normal text-black">
                DCN 5-5.10.2-3 rev.3
            </div>
            <div class="absolute bottom-14 right-16 text-right text-[11px] font-normal text-black">
                Page 1 of 1 
            </div>
        </div>
    @endforeach
    <style>
        @media print {
            @page {
                size: Letter portrait;
                margin: 0;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</div>

{{-- <script>
    window.onload = function() {
        window.print();
    };
</script> --}}