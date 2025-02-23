<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeRange extends Model
{
    protected $fillable = [
        'grading_session_id',
        'grade_name',
        'min_score',
        'max_score',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
    ];

    public function gradingSession(): BelongsTo
    {
        return $this->belongsTo(GradingSession::class);
    }
}
