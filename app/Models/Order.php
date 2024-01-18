<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'luggage_type_id',
        'trip_id',
        'product_space',
        'product_value',
        'description',
        'is_insured',
        'created_by_id',
        'updated_by_id',
        'deleted_at',
        'deleted_by_id',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id', 'id');
    }
}
