<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model {
  protected $table = 'carts';

  // Jika Anda ingin memperbolehkan mass assignment, tambahkan fillable properties
  protected $fillable = ['id', 'customer_id', 'product_id', 'qty'];

  // Relasi dengan model Product
  public function product() {
    return $this->belongsTo(Product::class);
  }

  // Relasi dengan model Customer
  public function customer() {
    return $this->belongsTo(Customer::class);
  }
}
