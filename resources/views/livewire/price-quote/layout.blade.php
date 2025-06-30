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
    <div class="relative bg-cover bg-no-repeat mx-auto border border-white">
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
        <div class="absolute w-28 h-28">
          <img src="https://i.ibb.co/fdh0S6YF/pmsi-logo-copyright.png" alt="PMSi Logo (copyright)">
        </div>
      </div>
      {{-- Heading --}}
      <div class="mt-[150px] flex flex-row text-sm">
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
              {{ $priceQuote->contact_person }}
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
      <div class="text-sm space-y-3 mt-2">
        <div>
          {{ $priceQuote->salutation }}
        </div>
        <div>
          <span class="font-bold text-xs">{{ $priceQuote->introduction }}</span>
        </div>
      </div>

      {{-- Equipment List --}}
      <div class="relative">
        <table class="w-full text-sm mt-4 text-left text-gray-700 dark:text-gray-400 table-fixed">
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
                  @if ($item->item_number)
                    {{ $item->item_number }}
                  @endif
                </td>
                <td class="px-2 py-1">
                  {{ $item->make }}
                </td>
                <td class="bg-gray-50 px-2 py-1">
                  {{ $item->model }}
                </td>
                <td class="px-2 py-1 text-left">
                  @if (!$item->item_number)
                    <span class="text-blue-500">
                      {{ $item->description }}
                    </span>
                  @else
                    <span>
                      {{ $item->description }}
                    </span>
                  @endif
                </td>
                <td class="bg-gray-50 px-2 py-1">
                  @if ($item->item_number)
                    {{ $item->quantity }}
                  @endif
                </td>
                <td class="px-2 py-1">
                  @if ($item->item_number)
                    {{ $item->unit_price }}
                  @endif
                </td>
                <td class="bg-gray-50 px-2 py-1">
                  @if ($item->item_number)
                    {{ $item->line_total }}
                  @endif
                </td>
              </tr>
            @endforeach
            {{-- Table Footer --}}
            <tr class="font-semibold text-gray-700 uppercase dark:text-white text-xs text-center">
              <td class="py-1"></td>
              <td class="py-1"></td>
              <td class="py-1"></td>
              <td scope="row" class="py-1">Total Quantity Quoted</td>
              <td class="py-1">{{ $equipmentList->sum('quantity') }}</td>
              <td scope="row" class="py-1">Sub-Total</td>
              <td class="bg-gray-50 py-1">₱ {{ $priceQuote->subtotal }}</td>
            </tr>
            <tr class="font-semibold text-gray-700 uppercase dark:text-white text-xs text-center">
              <td class="py-1"></td>
              <td class="py-1"></td>
              <td class="py-1"></td>
              <td class="py-1"></td>
              <td class="py-1"></td>
              <td scope="row" class="py-1">12% VAT</td>
              <td class="bg-gray-50 py-1">₱ {{ $priceQuote->vat_amount }}</td>
            </tr>
            <tr class="font-semibold text-gray-700 uppercase dark:text-white text-xs text-center">
              <td class="px-2 py-1"></td>
              <td class="px-2 py-1"></td>
              <td class="px-2 py-1"></td>
              <td class="px-2 py-1"></td>
              <td class="px-2 py-1"></td>
              <td scope="row" class="px-2 py-1">Total PHP</td>
              <td class="bg-gray-50 px-2 py-1">₱ {{ $priceQuote->total }}</td>
            </tr>
          </tbody>
        </table>
        {{-- Note, still under the table --}}
        <div class="w-full mt-[-44px]" style="page-break-inside: avoid;">
          <div class="flex flex-col gap-y-2 w-[70%]">
            <span class="italic uppercase text-xs font-bold">Note:</span>
            <span class="text-xs font-bold text-blue-500">{!! nl2br(e($priceQuote->note)) !!}
            </span>
          </div>
          <div class="w-[30%]"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Price Quote Terms & Conditions --}}
  <div class="relative bg-cover bg-no-repeat mx-auto" style="page-break-before: always;">
    <div class="border-2 border-gray-500 text-[10px] mb-4 p-1 flex flex-col leading-tight text-justify">
      <span class="italic text-[13px]">PMSi Terms & Conditions:</span>

      <span>(1) Quotation:</span>

      <li class="pl-12">This quotation is valid for 30 days from quoted period and only applies to equipment listed
        above
        and customer
        acceptance received within the specified quote period.</li>

      <span>(2) Scheduling:</span>

      <li class="pl-12">Scheduled equipment have first priority, Walk-in equipment delivery is first-come,
        first-served
        basis.</li>

      <li class="pl-12">Pick-up and delivery days are excluded from the expected turnaround time.</li>

      <span>(3) Payment:</span>

      <li class="pl-12">Prices are in Php VAT excluded (unless otherwise stated). Payment terms are <span
          class="text-red-500">COD (cash on
          delivery), CIA (cash
          in advance), Net (# of days).</span><br> Payment will be by cash, check, or direct bank deposit.</li>

      <li class="pl-12">2% late payment fee and 2% finance fee will be accrued for every month invoice remains
        unpaid.
        Accrual will be
        effective from date of invoice.</li>

      <li class="pl-12">All bank transaction processing fees incurred to received or process customer payment are
        payable
        by
        customer.</li>

      <li class="pl-12">Any claims regarding an invoice issued must be made within 7 days of the invoice received
        date or
        emailed
        date.</li>

      <li class="pl-12">Calibration certificates and calibration reports will be release only upon receipt of full
        payment.</li>

      <li class="pl-12">Unpaid, late or incomplete payments will be charged a 2% interest on balance owed per month.
      </li>

      <li class="pl-12">All legal fees incurred to collect unpaid accounts or account balances are payable by
        customer.
      </li>

      <li class="pl-12">Customer agrees to pay calibration fee whether instrument passes calibration or not.</li>

      <span>(4) Equipment Status / Decision Rule:</span>

      <li class="pl-12">On-Site Calibration: Removal and installation of test equipment from rack mounts, cabinets,
        fixtures, etc. are
        customer's responsibility</li>

      <li class="pl-12">Written or verbal authority from the customer is required to perform repair or adjustments
        on
        out-of-tolerance
        equipment discovered during the calibration process and may be subject to additional charges.</li>

      <li class="pl-12">Work on equipment fount to defective, totally or partially inoperative during the
        calibration
        process will
        cease on instance of discovery and the customer will be advised of the situation for a decision to resume or
        suspend the item's calibration.</li>

      <li class="pl-12">Maintenance cancellation for work-in-progress: Equipment will incur a minimum 50% or the
        full
        maintenance fee
        depending on the progress at time of cancellation.</li>

      <li class="pl-12 font-bold">Equipment unclaimed after ninety (90) days upon notification of completion will be
        subject to
        transfer of
        ownership to PMSi.
        PMSi may dispose of these items to recover incurred expenses of calibration, storage, handling and other
        costs.
      </li>

      <span>(5) Calibration Type: PNS ISO 17025 for scope of accreditation, or ANSI Z540, MIL-STD 45662A, or other
        specified for non-accredited.</span>

      <span>(6) Pickup & Delivery: (optional) contact PMSi office for more details.</span>

      <span>(7) On-Site: (optional) contact PMSi office for more details.</span>

      <span>(8) Calibration Procedures: Unless customer specified or directed, will be base on manufacturer
        specification,
        MIL-STD 45662A, NAVAIR, AS, ASME, ASTM, BSI, DIN, DKD, DTI/PNS, EA, EAL, EN, EURACHEM, EURAMET, ISO, MSL,
        NIST,
        AG-NMI, OIML, UKAS, in-house developed, customer developed, and others as available.</span>

      <span>(9) Liability:</span>

      <li class="pl-12">PMSi liability is limited to the cost of calibration re-work. No guarantee on replacement
        parts.
      </li>

      <li class="pl-12">Customer agrees to pay all legal fees incurred due to conflict arising from this
        transaction. Any
        fees
        incurred to collect payment will be charged to customer's account.</li>

      <span>(10) Acceptance:</span>

      <li class="pl-12"><span class="font-bold">PMSi automatically rejects customer purchase order Terms and
          Conditions.</span> Do not issue your
        company purchase
        order unless you agree
        to waive your company's terms and conditions and accept PMSi Terms and Conditions. PMSi accepts the customer's
        purchase solely based on the
        agreed equipment listed and corresponding prices that match PMSi price quotation.
      </li>

      <li class="pl-12">A customer issued purchase order, delivery of items to PMSi, pick-up of item from customer
        site,
        or PMSi
        receipt of full or partial
        payment regarding this price quotation constitutes acceptance in full of PMSi terms and conditions unless
        otherwise noted by
        PMSi. Any changes to this price quotation requires prior written approval from PMSi. Customer agrees that PMSi
        price quote
        terms & conditions supersedes customer's purchase order or purchase requisition terms & conditions.
      </li>

      <li class="pl-12">PMSi reserves the right to withdraw or cancel this price quotation for cause without notice
        to
        client.</li>

      <span>(11) Guarantee:</span>

      <li class="pl-12">Calibration Guarantee: PMSi guarantees calibration of customer equipment only upon pickup by
        or
        delivery to
        customer.
        There is no extended guarantee or warranty from date of delivery or customer receipt.
      </li>

      <li class="pl-12">Repair Guarantee: PMSi guarantees repair work for 7 days from time of completion and for
        labor
        cost
        only.</li>

      <span>(12) Confidentiality:</span>

      <span class="pl-12">This document contains confidential information intended for a specific individual or
        company
        and purpose. If
        you are not the
        intended recipient, you should delete this document and are hereby notified that any disclosure, copying, or
        distribution of this
        document, or taking of any action based on it, is strictly prohibited. PMSi reserves the right to pursue legal
        action on this matter.
      </span>

      <span>(13) Indemnity/Hold Harmless:</span>

      <span class="pl-12">Customer shall defend, indemnify and hold PMSi, its officers, representatives and
        employees
        harmless from any
        and all claims, injuries,
        damages, losses or suits including attorney fees, arising out of or resulting from the acts, errors or
        omissions
        of PMSi in performance of
        this Agreement (price quote). Furthermore, damage to customer equipment may occur due to its condition during
        the
        calibration or repair process; customer agrees to hold PMSi and all its staff blameless and harmless for all
        damage occurrences
        whether committed during in-house (PMSi) or on-site (customer facility).
      </span>
    </div>
    <div class=" flex flex-col text-[13px]">
      <span class="pb-4">Sincerely,</span>
      <span class="">Elvia N. Méndez</span>
      <span class="text-[9px]">( This document is computer generated, no signature is required )</span>
    </div>
    {{-- Footer Here But removed --}}
  </div>


  </div>
</body>

</html>
