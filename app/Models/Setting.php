<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public $timestamps = false;

    protected $fillable = ['key', 'value', 'type', 'group'];

    // ── Static helpers ─────────────────────────────────────────────────────

    /**
     * Récupère la valeur d'un paramètre, mis en cache Redis 24 h.
     *
     * @param  string  $key  Clé du paramètre
     * @param  mixed  $default  Valeur par défaut si introuvable
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting.{$key}", 86400, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            if (! $setting) {
                return $default;
            }

            return match ($setting->type) {
                'boolean' => (bool) $setting->value,
                'integer' => (int) $setting->value,
                'json' => json_decode($setting->value, true),
                default => $setting->value,
            };
        });
    }

    /**
     * Enregistre ou met à jour un paramètre et invalide son cache.
     */
    public static function set(string $key, mixed $value, string $type = 'text', string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : $value, 'type' => $type, 'group' => $group]
        );

        Cache::forget("setting.{$key}");
    }
}
