<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['user_id', 'action', 'description', 'auditable_type', 'auditable_id', 'ip_address'])]
class AuditLog extends Model
{
    /**
     * Admin yang melakukan aktivitas.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Objek yang menjadi subjek aktivitas (opsional).
     *
     * @return MorphTo<AuditLog, $this>
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Catat aktivitas penting admin ke audit log.
     */
    public static function record(
        string $action,
        ?string $description = null,
        ?Model $auditable = null,
    ): static {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'ip_address' => request()?->ip(),
        ]);
    }
}
