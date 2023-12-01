<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

class Book extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        "title",
        "author"
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // here we defined our local scoped query to fetch books that contains a specific word
    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where("title", "LIKE", "%" . $title . "%");
    }

    // scopePopular takes date filters for the reviews, gets count of reviews for the books and order by the count descending
    public function scopePopular(Builder $query, $from = null, $to = null) : Builder
    {
        return $query->withCount(["reviews" => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)])
            ->orderBy("reviews_count", "desc");
    }

    // scopeHighestRated takes date filters for the reviews, gets average rating of reviews for the books and order by the average descending
    public function scopeHighestRated(Builder $query, $from = null, $to = null) : Builder
    {
        return $query->withAvg(["reviews" => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)], "rating")
            ->orderBy("reviews_avg_rating", "desc");
    }

    // scopeMinReviews filter queries with their review count's
    public function scopeMinReviews(Builder $query, int $minReviews): Builder
    {
        return $query->having("reviews_count", ">=", $minReviews);
    }

    // dateRangeFilter adds date filters to our query if the values are given
    private function dateRangeFilter(Builder $query, $from = null, $to = null)
    {
        if($from && !$to) {
            $query->where("created_at", ">=", $from);
        }else if(!$from && $to) {
            $query->where("created_at", "<=", $to);
        }else if($from && $to) {
            $query->whereBetween("created_at", [$from, $to]);
        }
    }
}
