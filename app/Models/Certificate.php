<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['member_id', 'activity_id', 'title', 'certificate_number', 'issued_at', 'file_path', 'notes', 'created_by', 'status', 'rejection_reason', 'reviewed_by', 'reviewed_at'])]
class Certificate extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function activity(): BelongsTo { return $this->belongsTo(Activity::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function reviewedBy(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    protected function casts(): array { return ['issued_at' => 'date', 'reviewed_at' => 'datetime']; }
}
