<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyMenu;
use App\Models\MenuItem;
use App\Models\Sppg;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'sppg' => Sppg::first(),
            'stats' => [
                'total_menus' => DailyMenu::count(),
                'published_menus' => DailyMenu::where('status', 'published')->count(),
                'draft_menus' => DailyMenu::where('status', 'draft')->count(),
                'total_foods' => MenuItem::count(),
            ],
            'recentMenus' => DailyMenu::latest('menu_date')
                ->limit(5)
                ->get(),
        ]);
    }
}
