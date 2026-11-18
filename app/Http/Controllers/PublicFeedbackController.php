<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PublicFeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'daily_menu_id' => [
                'nullable',
                'integer',
                Rule::exists('daily_menus', 'id')->where('status', 'published'),
            ],
            'category' => [
                'required',
                Rule::in(['taste', 'portion', 'cleanliness', 'other']),
            ],
            'name' => ['nullable', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        Feedback::create($validated);

        return back()->with('success', 'Terima kasih! Masukan Anda berhasil dikirim.');
    }
}
