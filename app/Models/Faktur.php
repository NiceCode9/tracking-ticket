<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faktur extends Model
{
    protected $fillable = [
        'distributor_id',
        'no_faktur',
        'tgl_faktur',
        'tgl_jatuh_tempo',
        'tgl_tanda_terima',
        'nominal',
        'status',
    ];

    protected $casts = [
        'tgl_faktur' => 'date',
        'tgl_jatuh_tempo' => 'date',
        'tgl_tanda_terima' => 'date',
        'nominal' => 'integer',
    ];

    const STATUS_BELUM_TERJADWAL = '0';
    const STATUS_TERJADWAL = '1';
    const STATUS_JADWAL_ULANG = '2';
    const STATUS_TERBAYAR = '3';

    public static array $statusLabels = [
        self::STATUS_BELUM_TERJADWAL => 'Belum Terjadwal',
        self::STATUS_TERJADWAL => 'Terjadwal',
        self::STATUS_JADWAL_ULANG => 'Jadwal Ulang',
        self::STATUS_TERBAYAR => 'Terbayar',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? 'Unknown';
    }

    public function isBelumTerjadwal(): bool
    {
        return $this->status === self::STATUS_BELUM_TERJADWAL;
    }

    public function isTerjadwal(): bool
    {
        return $this->status === self::STATUS_TERJADWAL;
    }

    public function isJadwalUlang(): bool
    {
        return $this->status === self::STATUS_JADWAL_ULANG;
    }

    public function isTerbayar(): bool
    {
        return $this->status === self::STATUS_TERBAYAR;
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }
}
