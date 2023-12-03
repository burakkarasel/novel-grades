<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Book;

class Review extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        "review",
        "rating",
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // booted method will run as specified
    protected static function booted()
    {
        // careful with this subscriber if you use Review::where(["id" => ?]).update(["rating" => ?]) it won't catch it
        // because you are not working on the model directly
        // also if you roll back a transaction it won't catch it either
        // but if you fetch the review and manipulate the instance directly it'll catch it
        // $review = Review::findOrFail(["id" => ?])
        // $review->rating = ?
        // $review->save()
        // or
        // $review->update(["rating" => ?])
        // here we invalidate the cache data is manipulated in the DB
        static::updated(fn(Review $review) => cache()->forget("book:" . $review->book_id));
        static::created(fn(Review $review) => cache()->forget("book:" . $review->book_id));
        static::deleted(fn(Review $review) => cache()->forget("book:" . $review->book_id));
    }
}
