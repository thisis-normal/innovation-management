<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradingSession extends Model
{
    protected $fillable = [
        'year',
        'session_number',
        'description',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'year' => 'integer',
        'session_number' => 'integer',
    ];

    public function gradeRanges(): HasMany
    {
        return $this->hasMany(GradeRange::class);
    }

    protected static function boot(): void
    {
        parent::boot();
        // When a session is deleted, reorder remaining sessions
        static::deleted(function ($session) {
            // Get all sessions for the same year with higher session numbers
            static::query()->where('year', $session->year)
                ->where('session_number', '>', $session->session_number)
                ->orderBy('session_number')
                ->get()
                ->each(function ($s) {
                    $s->session_number = $s->session_number - 1;
                    $s->save();
                });
        });


        static::creating(function ($session) {
            if (!$session->session_number) {
                // Find the real next session number after any reordering
                $maxSession = static::query()->where('year', $session->year)
                    ->max('session_number');
                $session->session_number = ($maxSession ?? 0) + 1;
            }
        });
    }
}
