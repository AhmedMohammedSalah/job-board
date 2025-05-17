<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empoloyer extends Model
{
    /** @use HasFactory<\Database\Factories\EmpoloyerFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'company_name',
        'company_logo',
        'company_description',
        'industry',
        'website',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
    public function applications()
    {
        return $this->hasMany(Application::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
