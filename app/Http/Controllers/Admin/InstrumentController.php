<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instrument;
use App\Models\InstrumentRental;
use Illuminate\Http\Request;

class InstrumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Instrument::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $instruments = $query->withCount('rentals')
            ->orderBy('type')
            ->orderBy('brand')
            ->paginate(20);

        return view('admin.instruments.index', compact('instruments'));
    }

    public function create()
    {
        return view('admin.instruments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'serial_number' => 'nullable|string|max:255|unique:instruments,serial_number',
            'condition' => 'nullable|in:excellent,good,fair,poor',
            'supplier' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,rented,sold,maintenance,retired',
            'notes' => 'nullable|string',
        ]);

        Instrument::create($validated);

        return redirect()->route('admin.instruments.index')
            ->with('success', 'Strumento creato con successo.');
    }

    public function show(Instrument $instrument)
    {
        $instrument->load(['rentals.student']);
        return view('admin.instruments.show', compact('instrument'));
    }

    public function edit(Instrument $instrument)
    {
        return view('admin.instruments.edit', compact('instrument'));
    }

    public function update(Request $request, Instrument $instrument)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'serial_number' => 'nullable|string|max:255|unique:instruments,serial_number,' . $instrument->id,
            'condition' => 'nullable|in:excellent,good,fair,poor',
            'supplier' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,rented,sold,maintenance,retired',
            'notes' => 'nullable|string',
        ]);

        $instrument->update($validated);

        return redirect()->route('admin.instruments.index')
            ->with('success', 'Strumento aggiornato con successo.');
    }

    public function destroy(Instrument $instrument)
    {
        if ($instrument->rentals()->where('status', 'active')->exists()) {
            return redirect()->route('admin.instruments.index')
                ->with('error', 'Non puoi eliminare uno strumento con noleggi attivi.');
        }

        $instrument->delete();

        return redirect()->route('admin.instruments.index')
            ->with('success', 'Strumento eliminato con successo.');
    }
}
