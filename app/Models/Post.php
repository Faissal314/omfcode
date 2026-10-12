<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category',
        'read_time',
        'image_url',
        'is_featured',
        'views',
        'likes',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'views' => 'integer',
            'likes' => 'integer',
        ];
    }

    /**
     * Get the public-facing URL of the cover image.
     * Use this in Blade views instead of $post->image_url directly
     * so that Filament FileUpload can keep working with the raw path.
     */
    public function imagePublicUrl(): ?string
    {
        $value = $this->getRawOriginal('image_url');

        if (! $value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return asset('storage/'.$value);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }
}
