<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{ $customer['name'] }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
  <div>
    @php
      $person = !empty($customer['contact_persons']) ? $customer['contact_persons'][0] : null;
      $equipmentCount = count($customer['equipment']);
    @endphp
    <div class="relative bg-cover bg-no-repeat mx-auto border border-white page-break">
      {{-- Header --}}
      <div style="font-family: 'Times New Roman', Times, serif;">
        <div class="absolute inset-0 flex flex-col items-center tracking-normal -space-y-1">
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
              Carmona, Cavite, 4116 Philippines</p>
          </div>
          <div class="flex">
            <p class="text-[11px] text-center font-semibold">Telefax:(046) 889-0673 | Mobile: (0997) 410-6031</p>
          </div>
          <div class="flex">
            <p class="text-[11px] text-center font-semibold">E-mail: info@pmsi-cal.com ~ pmsical@yahoo.com | Website:
              www.pmsi-cal.com</p>
          </div>
        </div>
        <div class="absolute w-28 h-28">
          <img src="https://i.ibb.co/fdh0S6YF/pmsi-logo-copyright.png" alt="PMSi Logo (copyright)">
        </div>
      </div>
      {{-- Heading --}}
      <div class="mt-[150px] flex flex-row text-sm">
        <div class="flex justify-between mb-4 w-full">
          <div class="flex flex-col gap-1 w-[60%]">
            @if ($person)
              @if ($person['identity'] === 'male')
                <p class="text-sm font-bold text-gray-700 truncate capitalize">Mr. {{ $person['name'] }}</p>
              @elseif ($person['identity'] === 'female')
                <p class="text-sm font-bold text-gray-700 truncate capitalize">Ms. {{ $person['name'] }}</p>
              @else
                <p class="text-sm font-bold text-gray-700 truncate capitalize">{{ $person['name'] }}</p>
              @endif
            @endif
            <p class="text-sm font-bold text-gray-700 truncate">Client: <span
                class="uppercase">{{ $customer['name'] }}</span></p>
            @if (!empty($person['contact2']))
              <p class="text-sm font-semibold text-gray-700">Fax: {{ $person['contact2'] }}</p>
            @endif
            @if (!empty($person['contact1']))
              <p class="text-sm font-semibold text-gray-700">Tel: {{ $person['contact1'] }}</p>
            @endif
            @if (!empty($customer['email']))
              <p class="text-sm font-semibold text-gray-700">Email: {{ $customer['email'] }}</p>
            @endif
          </div>
          <div class="flex flex-col w-[40%] items-end">
            <div class="items-center">
              <div class="text-3xl font-bold text-gray-700 text-center">Calibration Recall</div>
              <div
                class="mt-4 text-lg text-center font-bold text-gray-800 border border-red-400 bg-yellow-300 py-4 px-6">
                DUE: {{ \Carbon\Carbon::parse($customer['equipment'][0]['calibrationDue'])->format('F Y') }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <hr class="border-gray-800 my-2">

      {{-- Salutation --}}
      <p class="text-xs text-gray-800 my-2 text-justify">
        @if ($person)
          @if ($person['identity'] === 'male')
            Dear Sir:
          @elseif ($person['identity'] === 'female')
            Dear Madam:
          @else
            Dear Sir/Madam:
          @endif
        @endif
        <br><br>
        {{-- Introduction --}}
        Please take note that the following list of <span class="underline italic">equipment will come due</span>
        for calibration service. Please contact our office at your convenience to schedule calibration service of
        this equipment.
      </p>

      {{-- Equipment List --}}
      <div class="border-b border-white">
        <table class="w-full text-sm mt-4 text-left text-gray-700 dark:text-gray-400 table-fixed">
          <thead class="text-[11px] text-center text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
              <th scope="col" class="bg-gray-100 px-2 py-1">
                Cal Due
              </th>
              <th scope="col" class="px-2 py-1">
                Equipment ID
              </th>
              <th scope="col" class="bg-gray-100 px-2 py-1">
                Make
              </th>
              <th scope="col" class="px-2 py-1">
                Model
              </th>
              <th scope="col" class="bg-gray-100 px-2 py-1">
                Description
              </th>
              <th scope="col" class="px-2 py-1">
                Serial No.
              </th>
              <th scope="col" class="bg-gray-100 px-2 py-1">
                Owner
              </th>
            </tr>
          </thead>
          <tbody>
            @foreach ($customer['equipment'] as $equipment)
              <tr class="text-xs text-center bg-white border-b border-gray-200">
                <td class="bg-gray-50 px-2 py-1">
                  @if ($equipment['calibrationDue'])
                    {{ \Carbon\Carbon::parse($equipment['calibrationDue'])->format('d-M-Y') }}
                  @endif
                </td>
                <td class="px-2 py-1">
                  {{ $equipment['equipment_id'] }}
                </td>
                <td class="bg-gray-50 px-2 py-1">
                  {{ $equipment['make'] }}
                </td>
                <td class="px-2 py-1">
                  {{ $equipment['model'] }}
                </td>
                <td class="bg-gray-50 px-2 py-1">
                  {{ $equipment['description'] }}
                </td>
                <td class="px-2 py-1">
                  {{ $equipment['serial'] }}
                </td>
                <td class="bg-gray-50 px-2 py-1">
                  @if ($equipment['isClientExclusive'])
                    <span>{{ $equipment['exclusive_name'] }}</span>
                  @else
                    Not Applicable
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Table footer --}}
      <div>
        <p class="text-xs mt-2 text-gray-700 text-center">
          Total number of equipment{{ $equipmentCount > 1 ? 's' : '' }}:
          <span class="font-semibold">{{ $equipmentCount }}</span>
        </p>
      </div>

      {{-- Letter Body --}}
      <div class="space-y-2 text-xs text-gray-800 my-4 text-justify break-inside-avoid">
        <div class="pb-2">
          We at PMS<span class="text-red-500 italic">i</span> are committed to provide an impartial high-quality
          calibration, maintenance and repair service of test and measurement equipment. We offer an OEM level of
          service & expertise at a competitive rate.
        </div>
        <div class="pb-2">
          For other capabilities, questions, or a quote, please contact us at <span class="font-semibold italic">(046)
            889-0673</span> or <span class="font-semibold italic">(0997) 410
            6031</span>.<br>
          You may also email us at <span class="font-semibold italic">info@pmsi-cal.com</span> or <span
            class="font-semibold italic">pmsical@yahoo.com</span>.
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
      <div style="page-break-after: always;"></div>
      <style>
        .page-break {
          page-break-after: always;
        }
      </style>
    </div>
</body>

</html>

{{-- Make sure the img are not from assets --}}
