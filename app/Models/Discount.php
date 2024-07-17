<?php
// app/Models/Discount.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = ['discount_name', 'product_id', 'besar_diskon'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
