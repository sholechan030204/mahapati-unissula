<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'planned_start_date', 'planned_end_date', 'actual_start_date', 'actual_end_date', 'status', 'created_by'])]
class WorkProgram extends Model
{
    /** @use HasFactory<\Database\Factories\WorkProgramFactory> */
    use HasFactory;

    public const STATUS_PLANNED = 'planned';

    public const STATUS_ONGOING = 'ongoing';

    public const STATUS_COMPLETED = 'completed';

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function casts(): array
    {
        return [
            'planned_start_date' => 'date',
            'planned_end_date' => 'date',
            'actual_start_date' => 'date',
            'actual_end_date' => 'date',
        ];
    }
}
