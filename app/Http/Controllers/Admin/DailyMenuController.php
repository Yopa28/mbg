<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyMenu;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DailyMenuController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Menus/Index', [
            'menus' => DailyMenu::with('items.menuItem')
                ->latest('menu_date')
                ->paginate(10),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_date' => ['required', 'date', 'unique:daily_menus,menu_date'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        DailyMenu::create($validated);

        return back()->with('success', 'Menu harian berhasil dibuat.');
    }

    public function update(Request $request, DailyMenu $dailyMenu)
    {
        $validated = $request->validate([
            'menu_date' => [
                'required',
                'date',
                'unique:daily_menus,menu_date,' . $dailyMenu->id,
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $dailyMenu->update($validated);

        return back()->with('success', 'Menu harian berhasil diperbarui.');
    }

    public function publish(DailyMenu $dailyMenu)
    {
        $dailyMenu->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', 'Menu berhasil dipublikasikan.');
    }

    public function unpublish(DailyMenu $dailyMenu)
    {
        $dailyMenu->update([
            'status' => 'draft',
            'published_at' => null,
        ]);

        return back()->with('success', 'Menu dikembalikan menjadi draft.');
    }

    public function destroy(DailyMenu $dailyMenu)
    {
        $dailyMenu->delete();

        return back()->with('success', 'Menu harian berhasil dihapus.');
    }
}
