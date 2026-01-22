<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%");
            });
        }

        $books = $query
            ->withCount('distributions')
            ->orderBy('title')
            ->paginate(20);

        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        return view('admin.books.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $book = Book::create($validated);

        return redirect()->route('admin.books.show', $book)
            ->with('success', 'Libro creato con successo.');
    }

    public function show(Book $book)
    {
        $book->load(['distributions.student']);
        return view('admin.books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        return view('admin.books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $book->update($validated);

        return redirect()->route('admin.books.index')
            ->with('success', 'Libro aggiornato con successo.');
    }

    public function destroy(Book $book)
    {
        if ($book->distributions()->exists()) {
            return redirect()->route('admin.books.index')
                ->with('error', 'Non puoi eliminare un libro con distribuzioni collegate.');
        }

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Libro eliminato con successo.');
    }
}

