<?php

namespace App\Observers;

use App\Models\Faktur;
use App\Models\LogFaktur;

class FakturObserver
{
    /**
     * Handle the Faktur "created" event.
     */
    public function created(Faktur $faktur): void
    {
        //
    }

    /**
     * Handle the Faktur "updated" event.
     */
    public function updated(Faktur $faktur): void
    {
        // if ($faktur->isDirty('status')) {
        //     LogFaktur::create([
        //         'faktur_id' => $faktur->id,
        //         'status' => $faktur->status,
        //         'keterangan' => 'Status berubah dari ' .
        //             Faktur::$statusLabels[$faktur->getOriginal('status')] . ' menjadi ' .
        //             $faktur->status_label,
        //         'user_id' => auth()->id()
        //     ]);
        // }
    }

    /**
     * Handle the Faktur "deleted" event.
     */
    public function deleted(Faktur $faktur): void
    {
        //
    }

    /**
     * Handle the Faktur "restored" event.
     */
    public function restored(Faktur $faktur): void
    {
        //
    }

    /**
     * Handle the Faktur "force deleted" event.
     */
    public function forceDeleted(Faktur $faktur): void
    {
        //
    }
}
