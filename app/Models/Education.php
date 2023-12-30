<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Education extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'educations';
    protected $fillable = [
        'user_id',
        'school',
        'school_url',
        'degree',
        'field_of_study',
        'start_date',
        'end_date',
        'grade',
        'activities',
        'description',
    ];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
