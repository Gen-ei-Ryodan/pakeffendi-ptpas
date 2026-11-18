<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use SplFileInfo;

class OptimizeImages extends Command
{
    protected $signature = 'optimize:images
        {--dir= : Directory to process (default: storage/app/public/products and storage/app/public/product-images)}
        {--quality=82 : WebP quality (recommended 80-85)}';

    protected $description = 'Compress product images to WebP (quality 80-85) without touching the originals';

    /** @var array<int, string> */
    private const SOURCE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif'];

    public function handle(): int
    {
        $directories = $this->option('dir')
            ? [(string) $this->option('dir')]
            : [storage_path('app/public/products'), storage_path('app/public/product-images')];
        $quality = (int) $this->option('quality');

        if ($quality < 1 || $quality > 100) {
            $this->error('Quality must be between 1 and 100 (recommended 80-85).');

            return self::FAILURE;
        }

        $driver = $this->resolveDriver();
        if ($driver === null) {
            $this->error('Neither GD (with WebP support) nor Imagick is available.');

            return self::FAILURE;
        }

        $converted = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($directories as $directory) {
            if (! is_dir($directory)) {
                $this->warn('Directory not found: '.$directory);

                continue;
            }

            $this->info(sprintf('Using %s driver, quality %d on: %s', $driver, $quality, $directory));

            foreach ($this->sourceFiles($directory) as $file) {
                $target = $this->webpTarget($file->getPathname());

                if ($target === null) {
                    continue;
                }

                // Already converted and up to date — never regenerate needlessly.
                if (is_file($target) && filemtime($target) >= $file->getMTime()) {
                    $skipped++;

                    continue;
                }

                try {
                    if ($driver === 'imagick') {
                        $this->convertWithImagick($file->getPathname(), $target, $quality);
                    } else {
                        $this->convertWithGd($file->getPathname(), $target, $quality);
                    }
                    $converted++;
                    $this->line('  converted: '.$file->getPathname());
                } catch (\Throwable $e) {
                    $failed++;
                    $this->warn('  failed: '.$file->getPathname().' — '.$e->getMessage());
                }
            }
        }

        $this->newLine();
        $this->info(sprintf('Done. Converted: %d, skipped (up-to-date): %d, failed: %d', $converted, $skipped, $failed));
        $this->comment('Originals were left untouched. Product::photo_url now prefers the .webp variant when present.');

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function resolveDriver(): ?string
    {
        if (extension_loaded('imagick') && class_exists(\Imagick::class)) {
            return 'imagick';
        }

        if (extension_loaded('gd') && function_exists('imagewebp')) {
            return 'gd';
        }

        return null;
    }

    /**
     * @return array<int, SplFileInfo>
     */
    private function sourceFiles(string $directory): array
    {
        $iterator = new RegexIterator(
            new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)),
            '/\.(jpe?g|png|gif)$/i'
        );

        $files = [];
        foreach ($iterator as $file) {
            if ($file instanceof SplFileInfo && $file->isFile()) {
                $files[] = $file;
            }
        }

        return $files;
    }

    private function webpTarget(string $path): ?string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (! in_array($extension, self::SOURCE_EXTENSIONS, true)) {
            return null;
        }

        return preg_replace('/\.[^.]+$/', '.webp', $path) ?: null;
    }

    private function convertWithGd(string $source, string $target, int $quality): void
    {
        $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));

        $image = match ($extension) {
            'jpg', 'jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($source) : false,
            'png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($source) : false,
            'gif' => function_exists('imagecreatefromgif') ? @imagecreatefromgif($source) : false,
            default => false,
        };

        if ($image === false) {
            throw new \RuntimeException('Unable to read image (GD support missing for '.$extension.'?).');
        }

        // Keep PNG/GIF transparency in the WebP output.
        if (in_array($extension, ['png', 'gif'], true)) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }

        $ok = @imagewebp($image, $target, $quality);
        imagedestroy($image);

        if ($ok === false || ! is_file($target)) {
            throw new \RuntimeException('Failed writing WebP file.');
        }
    }

    private function convertWithImagick(string $source, string $target, int $quality): void
    {
        $imagick = new \Imagick();
        $imagick->readImage($source);
        $imagick->setImageFormat('webp');
        $imagick->setImageCompressionQuality($quality);
        $ok = $imagick->writeImage($target);
        $imagick->clear();

        if ($ok === false || ! is_file($target)) {
            throw new \RuntimeException('Failed writing WebP file.');
        }
    }
}
