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
                'invoice_id'        => $item->invoice_id,
                'item_number'       => $item->item_number,
                'quantity'          => $item->quantity,
                'unit_price'        => $item->unit_price,
                'less_type'         => $item->less_type,
                'less_percentage'   => $item->less_percentage,
                'less_amount'       => $item->less_amount,
                'charge_type'       => $item->charge_type,
                'charge_percentage' => $item->charge_percentage,
                'charge_amount'     => $item->charge_amount,
                'line_total'        => $item->line_total,
                
                'transaction_id'    => $item->transaction_id,
                'make'              => $equipment->make ?? '',
                'model'             => $equipment->model ?? '',
                'description'       => $equipment->description ?? '',
                'serial'            => $equipment->serial ?? '',
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
