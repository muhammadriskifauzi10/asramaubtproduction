<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Potonganharga extends Model
{
    use HasFactory;

    protected $table = 'potonganharga';
    protected $primaryKey = 'id';

    protected $fillable = [
        'no_invoice',
        'pembayaran_detail_id',
        'potongan_harga',
        'operator_id'
    ];
}
