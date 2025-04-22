<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\StorageHelper;

class CreateStorageLink extends Command
{
    protected $signature = 'storage:link-custom';
    protected $description = 'Create a symbolic link from public/storage to storage/app/public';

    public function handle()
    {
        if (StorageHelper::createStorageLink()) {
            $this->info('The [public/storage] directory has been linked.');
            
            // Pastikan direktori gambar ada
            StorageHelper::ensureGambarDirectoryExists();
            
            // Pindahkan semua gambar yang ada
            $files = glob(storage_path('app/public/gambar/*'));
            foreach ($files as $file) {
                StorageHelper::moveImageToCorrectLocation($file);
            }
            
            $this->info('All images have been synchronized.');
        } else {
            $this->error('The symbolic link could not be created.');
        }
    }
} 