<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CleaningService extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'cover_image',
        'title',
        'subtitle',
        'left_image',
        'what_we_offer_content',
        'what_we_offer_content_tags',
        'why_choose_us_content',
        'why_choose_us_content_tags',
        'right_image'
    ];

    protected $casts = [
        'what_we_offer_content_tags' => 'array',
        'why_choose_us_content_tags' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
