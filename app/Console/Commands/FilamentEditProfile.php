<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FilamentEditProfile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'edit-profile:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a link for the EditProfile directory';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $link = base_path('vendor/joaopaulolndev/filament-edit-profile/src/Livewire');
        $target = app_path('Livewire/EditProfile');

        if (!file_exists($link)) {
            if (symlink($target, $link)) {
                $this->info('Linked successfully.');
            } else {
                $this->error('Failed to link.');
            }
        } else {
            $this->info('Link already exists.');
        }

        return 0;
    }
}