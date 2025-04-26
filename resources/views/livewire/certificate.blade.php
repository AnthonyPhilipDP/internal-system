<div style="font-family: 'Times New Roman', Times, serif;">
    @foreach($equipmentData as $equipment)
        <div class="relative w-[8.5in] h-[11in] bg-cover bg-no-repeat mx-auto page-break" style="background-image: url('{{ asset('images/templates/CalibrationCertificate.jpg') }}');">
            <div class="flex flex-col pt-[240px] px-12 space-y-2">
                <div class="flex flex-col">
                    <div class="flex">
                        <p class="w-[85%] font-semibold uppercase text-sm text-right mr-[10px]">Certificate No.: </p>
                        <p class="text-sm font-semibold">40-{{ $equipment['transaction_id'] }}</p>
                    </div>
                    <div class="flex">
                        <p class="w-[25%] font-semibold uppercase text-sm text-right mr-[20px]">Client Name: </p>
                        <p class="w-[75%] text-sm">{{ $equipment['customer_name'] }}</p>
                    </div>
                    <div class="flex">
                        <p class="w-[25%] font-semibold uppercase text-sm text-right mr-[20px]">Client Address: </p>
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
                        <p class="w-[25%] font-semibold uppercase text-sm text-right mr-[20px]">Serial:</p>
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
                            <p class="w-[48%] font-semibold uppercase text-sm text-right mr-[20px]">Date Received: </p>
                            <p class="text-sm">{{ date('F d, Y', strtotime($equipment['inDate'])) }}</p>
                        </div>
                        <div class="flex w-[48%]">
                            <p class="w-[45%] font-semibold uppercase text-sm text-right mr-[20px]">Temperature: </p>
                            <p class="text-sm">{{ $equipment['temperature'] }}</p>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <div class="flex w-[50%]">
                            <p class="w-[48%] font-semibold uppercase text-sm text-right mr-[20px]">Cal Date: </p>
                            <p class="text-sm">{{ date('F d, Y', strtotime($equipment['calibrationDate'])) }}</p>
                        </div>
                        <div class="flex w-[48%]">
                            <p class="w-[45%] font-semibold uppercase text-sm text-right mr-[20px]">Humidity: </p>
                            <p class="text-sm">{{ $equipment['humidity'] }}</p>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <div class="flex w-[50%]">
                            <p class="w-[48%] font-semibold uppercase text-sm text-right mr-[20px]">Client Req. CAL DUE: </p>
                            <p class="text-sm">{{ date('F d, Y', strtotime($equipment['calibrationDue'])) }}</p>
                        </div>
                        <div class="flex w-[48%]">
                            <p class="w-[45%] font-semibold uppercase text-sm text-right mr-[20px]">Validation: </p>
                            <p class="text-sm">{{ $equipment['validation'] }}</p>
                        </div>
                    </div>
                    <div class="flex">
                        <p class="w-[25%] font-semibold uppercase text-sm text-right mr-[20px]">CAL Procedure: </p>
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
                <div class="flex flex-col items-center">
                    <div class="flex w-full justify-center">
                        <p class="w-[50%] font-semibold uppercase text-sm text-right mr-[20px]">Condition In: </p>
                        <p class="w-[50%] text-sm">{{ $equipment['inCondition'] }}</p>
                    </div>
                    <div class="flex w-full justify-center">
                        <p class="w-[50%] font-semibold uppercase text-sm text-right mr-[20px]">Condition Out: </p>
                        <p class="w-[50%] text-sm">{{ $equipment['outCondition'] }}</p>
                    </div>
                </div>
                <p class="text-xs text-justify pt-2">
                    Precision Measurement Specialists, inc. (PMSi) certifies that the instrument specified above was calibrated according to PMSi's Management System which is intended to meet PNS ISO/IEC 17025:2017.
                    This instrument was calibrated with working standards whose measurement results are traceable to either the National Institute of Standards and Technology (NIST, USA), to Philippine ITDI-NML
                    (National Metrology Lab), to other international accredited metrology labs, to NIST accepted intrinsic standards of measurement, to fundamental or phtsical constants, or by comparison or consensus standards.
                    Calibration data or report is issued under separate cover that includes specific or applicable traceability
                </p>
                <p class="text-xs text-justify pt-2">
                    Precision Measurement Specialists, inc. maintains traceability records on all instruments under contract. Records are on file, available for inspection by authorized representatives of the contracting
                    party and/or representatives of the cognizant government agency.
                </p>
                <p class="text-[9px] text-justify uppercase italic font-semibold">
                    This certificate and report:
                </p>
            </div>
            <div class="grid grid-cols-1 text-xs uppercase italic font-semibold">
                <div class="flex">
                    <p class="pl-48 text-[9px]">May not be published or reproduced except in full without written authorization from PMSi</p>
                </div>
                <div class="flex">
                    <p class="pl-48 text-[9px]">May not be used to claim endorsement by PMSi and by accrediting or traceability organizations mentioned</p>
                </div>
            </div>
            <div class="absolute bottom-16 left-0 w-full flex justify-around px-12">
                <div class="flex w-full justify-around">
                    <div class="flex flex-col items-center">
                        <p class="text-[11px] font-semibold text-gray-700">&emsp;</p>
                        <p class="text-[11px] font-semibold text-gray-700 overline">Calibrated By:</p>
                    </div>
                    <div class="flex flex-col items-start">
                    </div>
                    <div class="flex flex-col items-center">
                        <p class="text-[11px] font-semibold text-gray-700">{{ now()->format('F j, Y') }}</p>
                        <p class="text-[11px] font-semibold text-gray-700 overline">&emsp;&emsp;&emsp;Date&emsp;&emsp;&emsp;</p>
                    </div>
                    <div class="flex flex-col items-start">
                    </div>
                    <div class="flex flex-col items-center">
                        <p class="text-[11px] font-semibold text-gray-700">&emsp;</p>
                        <p class="text-[11px] font-semibold text-gray-700 overline">Reviewed By: {{ Auth::user()->name }}</p>
                    </div>
                </div>
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