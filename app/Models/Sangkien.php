<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sangkien extends Model
{
    use HasFactory;

    protected $table = 'sangkiens';

    protected $fillable = [
        'title',
        'description',
        'status',
        'author_id',
        'category',
        'submitted_date',
        'approved_date'
    ];

    protected $casts = [
        'submitted_date' => 'datetime',
        'approved_date' => 'datetime'
    ];
}
