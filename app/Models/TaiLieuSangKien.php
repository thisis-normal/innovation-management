<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaiLieuSangKien extends Model
{
    use HasFactory;

    protected $table = 'tai_lieu_sang_kien';

    protected $fillable = [
        'sang_kien_id',
        'file_path'
    ];

    public function sangKien()
    {
        return $this->belongsTo(SangKien::class, 'sang_kien_id');
    }
}
