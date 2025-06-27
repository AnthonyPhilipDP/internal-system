<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use Livewire\Attributes\Layout;

class PriceQuote extends Component
{
    #[Layout('components.layouts.vanilla')]

    public $priceQuote;
    public $customer;
    public $equipmentList = [];

    public function mount($price_quote_id)
    {
        $this->priceQuote = \App\Models\PriceQuote::with(['customer', 'equipment_list.equipment'])->findOrFail($price_quote_id);
        $this->customer = Customer::where('customer_id', $this->priceQuote->customer_id)->first();
        $this->equipmentList = $this->priceQuote->equipment_list;
    }

    public function render()
    {
        return view('livewire.price-quote');
    }
}
