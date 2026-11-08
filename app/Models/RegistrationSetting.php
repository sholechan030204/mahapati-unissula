<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationSetting extends Model
{
    protected $fillable = [
        'is_open', 'opens_at', 'closes_at', 'interview_starts_at', 'interview_ends_at',
        'announcement_at', 'quota', 'fee', 'whatsapp_group_link', 'pamphlet_path', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
            'opens_at' => 'date',
            'closes_at' => 'date',
            'interview_starts_at' => 'date',
            'interview_ends_at' => 'date',
            'announcement_at' => 'date',
            'quota' => 'integer',
            'fee' => 'integer',
        ];
    }

    /** @return array<string, mixed> */
    public static function publicData(): array
    {
        $defaults = config('registration');
        $setting = static::query()->first();

        if ($setting === null) {
            return $defaults;
        }

        return array_merge($defaults, [
            'is_open' => $setting->is_open,
            'opens_at' => $setting->opens_at?->toDateString(),
            'closes_at' => $setting->closes_at?->toDateString(),
            'interview_starts_at' => $setting->interview_starts_at?->toDateString(),
            'interview_ends_at' => $setting->interview_ends_at?->toDateString(),
            'announcement_at' => $setting->announcement_at?->toDateString(),
            'quota' => $setting->quota,
            'fee' => $setting->fee,
            'whatsapp_group_link' => $setting->whatsapp_group_link,
            'pamphlet_path' => $setting->pamphlet_path,
        ]);
    }
}
