<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuantarifasrama extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_asrama_tarif';

    protected $primaryKey = 'id';

    protected $fillable = [
        'no_pengajuan',
        'tarif_id',
        'nama_tarif',
        'nominal',
    ];

    // Relasi ke pengajuan asrama
    public function pengajuan()
    {
        return $this->belongsTo(
            Pengajuanasrama::class,
            'no_pengajuan',
            'no_pengajuan'
        );
    }
}
