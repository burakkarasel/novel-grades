@extends("layouts.app")

@section("content")
    <h1 class="mb-10 text-2xl">Add Review for {{ $book->title }}</h1>
    <form action="{{ route("books.reviews.store", $book) }}" method="POST">
        @csrf
        <label for="review">
            Review
        </label>
        <textarea name="review" id="review" required class="input mb-4"></textarea>
        <label for="rating"></label>
        <select name="rating" id="rating" class="input mb-4" required>
            <option value="">Select a rating</option>
            @for($i = 1; $i <= 5; $i++)
                <option value="{{ $i }}">{{ $i }}</option>
            @endfor
        </select>
        <div class="flex gap-2 items-center">
            <button type="submit" class="btn">Add Review</button>
            <a href="{{ route("books.show", $book) }}" class="btn">Cancel</a>
        </div>
    </form>
@endsection
