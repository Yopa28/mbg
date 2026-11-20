<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MenuItemController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/MenuItems/Index', [
            'menuItems' => MenuItem::latest()->paginate(10),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'calories' => ['nullable', 'numeric', 'min:0'],
            'protein' => ['nullable', 'numeric', 'min:0'],
            'carbs' => ['nullable', 'numeric', 'min:0'],
            'fiber' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')
                ->store('menu-items', 'public');
        }

        MenuItem::create($validated);

        return back()->with('success', 'Data makanan berhasil ditambahkan.');
    }


public function update(Request $request, MenuItem $menuItem)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'category' => ['nullable', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        'calories' => ['nullable', 'numeric', 'min:0'],
        'protein' => ['nullable', 'numeric', 'min:0'],
        'carbs' => ['nullable', 'numeric', 'min:0'],
        'fiber' => ['nullable', 'numeric', 'min:0'],
    ]);

    $oldPhoto = $menuItem->photo;

    if ($request->hasFile('photo')) {
        $newPhoto = $request->file('photo')->store('menu-items', 'public');

        if (!$newPhoto) {
            return back()->withErrors([
                'photo' => 'Foto gagal diunggah. Silakan coba lagi.',
            ]);
        }

        $validated['photo'] = $newPhoto;
    } else {
        unset($validated['photo']);
    }

    DB::transaction(function () use ($menuItem, $validated, $request) {
        $menuItem->update($validated);

        $menuItem->dailyMenuItems()->update([
            'calories' => $validated['calories'] ?? null,
            'protein' => $validated['protein'] ?? null,
            'carbs' => $validated['carbs'] ?? null,
            'fiber' => $validated['fiber'] ?? null,
        ]);
    });

    if ($request->hasFile('photo') && $oldPhoto) {
        Storage::disk('public')->delete($oldPhoto);
    }

    return back()->with('success', 'Data makanan berhasil diperbarui.');
}


    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->dailyMenuItems()->exists()) {
            return back()->withErrors([
                'menuItem' => 'Makanan masih digunakan dalam menu harian.',
            ]);
        }

        if ($menuItem->photo) {
            Storage::disk('public')->delete($menuItem->photo);
        }

        $menuItem->delete();

        return back()->with('success', 'Data makanan berhasil dihapus.');
    }
}
