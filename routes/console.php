<?php

use App\Models\Formation;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('images:optimize', function () {
    if (! function_exists('imagewebp')) {
        $this->error('GD WebP non disponible.');
        return;
    }

    $disk = Storage::disk('public');
    $convertFile = function (?string $relPath, int $maxWidth = 1200, int $quality = 80) use ($disk): ?string {
        if (! $relPath) return $relPath;
        $clean = ltrim(str_replace('/storage/', '', $relPath), '/');
        if (str_ends_with(strtolower($clean), '.webp') || ! $disk->exists($clean)) {
            return $relPath;
        }
        $fullPath = $disk->path($clean);
        $info = @getimagesize($fullPath);
        if (! $info) return $relPath;
        [$w, $h, $type] = $info;
        $src = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($fullPath),
            IMAGETYPE_PNG  => @imagecreatefrompng($fullPath),
            default        => false,
        };
        if (! $src) return $relPath;

        if ($w > $maxWidth) {
            $tw = $maxWidth;
            $th = (int) max(1, round($h * ($maxWidth / $w)));
            $dst = imagecreatetruecolor($tw, $th);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
            imagedestroy($src);
        } else {
            $dst = $src;
            imagepalettetotruecolor($dst);
            imagealphablending($dst, true);
            imagesavealpha($dst, true);
        }

        $webpClean = preg_replace('/\.(jpe?g|png)$/i', '.webp', $clean);
        ob_start();
        $ok = @imagewebp($dst, null, $quality);
        $bytes = ob_get_clean();
        imagedestroy($dst);
        if (! $ok || ! $bytes) return $relPath;

        $disk->put($webpClean, $bytes);
        return str_starts_with($relPath, '/storage/') ? '/storage/' . $webpClean : $webpClean;
    };

    $count = 0;
    foreach (Post::whereNotNull('cover_image')->get() as $post) {
        $new = $convertFile($post->cover_image);
        if ($new !== $post->cover_image) {
            $post->update(['cover_image' => $new]);
            $count++;
        }
    }
    foreach (Formation::whereNotNull('cover_image')->get() as $formation) {
        $new = $convertFile($formation->cover_image);
        if ($new !== $formation->cover_image) {
            $formation->update(['cover_image' => $new]);
            $count++;
        }
    }
    foreach (Product::whereNotNull('images')->get() as $product) {
        $changed = false;
        $imgs = $product->images ?? [];
        foreach ($imgs as $idx => $img) {
            if (is_array($img) && ! empty($img['url'])) {
                $newUrl = $convertFile($img['url']);
                if ($newUrl !== $img['url']) {
                    $imgs[$idx]['url'] = $newUrl;
                    $changed = true;
                }
            } elseif (is_string($img)) {
                $newUrl = $convertFile($img);
                if ($newUrl !== $img) {
                    $imgs[$idx] = $newUrl;
                    $changed = true;
                }
            }
        }
        if ($changed) {
            $product->update(['images' => $imgs]);
            $count++;
        }
    }

    Cache::forget('home.latest_posts');
    $this->info("✅ {$count} enregistrement(s) converti(s) en WebP.");
})->purpose('Convert existing uploaded PNG/JPEG images in storage to WebP');

