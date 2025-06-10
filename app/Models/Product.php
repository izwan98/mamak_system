<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Product extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'price'];

    public function getFormattedPriceAttribute()
    {
        return 'RM' . number_format($this->price, 2);
    }
}
