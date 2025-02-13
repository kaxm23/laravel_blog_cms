<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Backup the database';

    public function handle()
    {
        $filename = 'backup-' . Carbon::now()->format('Y-m-d-H-i-s') . '.sql';
        
        // Create backup
        $command = sprintf(
            'mysqldump -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            storage_path('app/backups/' . $filename)
        );
        
        exec($command);

        // Upload to S3
        Storage::disk('s3')->put(
            'backups/' . $filename,
            Storage::disk('local')->get('backups/' . $filename)
        );

        // Clean up old backups (keep last 30 days)
        $this->cleanOldBackups();
    }

    private function cleanOldBackups()
    {
        $files = Storage::disk('s3')->files('backups');
        $cutoff = Carbon::now()->subDays(30);

        foreach ($files as $file) {
            $fileDate = Carbon::createFromFormat('Y-m-d-H-i-s', substr($file, 7, 19));
            if ($fileDate->lt($cutoff)) {
                Storage::disk('s3')->delete($file);
            }
        }
    }
}