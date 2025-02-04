<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadExcelFiles extends Command
{
    protected $signature = 'upload:excelfiles';
    protected $description = 'Upload Excel files to the worksheets table';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Directory on your C drive containing Excel files
        $sourceDirectory = 'C:\Users\Anthony\Desktop\Worksheets\5.10.2.1 CAL - PMSi\50-x WS\WS 1501';
        // Destination directory in Laravel storage
        $destinationDirectory = 'worksheets';

        // Ensure the destination directory exists
        if (!Storage::disk('public')->exists($destinationDirectory)) {
            Storage::disk('public')->makeDirectory($destinationDirectory);
        }

        // Get all files from the source directory, excluding '.' and '..'
        $files = array_diff(scandir($sourceDirectory), array('.', '..'));

        $addedCount = 0;
        $skippedCount = 0;
        $failedCount = 0;

        foreach ($files as $file) {
            $filePath = $sourceDirectory . '/' . $file;
            $filename = basename($file);

            if (is_file($filePath) && (Str::endsWith($filename, '.xlsx') || Str::endsWith($filename, '.xls') || Str::endsWith($filename, '.XLS'))) {
                try {
                    // Extract the filename without the extension
                    $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

                    // Check if the file already exists in the destination
                    $newFilePath = $destinationDirectory . '/' . $filename;
                    if (Storage::disk('public')->exists($newFilePath)) {
                        $this->info("Skipped: $filename (already exists in destination)");
                        $skippedCount++;
                        continue;
                    }

                    // Move file to the Laravel storage directory
                    Storage::disk('public')->put($newFilePath, file_get_contents($filePath));

                    // Insert file information into the database
                    DB::table('worksheets')->insert([
                        'name' => $filenameWithoutExtension, // Use the filename without extension
                        'file' => $newFilePath,
                    ]);

                    $this->info("Added: $filename");
                    $addedCount++;
                } catch (\Exception $e) {
                    $this->error("Failed to add $filename: " . $e->getMessage());
                    $failedCount++;
                }
            } else {
                $this->info("Skipped: $filename (not an Excel file)");
                $skippedCount++;
            }
        }

        $this->info("Process completed.");
        $this->info("Total files added: $addedCount");
        $this->info("Total files skipped: $skippedCount");
        $this->info("Total files failed: $failedCount");
    }
}

//To make this file, I use:
//php artisan make:command UploadExcelFiles

//To run this file, I run:
//php artisan upload:excelfiles