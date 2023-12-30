<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tags';
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];
    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
