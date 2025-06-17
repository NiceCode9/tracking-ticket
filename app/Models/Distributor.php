<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Distributor extends Model
{
    protected $fillable = [
        'npwp',
        'nama',
        'no_telp',
        'alamat',
    ];

    public function fakturs(): HasMany
    {
        return $this->hasMany(Faktur::class);
    }
}
