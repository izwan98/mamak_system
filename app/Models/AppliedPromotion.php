<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppliedPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'promotion_id',
        'promotion_name',
        'discount_amount',
        'affected_items'
    ];

    protected $casts = [
        'affected_items' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
