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
    Schema::create('staffs', function (Blueprint $table) {
        $table->id();
        $table->string('nip')->unique();
        $table->string('nama');
        $table->string('jabatan');
        $table->string('alamat')->nullable();
        $table->string('nomor_telepon')->nullable();
        $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
        $table->string('id_template');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('staffs');
    }
};
