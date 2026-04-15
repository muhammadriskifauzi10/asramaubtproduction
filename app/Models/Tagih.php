<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagih extends Model
{
    use HasFactory;

    protected $table = 'tagih';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nama'
    ];
}
