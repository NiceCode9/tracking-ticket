<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogFaktur extends Model
{
    protected $table = 'log_faktur';
    protected $fillable = [
        'faktur_id',
        'status',
        'keterangan',
        'user_id'
    ];

    public function faktur(): BelongsTo
    {
        return $this->belongsTo(Faktur::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return Faktur::$statusLabels[$this->status] ?? 'Unknown';
    }
}
