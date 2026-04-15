<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyewa extends Model
{
    use HasFactory;

    protected $table = 'penyewa';
    protected $primaryKey = 'id';

    protected $fillable = [
        'angkatan',
        'namalengkap',
        'noktp',
        'nohp',
        'nim',
        'kip',
        'email',
        'jenis_kelamin',
        'alamat',
        'nama_bill_to',
        'status_asrama',
        'status_catering',
        'operator_id',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'operator_id');
    }
}
