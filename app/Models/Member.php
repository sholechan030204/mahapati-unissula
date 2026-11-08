<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[\Illuminate\Database\Eloquent\Attributes\Fillable(['user_id', 'full_name', 'alam_name', 'nra', 'nim', 'membership_type', 'status', 'position', 'profile_photo', 'address', 'phone'])]
class Member extends Model
{
    /** @use HasFactory<\Database\Factories\MemberFactory> */
    use HasFactory;

    public const TYPE_PERMANENT = 'anggota_tetap';

    public const TYPE_SPECIAL = 'anggota_istimewa';

    public const STATUS_ACTIVE = 'aktif';

    public const STATUS_INACTIVE = 'nonaktif';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function changes(): HasMany
    {
        return $this->hasMany(MemberChangeLog::class)->latest();
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_member')->withTimestamps();
    }
}
