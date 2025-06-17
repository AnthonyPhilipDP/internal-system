<div class="border relative w-[8.5in] h-[11in] mx-auto">
  <div class="relative">
    {{-- Top --}}
    <div class="absolute left-36 right-12 top-12">
      <div class="flex flex-row gap-x-4 text-xs">
        <div class="flex flex-col gap-y-2 w-[70%]">
          <span>{{ $customer->name ?? '' }}</span>
          <span>{{ $invoice->contactPerson ?? '' }}</span>
          <span>{{ $invoice->carbonCopy ?? '' }}</span>
          <span>{{ $customer->address ?? '' }}</span>
          <span>{{ $customer->areaCodeTelephone1 ?? '' }} - {{ $customer->telephone1 ?? '' }}</span>
          <span>{{ $customer->email ?? '' }}</span>
        </div>
        <div class="flex flex-col gap-y-2 w-[30%]">
          <span>{{ $invoice->invoice_date }}</span>
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
    <div class="absolute left-12 right-12 top-56">
      <div class="flex flex-col text-xs">
        @foreach ($items as $item)
          <div class="flex flex-row gap-x-8">
            <span>{{ $item['item_number'] }}</span>
            <span>{{ $item['make'] }}</span>
            <span>{{ $item['model'] }}</span>
            <span>{{ $item['description'] }}</span>
            <span>{{ $item['serial'] }}</span>
            <span>{{ $item['qty'] }}</span>
            <span>{{ $item['unit_price'] }}</span>
            <span>{{ $item['total'] }}</span>
            <span>{{ $item['less'] }}</span>
            <span>{{ $item['charges'] }}</span>
            <span>{{ $item['comment'] }}</span>
          </div>
        @endforeach
      </div>
    </div>

    {{-- <div class="flex flex-col text-xs">
      @foreach ($items as $item)
        <div class="flex flex-row gap-x-8">
          <span>{{ $item['less'] }}</span>
          <span>{{ $item['charges'] }}</span>
          <span>{{ $item['comment'] }}</span>
        </div>
      @endforeach
    </div> --}}

    <div class="absolute top-[700px] left-12 right-12">
      <div class="flex flex-col gap-y-2 items-center justify-center">
        <span>TOTAL ITEMS INVOICED</span>
        <div class="flex flex-col gap-0">
          <span class="text-center">MUST PROVIDE BIR 2307 WITH PAYMENT</span>
          <span class="text-center">TO VALIDATE 1% or 2% DEDUCTION</span>
        </div>
      </div>
    </div>

    {{-- Bottom --}}
    <div class="absolute top-[800px] right-12">
      <div class="flex flex-col gap-y-4 text-xs">
        <div class="flex flex-col gap-0">
          <span>{{ $invoice['subTotal'] }}</span>
          <span>{{ $invoice['vatToggle'] }}</span>
        </div>
        <span>{{ $invoice['total'] }}</span>
      </div>
    </div>
  </div>
</div>
