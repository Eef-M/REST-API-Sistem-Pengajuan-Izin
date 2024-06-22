<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    use HasFactory;

    protected $fillable = [
        'komentar',
        'izin_id',
        'user_id',
    ];

    public function izin()
    {
        return $this->belongsTo(Izin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
