<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FirstContact;
use Illuminate\Http\Request;

class FirstContactController extends Controller
{
    public function show($token = null)
    {
        $firstContact = null;
        $prefilled = false;
        
        if ($token) {
            $firstContact = FirstContact::where('token', $token)
                ->where('status', 'pending')
                ->first();
            
            if ($firstContact) {
                $prefilled = true;
            }
        }
        
        return view('public.first-contact.form', compact('firstContact', 'prefilled', 'token'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'token' => 'nullable|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);
        
        // Se c'è un token, aggiorna il contatto esistente, altrimenti creane uno nuovo
        if (!empty($validated['token'])) {
            $firstContact = FirstContact::where('token', $validated['token'])
                ->where('status', 'pending')
                ->first();
            
            if ($firstContact) {
                $firstContact->update($validated);
            } else {
                $firstContact = FirstContact::create($validated);
            }
        } else {
            $firstContact = FirstContact::create($validated);
        }
        
        return redirect()->route('public.first-contact.show', ['token' => $firstContact->token])
            ->with('success', 'Grazie per il tuo interesse! Ti contatteremo presto.');
    }
}
