<?php

namespace App\Http\Controllers;

use App\Models\DailyMenu;
use App\Models\Sppg;
use Inertia\Inertia;

class PublicMenuController extends Controller
{
    public function index()
    {
        $menu = DailyMenu::where('status', 'published')
            ->with('items.menuItem')
            ->latest('menu_date')
            ->first();

        return Inertia::render('Public/Menu/Index', [
            'sppg' => Sppg::first(),
            'menu' => $menu,
        ]);
    }

    public function show(DailyMenu $dailyMenu)
    {
        abort_unless($dailyMenu->status === 'published', 404);

        $dailyMenu->load('items.menuItem');

        return Inertia::render('Public/Menu/Show', [
            'sppg' => Sppg::first(),
            'menu' => $dailyMenu,
        ]);
    }
}
