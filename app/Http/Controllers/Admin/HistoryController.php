<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyMenu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
        ]);

        $today = today()->toDateString();

        $historyQuery = DailyMenu::query()
            ->whereDate('menu_date', '<', $today);

        $totalHistory = (clone $historyQuery)->count();
        $publishedHistory = (clone $historyQuery)
            ->where('status', 'published')
            ->count();

        $menus = (clone $historyQuery)
            ->withCount('items')
            ->when(
                $filters['date_from'] ?? null,
                fn ($query, $date) => $query->whereDate('menu_date', '>=', $date)
            )
            ->when(
                $filters['date_to'] ?? null,
                fn ($query, $date) => $query->whereDate('menu_date', '<=', $date)
            )
            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) => $query->where('status', $status)
            )
            ->orderByDesc('menu_date')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/History/Index', [
            'menus' => $menus,
            'filters' => [
                'date_from' => $filters['date_from'] ?? '',
                'date_to' => $filters['date_to'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'summary' => [
                'total' => $totalHistory,
                'published' => $publishedHistory,
                'draft' => $totalHistory - $publishedHistory,
            ],
        ]);
    }

    public function show(DailyMenu $dailyMenu)
    {
        abort_unless(
            $dailyMenu->menu_date->lt(today()),
            404
        );

        $dailyMenu->load([
            'items.menuItem',
            'kitchenDocumentations',
        ]);

        return Inertia::render('Admin/History/Show', [
            'dailyMenu' => $dailyMenu,
        ]);
    }
}
