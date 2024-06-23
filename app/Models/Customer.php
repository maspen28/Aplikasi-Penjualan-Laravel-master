<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable {
  use HasFactory, Notifiable;

  protected $fillable = [
    'email',
    'password',
    'username',
    'name',
    'address',
    'citie_id',
    'district_id',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  // Relationships
  public function district() {
    return $this->belongsTo(District::class);
  }

  public function cart() {
    return $this->hasMany(Cart::class);
  }

  public function order() {
    return $this->hasMany(Order::class);
  }
}
