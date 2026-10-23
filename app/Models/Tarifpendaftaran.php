<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarifpendaftaran extends Model
{
    use HasFactory;

    protected $table = 'tarif_pendaftaran';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'jenis',
        'kategori',
        'nominal',
        'status',
        'operator_id',
    ];
}
