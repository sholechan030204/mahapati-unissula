<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value'])]
class SiteSetting extends Model
{
    /** @return array<string, string|null> */
    public static function values(): array
    {
        return static::query()->pluck('value', 'key')->all();
    }

    /** @param array<string, string|null> $values */
    public static function saveValues(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
