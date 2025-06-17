<?php

namespace App\Livewire;

use App\Models\Invoice;
use Livewire\Component;
use App\Models\Customer;
use App\Models\Equipment;
use Livewire\Attributes\Layout;

class InvoiceManager extends Component
{
    #[Layout('components.layouts.vanilla')]
    
    public $invoice;
    public $customer;
    public $items = [];

    public function mount($invoice_id)
    {
        // Eager load customer and items (with equipment if needed)
        $this->invoice = Invoice::with(['items.equipment', 'customer'])->findOrFail($invoice_id);
        $this->customer = Customer::where('customer_id', $this->invoice->customer_id)->first();

        // Prepare items array for the view
        $this->items = $this->invoice->items->map(function ($item) {
            $equipment = Equipment::where('transaction_id', $item->transaction_id)->first();
        
            return [
                'item_number'   => $item->item_number,
                'make'          => $equipment->make ?? '',
                'model'         => $equipment->model ?? '',
                'description'   => $equipment->description ?? '',
                'serial'        => $equipment->serial ?? '',
                'qty'           => $item->quantity,
                'unit_price'    => $item->unit_price,
                'total'         => $item->line_total,
                'less'          => $item->less_amount,
                'charges'       => $item->charge_amount,
                'comment'       => $item->comment ?? '',
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.invoice-manager', [
            'invoice' => $this->invoice,
            'customer' => $this->customer,
            'items' => $this->items,
        ]);
    }
}
