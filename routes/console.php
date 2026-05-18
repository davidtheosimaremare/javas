<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('storage:migrate-to-minio', function () {
    $this->info('🚀 Memulai migrasi gambar lokal (storage/app/public) ke MinIO...');

    $localPath = storage_path('app/public');
    if (!\Illuminate\Support\Facades\File::exists($localPath)) {
        $this->error('❌ Folder local public storage tidak ditemukan!');
        return;
    }

    $files = \Illuminate\Support\Facades\File::allFiles($localPath);
    $total = count($files);

    if ($total === 0) {
        $this->info('✅ Tidak ada file yang perlu dimigrasi.');
        return;
    }

    $this->info("📦 Menemukan {$total} file untuk diunggah.");
    $bar = $this->output->createProgressBar($total);
    $bar->start();

    $successCount = 0;
    foreach ($files as $file) {
        $relativePath = $file->getRelativePathname();
        
        // Lewati file sistem tersembunyi
        if (str_starts_with(basename($relativePath), '.')) {
            $bar->advance();
            continue;
        }

        try {
            $stream = fopen($file->getRealPath(), 'r');
            \Illuminate\Support\Facades\Storage::disk('public')->put($relativePath, $stream);
            fclose($stream);
            $successCount++;
        } catch (\Exception $e) {
            $this->error("\n❌ Gagal mengunggah {$relativePath}: " . $e->getMessage());
        }

        $bar->advance();
    }

    $bar->finish();
    $this->info("\n🎉 Migrasi selesai! Berhasil mengunggah {$successCount} dari {$total} file ke MinIO!");
})->purpose('Migrate all local public storage files to MinIO');
