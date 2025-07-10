<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReview extends Model
{
    use HasFactory;
    protected $fillable = [
        'author_name',
        'profile_photo_url',
        'rating',
        'text',
    ];
}
