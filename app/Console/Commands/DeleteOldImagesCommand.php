<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteOldImagesCommand extends Command
{
    protected $signature = 'images:delete-old';

    protected $description = 'Delete uploaded images older than 3 hours';

    public function handle(): void
    {
        $this->info('Starting cleanup of old images...');

        $disk = Storage::disk('local');
        $files = $disk->files('public/images');
        $threshold = Carbon::now()->subHours(3);
        $count = 0;

        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp($disk->lastModified($file));

            if ($lastModified->lt($threshold)) {
                $disk->delete($file);
                $count++;
                $this->info("Deleted: {$file}");
            }
        }

        $this->info("Cleanup complete. {$count} images deleted.");
    }
}
