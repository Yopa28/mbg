<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
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
            'photo' => ['nullable', 'string', 'max:255'],
        ]);

        MenuItem::create($validated);

        return back()->with('success', 'Data makanan berhasil ditambahkan.');
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'string', 'max:255'],
        ]);

        $menuItem->update($validated);

        return back()->with('success', 'Data makanan berhasil diperbarui.');
    }

    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->dailyMenuItems()->exists()) {
            return back()->withErrors([
                'menuItem' => 'Makanan masih digunakan dalam menu harian.',
            ]);
        }

        $menuItem->delete();

        return back()->with('success', 'Data makanan berhasil dihapus.');
    }
}
