<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'certifications';
    protected $fillable = [
        'user_id',
        'organization',
        'organization_url',
        'title',
        'issue_date',
        'expiration_date',
        'credential_id',
        'credential_url',
        'credential_file',
        'description',
    ];
    protected $casts = [
        'issue_date' => 'date',
        'expiration_date' => 'date',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
