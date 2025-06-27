<div>
  <button wire:loading.attr="disabled" wire:click="downloadFiles"
    class="absolute top-4 left-4 bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700">
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
  <div class="relative w-[8.5in] bg-cover bg-no-repeat mx-auto border">
    <div style="font-family: 'Times New Roman', Times, serif;">
      <div class="absolute top-[45px] inset-0 flex flex-col items-center tracking-normal -space-y-1">
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
      <div class="absolute top-[54px] left-16 w-28 h-28">
        <img src="{{ asset('images/PMSi Logo(transparent).png') }}" alt="PMSi Logo">
      </div>
    </div>
    {{-- Heading --}}
    <div class="mt-[195px] mx-16 flex flex-row text-sm">
      {{-- Left Side Heading --}}
      <div class="flex flex-row w-[50%] gap-x-4">
        <section class="flex flex-col text-end font-bold">
          <div>
            Date:
          </div>
          <div>
            To:
          </div>
          <div>
            @if ($priceQuote->contact_person)
              Attn:
            @endif
          </div>
          <div>
            @if ($priceQuote->carbon_copy)
              CC:
            @else
              &nbsp;
            @endif
          </div>
          <div>
            RE:
          </div>
        </section>
        <section>
          <div class="font-bold">
            {{ \Carbon\Carbon::parse($priceQuote->price_quote_date)->format('d/m/Y') }}
          </div>
          <div>
            {{ $customer->name }}
          </div>
          <div class="font-bold">
            @if ($priceQuote->contact_person_identity === 'female')
              Ms. {{ $priceQuote->contact_person }}
            @else
              Mr. {{ $priceQuote->contact_person }}
            @endif
          </div>
          <div>
            @if ($priceQuote->carbon_copy)
              {{ $priceQuote->carbon_copy }}
            @else
              &nbsp;
            @endif
          </div>
          <div>
            {{ $priceQuote->subject }}
          </div>
        </section>
      </div>
      {{-- Right Side Heading --}}
      <div class="flex flex-row w-[50%] gap-x-4">
        <section class="flex flex-col text-end">
          <div class="font-bold text-[18px] mb-2">
            @if ($priceQuote->price_quote_number)
              PMSi REF:
            @endif
          </div>
          <div class="font-bold">
            @if ($priceQuote->customer_ref)
              Customer REF #:
            @endif
          </div>
          <div>
            @if ($priceQuote->customer_fax)
              Customer Fax #:
            @endif
          </div>
          <div>
            @if ($priceQuote->pmsi_fax)
              PMSi Fax #:
            @endif
          </div>
          <div>
            @if ($priceQuote->customer_email)
              Customer E-mail:
            @endif
          </div>
          @if ($priceQuote->customer_mobile)
            Customer Mobile:
          @endif
          <div>
          </div>
          <div>
            @if ($priceQuote->quote_period)
              Quote Period:
            @endif
          </div>
        </section>
        <section>
          <div class="font-bold bg-gray-300 text-[18px] mb-2">
            @if ($priceQuote->price_quote_number)
              PQ 20 - <span class="text-blue-500">{{ $priceQuote->price_quote_number }}</span>
            @endif
          </div>
          <div class="bg-yellow-200">
            @if ($priceQuote->customer_ref)
              {{ $priceQuote->customer_ref }}
            @endif
          </div>
          <div>
            @if ($priceQuote->customer_fax)
              {{ $priceQuote->customer_fax }}
            @endif
          </div>
          <div>
            @if ($priceQuote->pmsi_fax)
              {{ $priceQuote->pmsi_fax }}
            @endif
          </div>
          <div>
            @if ($priceQuote->customer_email)
              {{ $priceQuote->customer_email }}
            @endif
          </div>
          @if ($priceQuote->customer_mobile)
            {{ $priceQuote->customer_mobile }}
          @endif
          <div>
          </div>
          <div>
            @if ($priceQuote->quote_period)
              <span class="text-blue-500">{{ $priceQuote->quote_period }}</span>
            @endif
          </div>
        </section>
      </div>
    </div>

    {{-- Letter Initial Information --}}
    <div class="mx-16 text-sm space-y-3">
      <div>
        {{ $priceQuote->salutation }}
      </div>
      <div>
        <span class="font-bold text-xs">{{ $priceQuote->introduction }}</span>
      </div>
    </div>

    {{-- Equipment List --}}
    <div class="mx-16">
      <div class="relative">
        <table class="w-full text-sm text-left text-gray-700 dark:text-gray-400 table-fixed">
          <thead class="text-xs text-center text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
              <th scope="col" class="bg-gray-100 px-2 py-1 w-[10%]">
                Item
              </th>
              <th scope="col" class="px-2 py-1 w-[10%]">
                Make
              </th>
              <th scope="col" class="bg-gray-100 px-2 py-1 w-[15%]">
                Model
              </th>
              <th scope="col" class="px-2 py-1 w-[25%]">
                Description
              </th>
              <th scope="col" class="bg-gray-100 px-2 py-1 w-[10%]">
                QTY
              </th>
              <th scope="col" class="px-2 py-1 w-[15%]">
                Unit Price
              </th>
              <th scope="col" class="bg-gray-100 px-2 py-1 w-[15%]">
                Extended Price
              </th>
            </tr>
          </thead>
          <tbody>
            @foreach ($equipmentList as $item)
              <tr class="text-xs text-center bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                <td class="bg-gray-50 px-2 py-1">
                  {{ $item->item_number }}
                </td>
                <td class="px-2 py-1">
                  {{ $item->make }}
                </td>
                <td class="bg-gray-50 px-2 py-1">
                  {{ $item->model }}
                </td>
                <td class="px-2 py-1 text-left">
                  {{ $item->description }}
                </td>
                <td class="bg-gray-50 px-2 py-1">
                  {{ $item->quantity }}
                </td>
                <td class="px-2 py-1">
                  {{ $item->unit_price }}
                </td>
                <td class="bg-gray-50 px-2 py-1">
                  {{ $item->line_total }}
                </td>
              </tr>
            @endforeach
          </tbody>

          <tfoot class="text-xs text-center">
            <tr class="font-semibold text-gray-700 uppercase dark:text-white">
              <td class="px-2 py-1"></td>
              <td class="px-2 py-1"></td>
              <td class="px-2 py-1"></td>
              <td class="px-2 py-1"></td>
              <td class="px-2 py-1"></td>
              <td scope="row" class="px-2 py-1">Total PHP</td>
              <td class="bg-gray-50 px-2 py-1">₱ {{ $priceQuote->total }}</td>
            </tr>
          </tfoot>

          <tfoot class="text-xs text-center">
            <tr class="font-semibold text-gray-700 uppercase dark:text-white">
              <td class="py-1"></td>
              <td class="py-1"></td>
              <td class="py-1"></td>
              <td scope="row" class="py-1">Total Unity Quoted</td>
              <td class="py-1">{{ $equipmentList->sum('quantity') }}</td>
              <td scope="row" class="py-1">Sub-Total</td>
              <td class="bg-gray-50 py-1">₱ {{ $priceQuote->subtotal }}</td>
            </tr>
          </tfoot>

          <tfoot class="text-xs text-center">
            <tr class="font-semibold text-gray-700 uppercase dark:text-white">
              <td class="py-1"></td>
              <td class="py-1"></td>
              <td class="py-1"></td>
              <td class="py-1"></td>
              <td class="py-1"></td>
              <td scope="row" class="py-1">12% VAT</td>
              <td class="bg-gray-50 py-1">₱ {{ $priceQuote->vat_amount }}</td>
            </tr>
          </tfoot>
        </table>
        {{-- Note, still under the table --}}
        <div class="w-full mt-[-44px]">
          <div class="flex flex-col gap-y-2 w-[70%]">
            <span class="italic uppercase text-xs font-bold">Note:</span>
            <span class="text-xs font-bold text-blue-500">{!! nl2br(e($priceQuote->note)) !!}
            </span>
          </div>
          <div class="w-[30%]"></div>
        </div>
      </div>
    </div>
    <!-- Footer -->
    <hr class="absolute left-12 right-12 bottom-12 border-gray-800 mb-1">
    <div class="absolute bottom-9 left-12 text-left text-[11px] font-[500] text-gray-800"
      style="font-family: 'Times New Roman', Times, serif;">
      DCN 4-4.3.2.3-3
    </div>
    <div class="absolute bottom-9 right-12 text-right text-[11px] font-[500] text-gray-800"
      style="font-family: 'Times New Roman', Times, serif;">
      Page 1
    </div>
  </div>
  <x-price-quote-terms-and-conditions />

</div>
