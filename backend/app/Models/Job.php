<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    /** @use HasFactory<\Database\Factories\JobsPostFactory> */
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
        'work_type', // 'remote', 'onsite', 'hybrid'
        'location',
        'min_salary',
        'max_salary',
        'deadline',
        'status' // 'draft', 'pending', 'published', 'expired'
    ];
}
