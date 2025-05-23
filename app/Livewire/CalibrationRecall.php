<?php

namespace App\Livewire;
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 0);
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Customer;
use Livewire\Attributes\Layout;

class CalibrationRecall extends Component
{

    #[Layout('components.layouts.vanilla')]

    public $customerData = [];
    public $filteredMonth;
    public $filteredYear;

    public function mount()
    {
        // Retrieve the data from the session
        $this->customerData = session()->pull('calibrationRecallData', []);

        // Retrieve the filtered month and year from the session
        $filter = session()->pull('calibrationRecallFilter', [
            'month' => null,
            'year' => null,
        ]);

        $this->filteredMonth = $filter['month'];
        $this->filteredYear = $filter['year'];

        // Filter the customerData by calibrationDue
        if ($this->filteredMonth && $this->filteredYear) {
            foreach ($this->customerData as $key => $customer) {
                // Filter the equipment array for the customer
                $filteredEquipment = array_filter($customer['equipment'], function ($equipment) {
                    $calibrationDue = Carbon::parse($equipment['calibrationDue']);
                    return (
                        $calibrationDue->format('m') === $this->filteredMonth &&
                        $calibrationDue->format('Y') === $this->filteredYear
                    );
                });

                // If no equipment matches, remove the customer
                if (empty($filteredEquipment)) {
                    unset($this->customerData[$key]);
                } else {
                    // Otherwise, update the customer's equipment with the filtered list
                    $this->customerData[$key]['equipment'] = array_values($filteredEquipment);
                }
            }

            // Reindex the array to avoid gaps in keys
            $this->customerData = array_values($this->customerData);
        }
    }

    public function downloadFiles()
    {
        $storageFolder = storage_path('app/public/calibration-recall');

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

            $monthName = Carbon::createFromFormat('m', $this->filteredMonth)->format('F');
            foreach ($this->customerData as $customer) {
                $filename = $customer['name'] . ' ' . $monthName . '-' . $this->filteredYear . '.html';
                $filePath = $storageFolder . '/' . $filename;

                $html = view('livewire.calibration-recall.layout', ['customer' => $customer])->render();
                file_put_contents($filePath, $html);

                // Convert the saved HTML file to PDF
                $pdfFilename = $customer['name'] . ' ' . $monthName . '-' . $this->filteredYear . '.pdf';
                $pdfPath = $storageFolder . '/' . $pdfFilename;

                \Spatie\Browsershot\Browsershot::html($html)
                    ->showBackground()
                    ->format('Letter')
                    ->disableCaptureURLs()
                    ->ignoreHttpsErrors()
                    ->margins(0, 0, 0, 0)
                    ->timeout(900)
                    ->scale(1)
                    ->save($pdfPath);
            }

            // If only one PDF, download directly
            if (count($this->customerData) === 1) {
                $pdfPath = $storageFolder . '/' . $pdfFilename;
                $this->dispatch('download-complete');
                return response()->download($pdfPath)->deleteFileAfterSend(true);
            }

            // If multiple PDFs, zip only the PDFs
            $zipFileName = 'calibration-recall-' . now()->format('Y-m-d') . '.zip';
            $zipFilePath = $storageFolder . '/' . $zipFileName;
            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                foreach (scandir($storageFolder) as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                        $zip->addFile($storageFolder . '/' . $file, $file);
                    }
                }
                $zip->close();
            } else {
                throw new \Exception('Could not create zip file.');
            }
            $this->dispatch('download-complete');
            return response()->download($zipFilePath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            $this->dispatch('download-error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.calibration-recall.index', [
            'customerData' => $this->customerData,
        ]);
    }
}
