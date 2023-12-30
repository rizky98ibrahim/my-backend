<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Experience extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'experiences';
    protected $fillable = [
        'user_id',
        'company',
        'company_url',
        'company_address',
        'job_title',
        'start_date',
        'end_date',
        'is_current',
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

    public function getIsCurrentAttribute($value)
    {
        return $value ? 'Yes' : 'No';
    }
}
