<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class QuoteRequestController extends Controller
{
    public function create()
    {
        return view('pages.quote');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:30',
            'company' => 'nullable|string|max:255',
            'project_type' => 'nullable|string|max:100',
            'description' => 'required|string|max:3000',
            'budget' => 'nullable|string|max:50',
        ]);

        QuoteRequest::create($data);

        return back()->with('success', 'Votre demande de devis a bien été envoyée ! Nous vous répondrons sous 48h.');
    }
}
