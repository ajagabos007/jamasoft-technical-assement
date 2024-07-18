<?php

namespace App\Models;

use App\Observers\WebsiteObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
 
#[ObservedBy([WebsiteObserver::class])]
class Website extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'url',
        'description',
    ];

    /**
     * The categories the website belongs to
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, CategoryWebsite::class, 'website_id', 'category_id');
    }

    /**
     * Get all of the website's votes.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }
}
