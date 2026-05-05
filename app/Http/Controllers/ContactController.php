<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function submit(Request $request): RedirectResponse
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'message' => 'required|string|max:5000',
        ]);

        // TODO: Phase 5 — mail sending

        return back()->with('success', 'Mensaje enviado correctamente.');
    }
}
