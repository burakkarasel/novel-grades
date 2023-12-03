<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        "title",
        "author"
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // here we defined our local scoped query to fetch books that contains a specific word
    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where("title", "LIKE", "%" . $title . "%");
    }

    public function scopeWithReviewsCount(Builder $query,  $from = null, $to = null): Builder
    {
        return $query->withCount(["reviews" => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)]);
    }

    public function scopeWithRatingAvg(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withAvg(["reviews" => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)], "rating");
    }

    // scopePopular takes date filters for the reviews, gets count of reviews for the books and order by the count descending
    public function scopePopular(Builder $query, $from = null, $to = null) : Builder
    {
        return $query->withReviewsCount($query, $from, $to)
            ->orderBy("reviews_count", "desc");
    }

    // scopeHighestRated takes date filters for the reviews, gets average rating of reviews for the books and order by the average descending
    public function scopeHighestRated(Builder $query, $from = null, $to = null) : Builder
    {
        return $query->withRatingAvg($query, $from, $to)
            ->orderBy("reviews_avg_rating", "desc");
    }

    // scopeMinReviews filter queries with their review count's
    public function scopeMinReviews(Builder $query, int $minReviews): Builder
    {
        return $query->having("reviews_count", ">=", $minReviews);
    }

    // dateRangeFilter adds date filters to our query if the values are given
    private function dateRangeFilter(Builder $query, $from = null, $to = null): void
    {
        if($from && !$to) {
            $query->where("created_at", ">=", $from);
        }else if(!$from && $to) {
            $query->where("created_at", "<=", $to);
        }else if($from && $to) {
            $query->whereBetween("created_at", [$from, $to]);
        }
    }

    public function scopePopularLastMonth(Builder $query): Builder
    {
        return $query->popular(now()->subMonth(), now())
            ->highestRated(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopePopularLastSixMonths(Builder $query): Builder
    {
        return $query->popular(now()->subMonths(6), now())
            ->highestRated(now()->subMonths(6), now())
            ->minReviews(5);
    }

    public function scopeHighestRatedLastMonth(Builder $query): Builder
    {
        return $query->highestRated(now()->subMonth(), now())
            ->popular(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopeHighestRatedLastSixMonths(Builder $query): Builder
    {
        return $query->highestRated(now()->subMonths(6), now())
            ->popular(now()->subMonths(6), now())
            ->minReviews(5);
    }

    protected static function booted()
    {
        static::updated(fn(Book $book) => cache()->forget("book:" . $book->id));
        static::created(fn(Book $book) => cache()->forget("book:" . $book->id));
        static::deleted(fn(Book $book) => cache()->forget("book:" . $book->id));
    }
}
