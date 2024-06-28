<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAccount extends Model {
    use HasFactory;

    protected $table = 'customers';
    protected $primarykey='id';

  protected $fillable = [
    'name', 'email', 'phone', 'address',
  ];

  // Jika ada relasi dengan model lain, definisikan di sini
}
