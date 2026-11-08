<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['subject_type', 'subject_id', 'action', 'payload', 'note', 'status', 'rejection_reason', 'proposed_by', 'reviewed_by', 'reviewed_at'])]
class ActivityChangeRequest extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityChangeRequestFactory> */
    use HasFactory;

    public const TYPE_PROGRAM = 'program';

    public const TYPE_ACTIVITY = 'activity';

    public const ACTION_CREATE = 'create';

    public const ACTION_UPDATE = 'update';

    public const ACTION_DELETE = 'delete';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public function proposedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }
}
