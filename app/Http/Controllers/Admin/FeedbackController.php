<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class FeedbackController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Feedback/Index', [
            'feedbacks' => Feedback::with('dailyMenu')
                ->latest()
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function update(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['new', 'reviewed', 'resolved'])],
        ]);

        $feedback->update($validated);

        return back()->with('success', 'Status ulasan berhasil diperbarui.');
    }

    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
