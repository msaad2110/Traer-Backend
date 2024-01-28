<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'luggage_type_id',
        'travelling_from',
        'travelling_to',
        'start_date',
        'end_date',
        'luggage_space',
        'commission',
        'created_by_id',
        'updated_by_id',
        'deleted_at',
        'deleted_by_id',
    ];

    public function luggage_type()
    {
        return $this->belongsTo(LuggageType::class, 'luggage_type_id', 'id');
    }
}
