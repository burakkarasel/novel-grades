<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // first we get the title from request query
        $title = $request->input("title");
        // get the filters from the request
        $filter = $request->input("filter");
        // then build a query for the books if the title is given or all
        $books = Book::when($title, fn(Builder $query, string $title) => $query->title($title));

        // here we add the filter if any given
        $books = match ($filter) {
            "popular_last_month" => $books->popularLastMonth(),
            "popular_last_six_months" => $books->popularLastSixMonths(),
            "highest_rated_last_month" => $books->highestRatedLastMonth(),
            "highest_rated_last_six_months" => $books->highestRatedLastSixMonths(),
            default => $books->latest()->withRatingAvg()->withReviewsCount()
        };

        // prepare cache key for the search
        // $cacheKey = "books:" . $filter . ":" . $title ;

        // then we get the books we need from the db
        // $books = cache()->remember($cacheKey, 3600, fn() => $books->get());
        $books = $books->get();
        // finally return the view
        return view("books.index", ["books" => $books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // create a cache key for the book
        $cacheKey = "book:" . $id;
        // cache the book with reviews
        /* $book = cache()->remember(
            $cacheKey,
            3600,
            fn() => Book::with([
                "reviews" => fn(Builder $query) => $query->latest()
            ])->withRatingAvg()
                ->withReviewsCount()
                ->findOrFail($id)
        ); */

        $book = Book::with([
            "reviews" => fn(Builder $query) => $query->latest()
        ])
            ->withRatingAvg()
            ->withReviewsCount()
            ->findOrFail($id);

        return view("books.show", ["book" => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
