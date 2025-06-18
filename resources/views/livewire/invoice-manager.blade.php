<div class="border relative w-[8.5in] h-[11in] mx-auto text-[11px] font-mono">
  <div class="relative">
    {{-- Top --}}
    <div class="absolute top-[150px] left-[210px] right-12">
      <div class="flex flex-row gap-x-24">
        <div class="flex flex-col gap-y-1 w-[80%]">
          <span>{{ $customer->name ?? '' }}</span>
          <span>{{ $invoice->contactPerson ?? '' }}</span>
          <span>{{ $invoice->carbonCopy ?? '' }}</span>
          <span>{{ $customer->address ?? '' }}</span>
          <span>({{ $customer->areaCodeTelephone1 ?? '' }}) {{ $customer->telephone1 ?? '' }}</span>
          <span>{{ $customer->email ?? '' }}</span>
        </div>
        <div class="flex flex-col gap-y-1 w-[30%]">
          <span>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</span>
          <span>{{ $invoice->poNoCalibration }}</span>
          <span>{{ $invoice->yourRef }}</span>
          <span>{{ $invoice->pmsiRefNo }}</span>
          <span>{{ $invoice->freeOnBoard }}</span>
          <span>{{ $invoice->businessSystem }}</span>
          <span>{{ $invoice->tin }}</span>
        </div>
      </div>
    </div>

    {{-- Middle --}}
    <div class="absolute top-[330px] left-12 right-12">
      <div class="flex flex-col pb-2">
        @foreach ($items as $item)
          <div class="flex flex-row my-[-2px] truncate">
            <span class="px-[2px] text-start w-[5%] h-4 truncate">{{ $item['item_number'] }}</span>
            <span class="px-[2px] text-start w-[20%] h-4 truncate">{{ $item['make'] }}</span>
            <span class="px-[2px] text-start w-[12.5%] h-4 truncate">{{ $item['model'] }}</span>
            <span class="px-[2px] text-start w-[28%] h-4 truncate">{{ $item['description'] }}</span>
            <span class="px-[2px] text-start w-[16.5%] h-4 truncate">{{ $item['serial'] }}</span>
            <span class="px-[2px] text-start w-[2%] h-4 truncate">{{ $item['quantity'] }}</span>
            <span class="px-[2px] text-end w-[8%] h-4 truncate">{{ $item['unit_price'] }}</span>
            <span class="px-[2px] text-end w-[8%] h-4 truncate">{{ $item['line_total'] }}</span>
          </div>
        @endforeach
      </div>
      @if ($invoice->applyToAll)
        <div class="flex flex-col">
          <div class="grid grid-cols-3 gap-x-6">
            <div class="flex flex-col col-span-1 text-end">
              <span>Less Breakdown:</span>
              <span>Charges Breakdown:</span>
              <span>Remarks Breakdown:</span>
            </div>
            <div class="flex flex-col col-span-1">
              <span>{{ $invoice->global_less_percentage . '% ' }}{{ $invoice->global_less_type . ' for all items' }}</span>
              <span>{{ $invoice->global_charge_percentage . '% ' }}{{ $invoice->global_charge_type . ' for all items' }}</span>
              <span>{{ $invoice->comments }}</span>
            </div>
            <div class="flex flex-col col-span-1 text-end">
              <span>{{ $invoice->global_less_amount }}</span>
              <span>{{ $invoice->global_charge_amount }}</span>
              <span></span>
            </div>
          </div>
        </div>
      @else
        {{-- <div class="flex flex-col">
          <div class="grid grid-cols-3 gap-x-6">
            <div class="flex flex-col col-span-1 text-end">
              @foreach ($items as $item)
                @if ($loop->first)
                  @if (!empty($item['less_type']) && ($item['less_percentage'] ?? 0) > 0)
                    <span>Less Breakdown:</span>
                  @endif
                @endif
                @if ($loop->first)
                  @if (!empty($item['charge_type']) && ($item['charge_percentage'] ?? 0) > 0)
                    <span>Charges Breakdown:</span>
                  @endif
                @endif
              @endforeach
              <span>Remarks Breakdown:</span>
            </div>
            <div class="flex flex-col col-span-1">
              @foreach ($items as $item)
                <!-- Less -->
                @if (!empty($item['less_type']) && ($item['less_percentage'] ?? 0) > 0)
                  <span>{{ $item['less_percentage'] }}% {{ $item['less_type'] }} for item
                    {{ $item['item_number'] }}</span>
                @endif

                <!-- Charges -->
                @if (!empty($item['charge_type']) && ($item['charge_percentage'] ?? 0) > 0)
                  <span>{{ $item['charge_percentage'] }}% {{ $item['charge_type'] }} for item
                    {{ $item['item_number'] }}</span>
                @endif
              @endforeach
              <span>{{ $invoice->comments }}</span>
            </div>
            <div class="flex flex-col col-span-1 text-end">
              @foreach ($items as $item)
                @if (!empty($item['less_type']) && ($item['less_percentage'] ?? 0) > 0)
                  <span>{{ $item['less_amount'] }}</span>
                @endif
                @if (!empty($item['charge_type']) && ($item['charge_percentage'] ?? 0) > 0)
                  <span>{{ $item['charge_amount'] }}</span>
                @endif
                <span></span>
              @endforeach
            </div>
          </div>
        </div> --}}
      @endif
    </div>


    <div class="absolute top-[780px] left-12 right-12 text-[14px]">
      <div class="relative flex flex-col gap-y-2 items-center justify-center font-bold">
        <span>TOTAL ITEMS INVOICED</span>
        <span class="absolute top-0 right-36 text-[11px] text-red-500">{{ count($items) }}</span>
        <div class="flex flex-col">
          <span class="text-center">MUST PROVIDE BIR 2307 WITH PAYMENT</span>
          <span class="text-center">TO VALIDATE 1% or 2% DEDUCTION</span>
        </div>
      </div>
    </div>

    {{-- Bottom --}}
    <div class="absolute top-[800px] right-12">
      <div class="flex flex-col gap-y-4">
        <div class="flex flex-col gap-0">
          <div class="flex flex-row justify-between gap-x-8">
            <span>Sub Total</span>
            <span>{{ $invoice['subTotal'] }}</span>
          </div>
          <div class="flex flex-row justify-between gap-x-8">
            <span>12% VAT</span>
            <span>{{ $invoice['vatToggle'] ? 'VAT EXEMPTED' : $invoice['vatAmount'] }}</span>
          </div>
        </div>
        <div class="flex flex-row justify-between">
          <span></span>
          <span>{{ $invoice->currency === 'PHP' ? '₱' : '$' }} {{ $invoice['total'] }}</span>
        </div>
      </div>
    </div>
  </div>
</div>
