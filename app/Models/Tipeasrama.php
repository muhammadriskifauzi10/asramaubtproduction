<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipeasrama extends Model
{
    use HasFactory;

    protected $table = 'tipe_asrama';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nama',
    ];
}
