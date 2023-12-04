<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class ReviewController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Book $book)
    {
        return view("books.reviews.create", ["book" => $book]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Book $book)
    {
        $data = $request->validate([
            "review" => "required | min:15",
            "rating" => "required | min:1 | max:5 | integer"
        ]);

        $book->reviews()->create($data);
        return redirect()->route("books.show", ["book" => $book]);
    }
}
