<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Review;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // seed books with good rating
        Book::factory(33)->create()->each(function(Book $book) {
            $numReviews = random_int(5, 30);
            Review::factory()->count($numReviews)
                ->good()
                ->for($book)
                ->create();
        });

        // seed books with bad rating
        Book::factory(33)->create()->each(function(Book $book) {
            $numReviews = random_int(5, 30);
            Review::factory()->count($numReviews)
                ->bad()
                ->for($book)
                ->create();
        });

        // seed books with average rating
        Book::factory(34)->create()->each(function(Book $book) {
            $numReviews = random_int(5, 30);
            Review::factory()->count($numReviews)
                ->average()
                ->for($book)
                ->create();
        });
    }
}
