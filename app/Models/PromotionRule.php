<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromotionRule extends Model
{
    use HasFactory;

    protected $fillable = ['promotion_id', 'conditions', 'actions'];

    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
