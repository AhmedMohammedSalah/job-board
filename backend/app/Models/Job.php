<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'responsibilities',
        'requirements',
        'category',
        'location',
        'technologies',
        'work_type',
        'employment_type',
        'salary_min',
        'salary_max',
        'benefits',
        'deadline',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'technologies' => 'array',
        'deadline' => 'date',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function comments()
    {
        return $this->hasMany(JobComment::class);
    }
}
    