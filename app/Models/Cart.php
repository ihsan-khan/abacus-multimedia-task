<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
    ];

    // Get or create cart for user
    public static function getCart($user_id)
    {
        return static::firstOrCreate(['user_id' => $user_id]);
    }
}
