<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['subtotal', 'discount', 'total'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function appliedPromotions()
    {
        return $this->hasMany(AppliedPromotion::class);
    }

    public function getFormattedTotalAttribute()
    {
        return 'RM' . number_format($this->total, 2);
    }
}
