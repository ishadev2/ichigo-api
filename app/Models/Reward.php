<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
  protected $fillable = [
    'user_id', 'availableAt', 'expiresAt', 'redeemedAt'
  ];

  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }
}
