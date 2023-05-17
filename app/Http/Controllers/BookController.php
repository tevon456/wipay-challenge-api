<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 10);
        $books = $user?->role == 'admin' ? Book::with('sales')->withCount('sales')->paginate($perPage) : Book::paginate($perPage);

        return response()->json($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'author_name' => 'required',
            'title' => 'required',
            'isbn' => 'required|unique:books,isbn',
            'price' => 'required|numeric|min:0',
            'inventory_count' => 'required|integer',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 400);
        }

        $book = Book::create($request->all());

        return response()->json($book, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        $book = $user?->role == 'admin' ? Book::with('sales')->withCount('sales')->find($id) : Book::find($id);

        if (!$book) {
            return response()->json(['errors' => [
                'message' => 'requested resource not found'
            ]], 404);
        }

        return response()->json($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validation = Validator::make($request->all(), [
            'author_name' => 'required',
            'title' => 'required',
            'isbn' => 'required|unique:books,isbn',
            'price' => 'required|numeric|min:0',
            'inventory_count' => 'required|integer',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 400);
        }

        $book = Book::find($id);

        if (!$book) {
            return response()->json(['errors' => [
                'message' => 'requested resource not found'
            ]], 404);
        }

        $book->update($request->all());

        return response()->json($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['errors' => [
                'message' => 'requested resource not found'
            ]], 404);
        }

        $book->delete();

        return response()->json([
            'message' => 'book deleted successfully',
        ]);
    }
}
