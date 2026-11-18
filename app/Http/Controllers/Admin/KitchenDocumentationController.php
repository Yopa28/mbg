<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyMenu;
use App\Models\KitchenDocumentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class KitchenDocumentationController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/KitchenPhotos/Index', [
            'documentations' => KitchenDocumentation::with('dailyMenu')
                ->latest()
                ->paginate(10),

            'dailyMenus' => DailyMenu::orderByDesc('menu_date')
                ->get(['id', 'title', 'menu_date', 'status']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'daily_menu_id' => ['required', 'exists:daily_menus,id'],
            'category' => [
                'required',
                Rule::in(['preparation', 'cooking', 'temperature_check', 'serving']),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'temperature' => [
                'nullable',
                'required_if:category,temperature_check',
                'numeric',
                'between:-50,300',
            ],
            'captured_at' => ['nullable', 'date'],
            'is_published' => ['required', 'boolean'],
        ]);

        $photoPath = $request->file('photo')->store('kitchen-documentations', 'public');

        if (!$photoPath) {
            return back()->withErrors([
                'photo' => 'Foto gagal diunggah. Silakan coba lagi.',
            ]);
        }

        $validated['photo_path'] = $photoPath;
        unset($validated['photo']);

        KitchenDocumentation::create($validated);

        return back()->with('success', 'Dokumentasi berhasil ditambahkan.');
    }

    public function update(Request $request, KitchenDocumentation $kitchenDocumentation)
    {
        $validated = $request->validate([
            'daily_menu_id' => ['required', 'exists:daily_menus,id'],
            'category' => [
                'required',
                Rule::in(['preparation', 'cooking', 'temperature_check', 'serving']),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'temperature' => [
                'nullable',
                'required_if:category,temperature_check',
                'numeric',
                'between:-50,300',
            ],
            'captured_at' => ['nullable', 'date'],
            'is_published' => ['required', 'boolean'],
        ]);

        $oldPhoto = $kitchenDocumentation->photo_path;

        if ($request->hasFile('photo')) {
            $newPhoto = $request->file('photo')->store('kitchen-documentations', 'public');

            if (!$newPhoto) {
                return back()->withErrors([
                    'photo' => 'Foto gagal diunggah. Silakan coba lagi.',
                ]);
            }

            $validated['photo_path'] = $newPhoto;
        }

        unset($validated['photo']);

        $kitchenDocumentation->update($validated);

        if ($request->hasFile('photo') && $oldPhoto) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return back()->with('success', 'Dokumentasi berhasil diperbarui.');
    }

    public function togglePublication(KitchenDocumentation $kitchenDocumentation)
    {
        $kitchenDocumentation->update([
            'is_published' => !$kitchenDocumentation->is_published,
        ]);

        return back()->with(
            'success',
            $kitchenDocumentation->is_published
                ? 'Dokumentasi berhasil dipublikasikan.'
                : 'Dokumentasi dikembalikan menjadi draft.'
        );
    }

    public function destroy(KitchenDocumentation $kitchenDocumentation)
    {
        if ($kitchenDocumentation->photo_path) {
            Storage::disk('public')->delete($kitchenDocumentation->photo_path);
        }

        $kitchenDocumentation->delete();

        return back()->with('success', 'Dokumentasi berhasil dihapus.');
    }
}
