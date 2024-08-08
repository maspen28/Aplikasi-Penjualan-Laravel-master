<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{

    protected $fillable = ['product_id', 'added_stock', 'harga_beli', 'total_beli', 'profit', 'updated_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
