<?php
// app/Models/Discount.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'discount_name', 'besar_diskon'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'discount_id');
    }
}
