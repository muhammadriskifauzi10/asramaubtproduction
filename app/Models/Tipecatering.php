<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipecatering extends Model
{
    use HasFactory;

    protected $table = 'tipe_catering';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'harga_id',
        'nama',
        'jumlah_porsi',
        'pagi',
        'siang',
        'malam',
        'operator_id'
    ];

    public function harga()
    {
        return $this->hasOne(Harga::class, 'id', 'harga_id');
    }
}
