<?php

namespace App\Livewire;

use Exception;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Customer;
use Livewire\Attributes\Layout;
use Spatie\LaravelPdf\Enums\Unit;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\Browsershot\Browsershot;

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

    public function downloadFiles()
    {
        $storageFolder = storage_path('app/public/price-quote');

        // Clean up the folder before generating new files
        if (is_dir($storageFolder)) {
            foreach (scandir($storageFolder) as $file) {
                if ($file !== '.' && $file !== '..') {
                    unlink($storageFolder . '/' . $file);
                }
            }
        } else {
            mkdir($storageFolder, 0777, true);
        }
                
        try {
            $price_quote_number =  $this->priceQuote->price_quote_number;
            $customer_id = $this->customer->customer_id;
            $nickname = $this->customer->nickname;

            $filename = $price_quote_number . '(' . $customer_id . ').html';

            $filePath = $storageFolder . '/' . $filename;

            $html = view('livewire.price-quote.layout', ['priceQuote' => $this->priceQuote, 'customer' => $this->customer, 'equipmentList' => $this->equipmentList])->render();
            file_put_contents($filePath, $html);

            
            // Convert the saved HTML file to PDF
            $nickname ? $pdfFilename = $price_quote_number . '(' . $nickname . ').pdf' : $pdfFilename = $price_quote_number . '(' . $customer_id . ').pdf';

            $pdfPath = $storageFolder . '/' . $pdfFilename;
            
            $footer = '
                <div style="width: calc(100% - 128px); margin: 0 auto;">
                    <hr style="border: 1px solid #1f2937; margin-bottom: 4px; width: 100%;">
                    <div style="display: table; width: 100%; font-size: 11px; font-weight: 500; color: #1f2937; font-family: \'Times New Roman\', Times, serif;">
                    <span style="display: table-cell; text-align: left;">DCN 4-4.3.2.3-3</span>
                    <span style="display: table-cell; text-align: right;"><span class="pageNumber"></span></span>
                    </div>
                </div>
            ';

            Browsershot::html($html)
                ->showBackground()
                ->showBrowserHeaderAndFooter()
                ->hideHeader()
                ->format('Letter')
                ->margins(48, 64, 48, 64, 'px')
                ->footerHtml($footer)
                ->save($pdfPath);

            $this->dispatch('download-complete');
            return response()->download($pdfPath)->deleteFileAfterSend(true);
        } catch (Exception $e) {
            $this->dispatch('download-error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.price-quote.index');
    }
}
