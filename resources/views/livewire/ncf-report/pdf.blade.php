<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{ $selectedReport->customerName }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body style="margin:0; padding:0;">
  <div>
    <div style="font-family: 'Times New Roman', Times, serif;">
      <div class="relative w-[8.5in] h-[11in] bg-cover bg-no-repeat mx-auto  page-break border">
        <div class="absolute top-[25px] inset-0 flex flex-col items-center tracking-normal -space-y-1">
          <div class="flex">
            <h1 class="text-[26px] text-center font-bold">Precision Measurement Specialists, <span
                class="italic text-red-500">i</span>nc.</h1>
          </div>
          <div class="flex">
            <p class="text-[12px] text-center font-semibold italic">'a metrology company'</p>
          </div>
          <div class="flex">
            <p class="text-[12px] text-center font-semibold italic">( since 1998 )</p>
          </div>
          <div class="flex">
            <p class="text-[11px] text-center font-semibold mt-1">B1 L3 Macaria Business Center, Governor's Dr.,
              Carmona,
              Cavite, 4116 Philippines</p>
          </div>
          <div class="flex">
            <p class="text-[11px] text-center font-semibold">Telefax:(046) 889-0673 | Mobile: (0997) 410-6031</p>
          </div>
          <div class="flex">
            <p class="text-[11px] text-center font-semibold">E-mail: info@pmsi-cal.com ~ pmsical@yahoo.com | Website:
              www.pmsi-cal.com</p>
          </div>
        </div>
        <div class="absolute top-[150px] w-full">
          <p class="text-2xl font-bold italic text-center">
            Equipment Nonconformity Finding
          </p>
        </div>
        <div class="absolute top-[34px] left-16 w-28 h-28">
          <img src="https://i.ibb.co/fdh0S6YF/pmsi-logo-copyright.png" alt="PMSi Logo (copyright)">
        </div>
        {{-- Content Starts Here --}}
        @php
          $selections = [
              'action1' => 'Attempt Realignment',
              'action2' => 'Attempt Troubleshooting',
              'action3' => 'Limit Instrument',
              'action4' => 'Reject Instrument',
              'action5' => 'Provide "as found-as left" data - do not limit',
              'action6' => 'Beyond Economical Repair (BER) - replace unit',
              '100' => 'Yes, 100%',
              '75' => 'No, approximately 75%',
              '50' => 'No, approximately 50%',
              '25' => 'No, approximately 25%',
              '0' => 'No',
              '1' => 'Yes',
          ];
        @endphp
        <div>

        </div>
        <section>
          <div class="flex justify-between mt-[190px] mx-12 px-8 py-2 border-[.100rem] border-gray-700 text-[11px]">
            <div class="flex flex-row">
              <div class="text-end gap-1 max-w-sm pr-2">
                <p class="font-bold text-gray-900">Client: </p>
                @if ($selectedReport->contactPersonName)
                  <p class="font-bold text-gray-900">
                    Attention to:
                  </p>
                @else
                  <p>&nbsp;</p>
                @endif
                @if ($selectedReport->contactPersonEmail)
                  <p class="font-bold text-gray-900">
                    E-mail:
                  </p>
                @else
                  <p>&nbsp;</p>
                @endif
                <p class="font-medium text-gray-900 mt-2">
                  Make:
                </p>
                <p class="font-medium text-gray-900">
                  Model:
                </p>
                <p class="font-medium text-gray-900">
                  Description:
                </p>
              </div>
              <div class="text-start gap-1 max-w-sm">
                <p class="font-medium text-gray-900">{{ $selectedReport->customerName }}</p>
                @if ($selectedReport->contactPersonName)
                  <p class="font-medium text-gray-900">
                    {{ $selectedReport->contactPersonName }}
                  </p>
                @else
                  <p>&nbsp;</p>
                @endif
                @if ($selectedReport->contactPersonEmail)
                  <p class="font-medium text-gray-900">
                    {{ $selectedReport->contactPersonEmail }}
                  </p>
                @else
                  <p>&nbsp;</p>
                @endif
                <p class="font-medium text-gray-900 mt-2">
                  {{ $selectedReport->make }}
                </p>
                <p class="font-medium text-gray-900">
                  {{ $selectedReport->model }}
                </p>
                <p class="font-medium text-gray-900">
                  {{ $selectedReport->description }}
                </p>
              </div>
            </div>
            <div class="flex flex-row">
              <div class="text-end gap-1 max-w-sm pr-2">
                <p class="font-bold text-gray-900 text-[13px]">
                  NCF#:
                </p>
                <p class="font-medium text-gray-900">
                  NCF Date:
                </p>
                <p class="font-medium text-gray-900">
                  &nbsp;
                </p>
                <p class="font-medium text-gray-900 mt-2">
                  &nbsp;
                </p>
                <p class="font-medium text-gray-900">
                  Equipment ID / Asset #:
                </p>
                <p class="font-medium text-gray-900">
                  Serial No.:
                </p>
              </div>
              <div class="text-start gap-1 max-w-sm">
                <p class="font-bold text-gray-900 text-[13px]">
                  100-{{ $selectedReport->ncfNumber }}
                </p>
                <p class="font-medium text-gray-900">
                  {{ \Carbon\Carbon::parse($selectedReport->issuedDate)->format('d-M-Y') }}
                </p>
                <p class="font-medium text-gray-900">
                  &nbsp;
                </p>
                <p class="font-medium text-gray-900 mt-2">
                  &nbsp;
                </p>
                <p class="font-medium text-gray-900">
                  {{ $selectedReport->equipment_id }}
                </p>
                <p class="font-medium text-gray-900">
                  {{ $selectedReport->serial }}
                </p>
              </div>
            </div>
          </div>
        </section>
        <section class="mx-12 px-12 py-1 flex flex-row gap-8 justify-between text-[11px]">
          <div>
            <div class="flex flex-col">
              <span class="font-semibold text-gray-900 -mx-10 text-[12px] italic">Equipment Status</span>
              <span class="font-semibold text-gray-900 italic font-arial">Calibration Status:</span>
              <span class="font-medium text-gray-900 pl-6 font-arial">Calibration completed?</span>
              <span class="font-semibold text-gray-900 mt-2 italic font-arial">Specific Failure:</span>
              @foreach ($selectedReport->specificFailure as $item)
                <span class="font-medium text-gray-900 pl-6 font-arial">{{ $item['specificFailure'] }}</span>
              @endforeach
            </div>
          </div>
          <span
            class="font-normal text-gray-900 font-arial absolute top-[357px] left-[270px]">{{ $selections[$selectedReport->isCalibrationCompleted] }}</span>
          <span class="font-normal text-gray-900 font-arial absolute top-[380px] left-[270px]">(UUT = Unit Under
            Test)</span>
          @if ($selectedReport->troubleshootingStatus)
            <span
              class="font-semibold text-gray-900 italic font-arial absolute top-[340px] right-[170px]">Troubleshooting
              Status:</span>
            <span class="font-normal text-gray-900 pl-6 font-arial absolute top-[357px] right-[85px]">Troubleshooting
              completed? &emsp;&emsp;
              {{ $selections[$selectedReport->troubleshootingStatus] }}.</span>
          @endif
          {{-- <div class="flex flex-row font-arial">
          <div class="text-end gap-1 max-w-sm pr-2">
            <p class="font-medium text-gray-900">
              Reported by:
            </p>
            <p class="font-medium text-gray-900">
              Reviewed by:
            </p>
          </div>
          <div class="text-start gap-1 max-w-sm">
            <p class="font-bold text-gray-900">
              {{ $selectedReport->ncfReportedBy }}
            </p>
            <p class="font-bold text-gray-900">
              {{ $selectedReport->ncfReviewedBy }}
            </p>
          </div>
        </div> --}}
        </section>
        <div class="absolute bottom-[515px]">
          <section class="mx-12 px-12 pb-1">
            <div class="flex flex-col">
              <span class="font-semibold text-gray-900 -mx-10 text-[12px] italic">Recommended Corrective Action:</span>
              <div class="flex gap-4">
                @foreach ($selectedReport->correctiveAction as $item)
                  <div class="flex items-center gap-2">
                    <x-bi-asterisk class="w-1 h-1 text-gray-900 items-center justify-center" />
                    <span class="text-[10px] font-medium text-gray-900 font-arial">{{ $selections[$item] }}</span>
                  </div>
                @endforeach
              </div>
            </div>
          </section>
          <section class="mx-12 px-12 pb-1 font-arial">
            <div class="flex gap-8">
              <span class="font-normal text-gray-900 text-[11px]">Reported By: <span
                  class="font-semibold">{{ $selectedReport->ncfReportedBy }}</span></span>
              <span class="font-normal text-gray-900 text-[11px]">Reviewed By: <span
                  class="font-semibold">{{ $selectedReport->ncfReviewedBy }}</span></span>
            </div>
          </section>
        </div>
        <div class="absolute bottom-[420px] left-[49px] right-[49px]">
          <section
            class=" px-8 py-1 flex flex-row justify-between text-[11px] border-[.100rem] border-gray-700 text-justify">
            <div class="font-arial">
              <div class="flex flex-row">
                <div class="flex flex-col">
                  <p class="font-semibold text-gray-900 -mx-6 italic text-[14px]"
                    style="font-family: 'Times New Roman', Times, serif">Chargeable Items:</p>
                  <span class="pl-14 text-gray-900">1. Calibration Fee:</span>
                  <span class="pl-14 text-gray-900">2. Diagnostic Fee:</span>
                  @if ($selectedReport->conditionalFee == 'repair')
                    <span class="pl-14 text-gray-900">3. Repair Fee:</span>
                  @else
                    <span class="pl-14 text-gray-900">3. Realignment Fee:</span>
                  @endif
                </div>
                <div class="flex flex-col">
                  <p class="font-semibold text-gray-900 -mx-6">&emsp;</p>
                  <span class="pl-14 text-gray-900">Yes</span>
                  <span
                    class="pl-14 text-gray-900">{{ is_numeric($selectedReport->diagnosticFee) ? 'PHP ' . $selectedReport->diagnosticFee : $selectedReport->diagnosticFee }}</span>
                  <span
                    class="pl-14 text-gray-900">{{ is_numeric($selectedReport->conditionalFeeAmount) ? 'PHP ' . $selectedReport->conditionalFeeAmount : $selectedReport->conditionalFeeAmount }}
                  </span>
                </div>
                <div class="flex flex-col">
                  <p class="font-semibold text-gray-900 -mx-6">&emsp;</p>
                  <span class="pl-14 text-gray-900">&emsp;</span>
                  <span class="pl-14 text-gray-900">( VAT - Excluded )</span>
                  <span class="pl-14 text-gray-900">( VAT - Excluded )</span>
                </div>
              </div>
              <div class="flex flex-col text-[8px]">
                <p><span class="font-semibold">Please Note:</span> Repair price quote will be emailed upon completion
                  of
                  the troubleshooting. Diagnostic fee is WAIVED upon approval of repair.
                </p>
              </div>
            </div>
          </section>
        </div>
        @php
          $clientDecision = [
              'action1' => 'Attempt Realignment',
              'action2' => 'Attempt Repair',
              'action3' => 'Limit Instrument',
              'action4' => 'Reject Instrument',
              'action5' => 'Provide "As found-As left" Data',
              'action6' => 'Beyond Economical Repair (BER)',
          ];
        @endphp
        <div class="absolute bottom-[350px] left-[80px]">
          <div class="flex flex-row">
            <span class="flex-1 font-bold italic -mx-6 text-[12px]">Client Decision / Approval:</span>
          </div>
          <div class="grid grid-cols-2 gap-x-2 font-arial text-[10px]">
            @foreach ($selectedReport->clientDecisionRecommendation as $item)
              <div class="flex items-center gap-2">
                <span class="w-3 h-3 text-black border-[.100rem] border-gray-900"></span>
                <span>{{ $clientDecision[$item] }}</span>
              </div>
            @endforeach
          </div>
        </div>
        <div class="absolute bottom-[330px] right-[80px]">
          <div class="flex flex-col text-[11px]">
            <div class="font-bold italic text-[12px]">Client's Instruction / Comment:</div>
            <div>________________________________________________</div>
            <div>________________________________________________</div>
            <div>________________________________________________</div>
            <div>________________________________________________</div>
          </div>
        </div>
        <div class="absolute bottom-[195px] left-[80px]">
          <div class="text-[9px] flex flex-row -mx-4 font-arial">
            <div>
              <p class="font-semibold">Expected Turnaround time:</p>
              <p>&emsp;</p>
            </div>
            <div class="pl-2">
              <p>Realignment: 7-12 working days.</p>
              <p>Troubleshooting: 15 to 20 working days (depending on the availability of replacement parts needed)</p>
            </div>
          </div>
          <div class="text-[11px] font-arial">
            <h1 class="font-bold mt-1 -mx-4">Instructions for Nonconforming parameter/test point(s):</h1>
            <div class="flex items-center gap-2">
              <span class=" w-3 h-3 text-black border-[.100rem] border-gray-900"></span>
              <span>Highlight test points or parameters in Measurement Report. Describe nonconformity in the Remarks
                Section.</span>
            </div>
            <div class="flex items-center gap-2">
              <span class=" w-3 h-3 text-black border-[.100rem] border-gray-900"></span>
              <span>Highlight test points or parameters in Measurement Report, no explanation/description required in
                the
                Remarks Section.</span>
            </div>
            <div class="flex items-center gap-2">
              <span class=" w-3 h-3 text-black border-[.100rem] border-gray-900"></span>
              <span>Do not highlight nonconforming test points or parameters. Describe nonconformity in the Remarks
                Section.</span>
            </div>
            <div class="flex items-center gap-2">
              <span class=" w-3 h-3 text-black border-[.100rem] border-gray-900"></span>
              <span>Do not highlight nonconforming test points or parameters, no explanation/description required in the
                Remarks Section.</span>
            </div>
          </div>
          <div class="mt-1 -mx-4 text-[12px]">
            <h1><span class="font-bold">Note:</span> Revision of Printed Measurement Report will incur
              revision/reprinting fee</h1>
          </div>
        </div>
        <div class="absolute bottom-12">
          <section class="mx-12 px-8 py-1 text-[11px]">
            <div class="flex w-full justify-around">
              <div class="flex flex-col items-center">
                <p class="text-xs font-medium text-black">&emsp;</p>
                <p class="text-xs font-medium text-black overline">&emsp;&emsp;&emsp;&emsp;&emsp;Signature Over Printed
                  Name&emsp;&emsp;&emsp;&emsp;&emsp;</p>
              </div>
              <div class="flex flex-col items-center">
                <p class="text-xs font-medium text-black">&emsp;</p>
                <p class="text-xs font-medium text-black overline">
                  &emsp;&emsp;&emsp;&emsp;&emsp;Date&emsp;&emsp;&emsp;&emsp;&emsp;</p>
              </div>
            </div>
            <div class="font-bold text-center mt-1">
              <div>
                *** Please complete "Client Decision/Approval" section and email back to PMSi ***
              </div>
              <div>
                Or, email your specific instruction to info@pmsi-cal.com or pmsical@yahoo.com
              </div>
            </div>
          </section>
          <section class="mx-8 px-8">
            <div class="text-[9px] font-medium text-black leading-3 text-justify">
              Client understands that there are inherent risks associated in troubleshooting and/or repair of client's
              equipment. As a condition of PMSi performing
              troubleshooting and/or repair to clients equipment, client agrees to indemnify and hold harmless PMSi and
              its officers, employees and agents from any and all damages
              (including partial or total equipment incapacitance), from personal injury or illness, from loss or damage
              to property, or from costs, including court cost and
              attorney's fees that may result from or arising from the troubleshooting and/or repair of clients
              equipment.
              A client's verbal, email or other written instructions
              to PMSi (or any personnel) to perform troubleshooting and/or repair is agreed upon by client as sufficient
              evidence of understanding this terms and agreement.
            </div>
          </section>
        </div>
        {{-- Footer --}}
        <section class="font-arial">
          <div class="absolute bottom-7 left-16 text-[11px] font-medium text-black">
            DCN 4-4.9.1-3 Rev.1
          </div>
          <div class="absolute bottom-7 left-0 right-0 flex justify-center text-[11px] font-medium text-black">
            NCF #100-{{ $selectedReport->ncfNumber }}
          </div>
          <div class="absolute bottom-7 right-16 text-[11px] font-medium text-black">
            Page 1 of 1
          </div>
        </section>
      </div>
    </div>
  </div>
</body>

</html>

{{-- Make sure the img are not from assets --}}
