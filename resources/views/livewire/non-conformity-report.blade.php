<div>
  <a class="absolute top-4 left-4 bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700 print:hidden"
    href="{{ url('admin/non-conformity-reports') }}">
    <button>
      Go back
    </button>
  </a>
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
          <p class="text-[11px] text-center font-semibold mt-1">B1 L3 Macaria Business Center, Governor's Dr., Carmona,
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
        <img src="{{ asset('images/PMSi Logo(transparent).png') }}" alt="PMSi Logo">
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
              @endif
              @if ($selectedReport->contactPersonEmail)
                <p class="font-bold text-gray-900">
                  E-mail:
                </p>
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
              @endif
              @if ($selectedReport->contactPersonEmail)
                <p class="font-medium text-gray-900">
                  {{ $selectedReport->contactPersonEmail }}
                </p>
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
      <section class="mx-12 px-12 py-1 flex flex-row justify-between text-[11px]">
        <div>
          <div class="flex flex-col">
            <span class="font-semibold text-gray-900 -mx-10 text-[12px] italic">Equipment Status</span>
            <span class="font-semibold text-gray-900 italic font-arial">Calibration Status:</span>
            <span class="font-medium text-gray-900 pl-6 font-arial">Calibration completed?</span>
            <span class="font-semibold text-gray-900 mt-2 italic font-arial">Specific Failure:</span>
            <span class="font-medium text-gray-900 pl-6 font-arial">{{ $selectedReport->specificFailure }}</span>
            <span class="font-semibold text-gray-900 mt-2 italic font-arial">Troubleshooting Status:</span>
            <span class="font-medium text-gray-900 pl-6 font-arial">Troubleshooting completed?</span>
          </div>
        </div>
        <div class="flex-1 ml-8">
          <div class="flex flex-col">
            <span class="font-semibold text-gray-900">&emsp;</span>
            <span class="font-semibold text-gray-900">&emsp;</span>
            <span
              class="font-medium text-gray-900 font-arial">{{ $selections[$selectedReport->isCalibrationCompleted] }}</span>
            <span class="font-semibold text-gray-900 mt-2 font-arial">(UUT = Unit Under Test)</span>
            <span class="font-medium text-gray-900">&emsp;</span>
            <span class="font-semibold text-gray-900 mt-2">&emsp;</span>
            <span
              class="font-medium text-gray-900 font-arial">{{ $selections[$selectedReport->troubleshootingStatus] }}.</span>
          </div>
        </div>
        <div class="flex flex-row font-arial pt-24">
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
        </div>
      </section>
      <section class="mx-12 px-12 py-1">
        <div class="flex flex-col">
          <span class="font-semibold text-gray-900 -mx-10 text-[12px] italic">Recommended Corrective Action:</span>
          <div class="grid grid-cols-3">
            @foreach ($selectedReport->correctiveAction as $item)
              <div class="flex items-center gap-1">
                <x-bi-asterisk class="w-1 h-1 text-gray-900 items-center justify-center" />
                <span class="text-[10px] font-medium text-gray-900 font-arial">{{ $selections[$item] }}</span>
              </div>
            @endforeach
          </div>
        </div>
      </section>
      <section
        class="mx-12 px-8 py-1 flex flex-row justify-between text-[11px] border-[.100rem] border-gray-700 text-justify">
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
              <span class="pl-14 text-gray-900">{{ $selectedReport->diagnosticFee }}</span>
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
            <p><span class="font-semibold">Please Note:</span> Repair price quote will be emailed upon completion of
              the troubleshooting. Diagnostic fee is WAIVED upon approval of repair.
              Diagnostic fee will also be WAIVED if no replacement parts are available or if we determine unit is beyond
              economical repair.
            </p>
          </div>
        </div>
      </section>
      <section class="mx-12 px-8 py-2 text-[11px]">
        <div class="flex flex-row">
          <span class="flex-1 font-bold italic -mx-6 text-[12px]">Client Decision / Approval:</span>
          <span class="flex-1 font-bold italic -mx-6 text-[12px] ml-36">Client's Instruction / Comment:</span>
        </div>
        <div class="flex flex-row">
          <div class="flex">
            <div class="flex flex-row gap-3 font-arial text-[10px]">
              <div>
                <div class="flex items-center gap-2">
                  <span class=" w-3 h-3 text-black border-[.100rem] border-gray-900"></span>
                  <span>Attempt Realignment</span>
                </div>
                <div class="flex items-center gap-2">
                  <span class=" w-3 h-3 text-black border-[.100rem] border-gray-900"></span>
                  <span>Attempt Repair</span>
                </div>
                <div class="flex items-center gap-2">
                  <span class=" w-3 h-3 text-black border-[.100rem] border-gray-900"></span>
                  <span>Limit Instrument</span>
                </div>
              </div>
              <div>
                <div class="flex items-center gap-2">
                  <span class=" w-3 h-3 text-black border-[.100rem] border-gray-900"></span>
                  <span>Reject Instrument</span>
                </div>
                <div class="flex items-center gap-2">
                  <span class=" w-3 h-3 text-black border-[.100rem] border-gray-900"></span>
                  <span>Provide "As found - As left" Data</span>
                </div>
                <div class="flex items-center gap-2">
                  <span class=" w-3 h-3 text-black border-[.100rem] border-gray-900"></span>
                  <span>Beyond Economical Repair (BET) - replace unit</span>
                </div>
              </div>
            </div>
          </div>
          <div class="flex flex-col font-arial italic ml-8">
            {{-- <div class="font-bold">Client Instruction / Comment:</div> --}}
            <div class="underline">________________________________________</div>
            <div class="underline">________________________________________</div>
            <div class="underline">________________________________________</div>
            <div class="underline">________________________________________</div>
          </div>
        </div>
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
          <h1 class="font-bold mt-2 -mx-4">Instructions for Nonconforming parameter/test point(s):</h1>
          <div class="flex items-center gap-2">
            <span class=" w-3 h-3 text-black border-[.100rem] border-gray-900"></span>
            <span>Highlight test points or parameters in Measurement Report. Describe nonconformity in the Remarks
              Section.</span>
          </div>
          <div class="flex items-center gap-2">
            <span class=" w-3 h-3 text-black border-[.100rem] border-gray-900"></span>
            <span>Highlight test points or parameters in Measurement Report, no explanation/description required in the
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
        <div class="mt-2 -mx-4 text-[12px]">
          <h1><span class="font-bold">Note:</span> Revision of Printed Measurement Report will incur
            revision/reprinting fee</h1>
        </div>
      </section>
      <div class="absolute bottom-12">
        <section class="mx-12 px-8 py-2 text-[11px]">
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
          <div class="font-bold text-center mt-2">
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
            attorney's fees that may result from or arising from the troubleshooting and/or repair of clients equipment.
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
</div>
