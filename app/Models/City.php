<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = 'city';
    protected $primarykey='id';

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
