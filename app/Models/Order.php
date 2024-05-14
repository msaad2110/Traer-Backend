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
        'customer_id',
        'tracking_number',
        'product_space',
        'product_value',
        'description',
        'is_insured',
        'status',
        'created_by_id',
        'updated_by_id',
        'deleted_at',
        'deleted_by_id',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id', 'id');
    }
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id', 'id');
    }

    public static function getNextCode()
    {
        //loop until a unique code is not created
        do {
            //generate a code with random integers
            $code = random_int(1000000000, 9999999999);
        } while (self::where('tracking_number', $code)->first()); // check if the code already exists or not

        return $code;
    }
}
