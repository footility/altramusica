<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Book;
use App\Models\BookDistribution;
use App\Models\CourseOffering;
use App\Models\Student;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;

class BookDistributionController extends Controller
{
    protected AcademicYearService $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function index(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();
        $query = BookDistribution::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            })->orWhereHas('book', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        } elseif ($currentYear) {
            $query->where('academic_year_id', $currentYear->id);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('book_id')) {
            $query->where('book_id', $request->book_id);
        }

        if ($request->filled('course_offering_id')) {
            $query->where('course_offering_id', $request->course_offering_id);
        }

        $distributions = $query
            ->with(['student', 'book', 'courseOffering.course', 'academicYear'])
            ->orderBy('distribution_date', 'desc')
            ->paginate(20);

        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $books = Book::orderBy('title')->get();
        $offeringsQuery = CourseOffering::with(['course'])->orderByDesc('id');
        if ($currentYear) {
            $offeringsQuery->where('academic_year_id', $currentYear->id);
        }
        $courseOfferings = $offeringsQuery->get();

        return view('admin.book-distributions.index', compact(
            'distributions',
            'years',
            'students',
            'books',
            'courseOfferings',
            'currentYear'
        ));
    }

    public function create(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();

        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $books = Book::orderBy('title')->get();
        $offeringsQuery = CourseOffering::with(['course'])->orderByDesc('id');
        if ($currentYear) {
            $offeringsQuery->where('academic_year_id', $currentYear->id);
        }
        $courseOfferings = $offeringsQuery->get();

        $preselectedStudent = $request->get('student_id') ? Student::find($request->get('student_id')) : null;

        return view('admin.book-distributions.create', compact(
            'years',
            'students',
            'books',
            'courseOfferings',
            'currentYear',
            'preselectedStudent'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'student_id' => 'required|exists:students,id',
            'book_id' => 'required|exists:books,id',
            'course_offering_id' => 'nullable|exists:course_offerings,id',
            'distribution_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'price_paid' => 'required|numeric|min:0',
        ]);

        if (empty($validated['academic_year_id'])) {
            $currentYear = $this->academicYearService->getCurrent();
            $validated['academic_year_id'] = $currentYear?->id;
        }

        $distribution = BookDistribution::create($validated);

        // Simple stock update (Phase 1)
        $book = Book::find($validated['book_id']);
        if ($book) {
            $book->decrement('stock_quantity', $validated['quantity']);
        }

        return redirect()->route('admin.book-distributions.show', $distribution)
            ->with('success', 'Distribuzione libro registrata.');
    }

    public function show(BookDistribution $bookDistribution)
    {
        $bookDistribution->load(['student', 'book', 'courseOffering.course', 'academicYear']);
        return view('admin.book-distributions.show', ['distribution' => $bookDistribution]);
    }

    public function edit(BookDistribution $bookDistribution)
    {
        $currentYear = $this->academicYearService->getCurrent();

        $bookDistribution->load(['student', 'book', 'courseOffering.course', 'academicYear']);

        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $books = Book::orderBy('title')->get();
        $offeringsQuery = CourseOffering::with(['course'])->orderByDesc('id');
        if ($currentYear) {
            $offeringsQuery->where('academic_year_id', $currentYear->id);
        }
        $courseOfferings = $offeringsQuery->get();

        return view('admin.book-distributions.edit', [
            'distribution' => $bookDistribution,
            'years' => $years,
            'students' => $students,
            'books' => $books,
            'courseOfferings' => $courseOfferings,
            'currentYear' => $currentYear,
        ]);
    }

    public function update(Request $request, BookDistribution $bookDistribution)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'student_id' => 'required|exists:students,id',
            'book_id' => 'required|exists:books,id',
            'course_offering_id' => 'nullable|exists:course_offerings,id',
            'distribution_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'price_paid' => 'required|numeric|min:0',
        ]);

        if (empty($validated['academic_year_id'])) {
            $currentYear = $this->academicYearService->getCurrent();
            $validated['academic_year_id'] = $currentYear?->id;
        }

        // Adjust stock delta if book/quantity changes
        $oldBookId = $bookDistribution->book_id;
        $oldQty = (int) $bookDistribution->quantity;

        $bookDistribution->update($validated);

        $newBookId = (int) $validated['book_id'];
        $newQty = (int) $validated['quantity'];

        if ($oldBookId === $newBookId) {
            $delta = $newQty - $oldQty;
            if ($delta !== 0) {
                Book::whereKey($newBookId)->decrement('stock_quantity', $delta);
            }
        } else {
            Book::whereKey($oldBookId)->increment('stock_quantity', $oldQty);
            Book::whereKey($newBookId)->decrement('stock_quantity', $newQty);
        }

        return redirect()->route('admin.book-distributions.index')
            ->with('success', 'Distribuzione aggiornata.');
    }

    public function destroy(BookDistribution $bookDistribution)
    {
        // restore stock
        Book::whereKey($bookDistribution->book_id)->increment('stock_quantity', (int) $bookDistribution->quantity);

        $bookDistribution->delete();

        return redirect()->route('admin.book-distributions.index')
            ->with('success', 'Distribuzione eliminata.');
    }
}

