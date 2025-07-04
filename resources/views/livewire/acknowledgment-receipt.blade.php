<div>
  @foreach ($equipmentChunks as $chunkIndex => $equipmentChunk)
    <div class="border relative w-[11in] h-[8.5in] bg-cover bg-no-repeat pt-[11.25rem] mx-auto px-12"
      style="background-image: url('{{ asset('images/templates/AcknowledgmentReceipt - Landscape.jpg') }}');">
      <hr class="mb-2 border-t-1 border-gray-700">
      <!-- Display customer and equipment details -->
      <div class="flex w-full justify-between mb-2">
        <div class="flex flex-col items-start gap-1 max-w-sm">
          <p class="text-xs font-bold text-gray-700">Client: <span
              class="underline uppercase">{{ $equipmentChunk->first()->customer->name }}</span></p>
          @php
            $activeContactPerson = $equipmentChunk
                ->first()
                ->customer->contactPerson->filter(fn($contact) => $contact->isActive)
                ->first();
          @endphp
          @if ($activeContactPerson)
            <p class="text-xs font-semibold text-gray-700">Attention of:
              @if ($activeContactPerson->identity == 'male')
                <span class="uppercase">Mr. {{ $activeContactPerson->name }}</span>
              @elseif ($activeContactPerson->identity == 'female')
                <span class="uppercase">Ms. {{ $activeContactPerson->name }}</span>
              @else
                <span class="uppercase">{{ $activeContactPerson->name }}</span>
              @endif
            </p>
          @endif
          @if (
              !is_null($equipmentChunk->first()->customer->telephone1) &&
                  $equipmentChunk->first()->customer->telephone1 !== 'N/A' &&
                  $equipmentChunk->first()->customer->telephone1 !== '' &&
                  $equipmentChunk->first()->customer->telephone1 !== 'n/a')
            <p class="text-xs font-semibold text-gray-700">
              Telephone: ({{ substr($equipmentChunk->first()->customer->telephone1, 0, 3) }})
              {{ substr($equipmentChunk->first()->customer->telephone1, 3, 3) }}-{{ substr($equipmentChunk->first()->customer->telephone1, 6) }}
            </p>
          @endif
          @if (
              !is_null($equipmentChunk->first()->customer->mobile1) &&
                  $equipmentChunk->first()->customer->mobile1 !== 'N/A' &&
                  $equipmentChunk->first()->customer->mobile1 !== '' &&
                  $equipmentChunk->first()->customer->mobile1 !== 'n/a')
            <p class="text-xs font-semibold text-gray-700">
              Telephone: ({{ substr($equipmentChunk->first()->customer->mobile1, 0, 4) }})
              {{ substr($equipmentChunk->first()->customer->mobile1, 4, 3) }}-{{ substr($equipmentChunk->first()->customer->mobile1, 7) }}
            </p>
          @endif
        </div>
        <div class="flex flex-col items-start gap-1 max-w-sm">
          <p class="text-xs font-semibold text-gray-700">Address: {{ $equipmentChunk->first()->customer->address }}</p>
          @if (
              !is_null($equipmentChunk->first()->customer->email) &&
                  $equipmentChunk->first()->customer->email !== 'N/A' &&
                  $equipmentChunk->first()->customer->email !== '' &&
                  $equipmentChunk->first()->customer->email !== 'n/a')
            <p class="text-xs font-semibold text-gray-700">Email: {{ $equipmentChunk->first()->customer->email }}</p>
          @endif
        </div>
        <div class="flex flex-col items-start gap-1 max-w-sm">
          <p class="text-xs font-semibold text-gray-700">Date:
            {{ $equipmentChunk->first()->created_at->format('F d, Y') }}</p>
          <p class="text-xs font-bold text-gray-700">DR Number: 401-{{ $equipmentChunk->first()->ar_id }}</p>
          {{-- <p class="text-xs font-semibold text-gray-700">Gate Pass: 1234</p> --}}
        </div>
      </div>
      <hr class="mb-2 border-t-1 border-gray-700">
      <!-- Table Title -->
      <div class="text-lg font-bold text-gray-800 mb-2 text-center uppercase">
        Acknowledgment Receipt
      </div>

      <div class="border-b border-white">
        <table class="w-full text-sm mt-4 text-left text-gray-700 dark:text-gray-400 table-fixed">
          @if ($equipmentChunks->count() > 1)
            <caption class="caption-bottom text-xs text-gray-500 font-mono mt-4">
              Number of equipment in this page: {{ $equipmentChunk->count() }}
            </caption>
          @endif
          <thead class="text-[11px] text-center text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
              <th scope="col" class="bg-gray-100 px-2 py-1">
                Transaction
              </th>
              <th scope="col" class="px-2 py-1">
                Make
              </th>
              <th scope="col" class="bg-gray-100 px-2 py-1">
                Model
              </th>
              <th scope="col" class="px-2 py-1">
                Description
              </th>
              <th scope="col" class="bg-gray-100 px-2 py-1">
                Equipment ID
              </th>
              <th scope="col" class="px-2 py-1">
                Serial
              </th>
              <th scope="col" class="bg-gray-100 px-2 py-1">
                Inspection
              </th>
              <th scope="col" class="px-2 py-1">
                Accessories
              </th>
              <th scope="col" class="bg-gray-100 px-2 py-1">
                Gate Pass
              </th>
            </tr>
          </thead>
          <tbody>
            @foreach ($equipmentChunk as $record)
              <tr class="text-[10px] text-center bg-white border-b border-gray-200">
                <td class="px-1 py-0.5 text-[10px] bg-gray-50 break-words">
                  {{ $record->transaction_id }}
                </td>
                <td class="px-1 py-0.5 text-[10px] break-words">
                  {{ $record->make }}
                </td>
                <td class="px-1 py-0.5 text-[10px] bg-gray-50 break-words">
                  {{ $record->model }}
                </td>
                <td class="px-1 py-0.5 text-[10px] break-words">
                  {{ $record->description }}
                </td>
                <td class="px-1 py-0.5 text-[10px] bg-gray-50 break-words">
                  {{ $record->equipment_id }}
                </td>
                <td class="px-1 py-0.5 text-[10px] break-words">
                  {{ $record->serial }}
                </td>
                <td class="px-1 py-0.5 text-[10px] bg-gray-50 capitalize">
                  @if (is_array($record->inspection))
                    {!! implode(', ', $record->inspection) !!}
                  @else
                    {{ $record->inspection }}
                  @endif
                </td>
                <td class="px-1 py-0.5 text-[10px] break-words capitalize">
                  @if (isset($record->accessory) && $record->accessory->pluck('name')->filter()->isNotEmpty())
                    {!! implode(', ', $record->accessory->pluck('name')->toArray()) !!}
                  @else
                    <span class="text-yellow-600">No Accessory</span>
                  @endif
                </td>
                <td class="px-1 py-0.5 text-[10px] bg-gray-50 break-words">
                  {{ $record->gatePass }}
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
        <div class="text-xs font-semibold text-gray-600 mb-4 text-center uppercase mt-4">
          @if ($totalEquipmentCount > 1)
            Total Number of Equipments: {{ $totalEquipmentCount }}
          @else
            Total Number of Equipment: {{ $totalEquipmentCount }}
          @endif
        </div>
      @endif
      <div class="absolute bottom-14 left-0 w-full flex justify-around px-12">
        <div class="flex w-full justify-around">
          <div class="flex flex-col items-center gap-8">
            <p class="text-xs font-semibold text-gray-700">Delivered By:</p>
            <p class="text-sm font-semibold text-gray-700 uppercase underline">{{ $deliveryRider }}</p>
            <p class="text-[9px] text-gray-700 mt-[-36px]">Note: Please sign over printer name</p>
          </div>
          <div class="flex flex-col items-start gap-8">
          </div>
          <div class="flex flex-col items-start gap-8">
          </div>
          <div class="flex flex-col items-start gap-8">
          </div>
          <div class="flex flex-col items-start gap-8">
          </div>
          <div class="flex flex-col items-start gap-8">
          </div>
          <div class="flex flex-col items-center gap-8">
            <p class="text-xs font-semibold text-gray-700">Received By:</p>
            <p class="text-sm font-semibold text-gray-700 uppercase underline">{{ $currentUser }}</p>
            <p class="text-[9px] text-gray-700 mt-[-36px]">{{ date('F d, Y g:i A') }}</p>
          </div>
        </div>
      </div>

      <!-- Page number and CDN number -->
      <div class="absolute bottom-6 left-12 text-xs font-semibold text-gray-500">
        DCN 4-4.13.1.1-38
      </div>
      <div class="absolute bottom-6 right-0 left-0 text-center text-xs font-semibold text-gray-500">
        Page {{ $chunkIndex + 1 }} of {{ $equipmentChunks->count() }}
      </div>
    </div>
  @endforeach
  <style>
    @media print {
      @page {
        size: Letter landscape;
      }
    }
  </style>
</div>

<script>
  window.onload = function() {
    window.print();
  };
</script>
