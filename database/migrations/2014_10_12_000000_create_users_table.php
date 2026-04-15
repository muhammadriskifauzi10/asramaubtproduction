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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id')->default(2);
            $table->unsignedInteger('sso_id')->nullable()->comment('SSO_ID Dari aplikasi SI-Dara'); // SSO_ID Dari aplikasi SI-Dara
            $table->string('identifier')->comment('ini NIM/NIP'); //ini NIM/NIP
            $table->unsignedBigInteger('type')->comment('Tipe user dari SI-Dara, 1 berarti dosen, 2 staff, 3 mahasiswa'); // Tipe user dari SI-Dara, 1 berarti dosen, 2 staff, 3 mahasiswa
            $table->string('name');
            $table->unsignedBigInteger('unit_id')->comment('Satuan kerja/prodi user'); //Satuan kerja/prodi user
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('photo')->nullable()->comment('Photo user'); //path photo user dari aplikasi SI-Dara
            $table->integer('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
