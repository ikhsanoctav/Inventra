<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper method to easily create audit log entries.
     */
    public static function record(string $action, ?string $entityType = null, ?int $entityId = null, $oldValues = null, $newValues = null): ?self
    {
        try {
            return self::create([
                'tenant_id' => auth()->user()->tenant_id ?? 1,
                'user_id' => auth()->id(),
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'old_values' => is_array($oldValues) ? $oldValues : ($oldValues ? (array) $oldValues : null),
                'new_values' => is_array($newValues) ? $newValues : ($newValues ? (array) $newValues : null),
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
