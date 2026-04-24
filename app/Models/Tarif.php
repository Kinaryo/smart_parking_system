<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $fillable = [
        'nama',
        'tarif_per_jam',
        'tarif_maksimal'
    ];
}