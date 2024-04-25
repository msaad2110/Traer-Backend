<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    const DOCUMENT_TYPE_PROFILE_PICTURE = 1;

    protected $fillable = [
        'name',
        'deleted_at'
    ];
}
