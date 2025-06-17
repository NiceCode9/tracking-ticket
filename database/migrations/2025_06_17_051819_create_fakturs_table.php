<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fakturs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_id')->nullable()->constrained('distributors')->onDelete('set null');
            $table->string('no_faktur')->unique();
            $table->date('tgl_faktur');
            $table->date('tgl_jatuh_tempo');
            $table->date('tgl_tanda_terima');
            $table->integer('nominal');
            $table->enum('status', ['0', '1', '2', '3'])->default('0')->comment('0: Belum Terjadwal, 1: Terjadwal, 2: Jadwal Ulang, 3: Terbayar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fakturs');
    }
};
