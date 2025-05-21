<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    protected $fillable = [
    'employer_id',
    'category_id',
    'title',
    'slug',
    'description',
    'responsibilities',
    'requirements',
    'benefits',
    'work_type',      // remote, onsite, hybrid
    'location',
    'min_salary',
    'max_salary',
    'deadline',
    'status',
];



    protected $casts = [
        'technologies' => 'array',
        'deadline' => 'date',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function employer()
    {
        return $this->belongsTo(Empoloyer::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}