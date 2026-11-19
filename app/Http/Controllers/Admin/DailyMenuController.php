<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyMenu;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DailyMenuController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Menus/Index', [
            'menus' => DailyMenu::with('items.menuItem')
                ->latest('menu_date')
                ->paginate(10),

            'menuItems' => MenuItem::orderBy('name')
                ->get(['id', 'name', 'category']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_date' => ['required', 'date', 'unique:daily_menus,menu_date'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.menu_item_id' => [
                'required',
                'integer',
                'exists:menu_items,id',
            ],
            'items.*.portion_type' => [
                'required',
                Rule::in(['large', 'small', 'toddler']),
            ],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit' => ['required', 'string', 'max:50'],
            'items.*.calories' => ['nullable', 'numeric', 'min:0'],
            'items.*.protein' => ['nullable', 'numeric', 'min:0'],
            'items.*.carbs' => ['nullable', 'numeric', 'min:0'],
            'items.*.fat' => ['nullable', 'numeric', 'min:0'],
        ]);

        $items = $validated['items'] ?? [];
        unset($validated['items']);

        $menu = DailyMenu::create($validated);

        foreach ($items as $item) {
            $menu->items()->create($item);
        }

        return back()->with('success', 'Menu harian berhasil dibuat.');
    }

    public function update(Request $request, DailyMenu $dailyMenu)
    {
        $validated = $request->validate([
            'menu_date' => [
                'required',
                'date',
                Rule::unique('daily_menus', 'menu_date')->ignore($dailyMenu->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.menu_item_id' => [
                'required',
                'integer',
                'exists:menu_items,id',
            ],
            'items.*.portion_type' => [
                'required',
                Rule::in(['large', 'small', 'toddler']),
            ],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit' => ['required', 'string', 'max:50'],
            'items.*.calories' => ['nullable', 'numeric', 'min:0'],
            'items.*.protein' => ['nullable', 'numeric', 'min:0'],
            'items.*.carbs' => ['nullable', 'numeric', 'min:0'],
            'items.*.fat' => ['nullable', 'numeric', 'min:0'],
        ]);

        $items = $validated['items'] ?? [];
        unset($validated['items']);

        $dailyMenu->update($validated);

        $dailyMenu->items()->delete();

        foreach ($items as $item) {
            $dailyMenu->items()->create($item);
        }

        return back()->with('success', 'Menu harian berhasil diperbarui.');
    }

    public function publish(DailyMenu $dailyMenu)
    {
        if (!$dailyMenu->items()->exists()) {
            return back()->withErrors([
                'menu' => 'Tambahkan minimal satu makanan sebelum mempublikasikan menu.',
            ]);
        }

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
        if ($dailyMenu->menu_date->lt(today())) {
            return back()->withErrors([
                'menu' => 'Menu yang sudah masuk riwayat harian tidak dapat dihapus.',
            ]);
        }

        $dailyMenu->delete();

        return back()->with('success', 'Menu harian berhasil dihapus.');
    }
}

