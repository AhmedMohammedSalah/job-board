<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteJob extends Model
{
    /** @use HasFactory<\Database\Factories\FavoriteJobFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'job_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
