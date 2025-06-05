<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NcfReport;
use Livewire\Attributes\Layout;
use Spatie\Browsershot\Browsershot;

class NonConformityReport extends Component
{

    #[Layout('components.layouts.vanilla')]

    public $selectedReport;

    public function mount($reportId)
    {
        $this->selectedReport = NcfReport::find($reportId);
    }

    public function downloadPdf($reportId)
    {
        $selectedReport = NcfReport::find($reportId);

        // Define storage folder
        $storageFolder = storage_path('app/public/reports');

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

        // Define filenames
        $filename = 'non-conformity-report-' . $reportId . '.html';
        $filePath = $storageFolder . '/' . $filename;

        // Render the Blade view to HTML
        $html = view('livewire.ncf-report.pdf', compact('selectedReport'))->render();
        file_put_contents($filePath, $html);

        // Convert the saved HTML file to PDF
        $pdfFilename = 'NCF #100-' . $selectedReport->transaction_id . ' ' . $selectedReport->customerName .'.pdf';
        $pdfPath = $storageFolder . '/' . $pdfFilename;

        Browsershot::html($html)
            ->showBackground()
            ->format('Letter')
            ->disableCaptureURLs()
            ->ignoreHttpsErrors()
            ->margins(0, 0, 0, 0)
            ->timeout(900)
            ->scale(1)
            ->save($pdfPath);
        return response()->download($pdfPath)->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.ncf-report.index');
    }
}
