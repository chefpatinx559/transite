<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    /**
     * Redimensionne (si > $maxWidth) et encode un UploadedFile en WebP sur le disque donné.
     * Retourne le chemin relatif stocké (ex: "posts/abc123.webp"), ou fallback sur store() natif
     * si GD / WebP n'est pas disponible ou si le format n'est pas pris en charge.
     */
    public static function storeAsWebp(
        UploadedFile $file,
        string $directory,
        string $disk = 'public',
        int $maxWidth = 1200,
        int $quality = 80,
    ): string {
        if (! function_exists('imagewebp') || ! function_exists('getimagesize')) {
            return $file->store($directory, $disk);
        }

        $realPath = $file->getRealPath();
        $info = $realPath ? @getimagesize($realPath) : false;
        if (! $info) {
            return $file->store($directory, $disk);
        }

        [$width, $height, $type] = $info;

        $src = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($realPath),
            IMAGETYPE_PNG  => @imagecreatefrompng($realPath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($realPath) : false,
            default        => false,
        };

        if (! $src) {
            return $file->store($directory, $disk);
        }

        if ($width > $maxWidth) {
            $targetWidth  = $maxWidth;
            $targetHeight = (int) max(1, round($height * ($maxWidth / $width)));
            $dst = imagecreatetruecolor($targetWidth, $targetHeight);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
            imagedestroy($src);
        } else {
            $dst = $src;
            imagepalettetotruecolor($dst);
            imagealphablending($dst, true);
            imagesavealpha($dst, true);
        }

        ob_start();
        $ok = @imagewebp($dst, null, $quality);
        $webpBytes = ob_get_clean();
        imagedestroy($dst);

        if (! $ok || ! $webpBytes) {
            return $file->store($directory, $disk);
        }

        $path = trim($directory, '/') . '/' . Str::random(40) . '.webp';
        Storage::disk($disk)->put($path, $webpBytes);

        return $path;
    }
}
