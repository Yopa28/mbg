
<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DailyMenuController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\SppgController;
use App\Http\Controllers\PublicMenuController;
use App\Http\Controllers\Admin\KitchenDocumentationController;
use Illuminate\Support\Facades\Route;


// Halaman publik
Route::get('/', [PublicMenuController::class, 'index'])
    ->name('public.menu.index');

Route::get('/menu/{dailyMenu}', [PublicMenuController::class, 'show'])
    ->name('public.menu.show');

// Halaman admin - wajib login
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/sppg', [SppgController::class, 'edit'])
            ->name('sppg.edit');

        Route::put('/sppg', [SppgController::class, 'update'])
            ->name('sppg.update');

        Route::get('/menus', [DailyMenuController::class, 'index'])
            ->name('menus.index');

        Route::post('/menus', [DailyMenuController::class, 'store'])
            ->name('menus.store');

        Route::put('/menus/{dailyMenu}', [DailyMenuController::class, 'update'])
            ->name('menus.update');

        Route::patch('/menus/{dailyMenu}/publish', [DailyMenuController::class, 'publish'])
            ->name('menus.publish');

        Route::patch('/menus/{dailyMenu}/unpublish', [DailyMenuController::class, 'unpublish'])
            ->name('menus.unpublish');

        Route::delete('/menus/{dailyMenu}', [DailyMenuController::class, 'destroy'])
            ->name('menus.destroy');

        Route::get('/menu-items', [MenuItemController::class, 'index'])
            ->name('menu-items.index');

        Route::post('/menu-items', [MenuItemController::class, 'store'])
            ->name('menu-items.store');

        Route::put('/menu-items/{menuItem}', [MenuItemController::class, 'update'])
            ->name('menu-items.update');

        Route::delete('/menu-items/{menuItem}', [MenuItemController::class, 'destroy'])
            ->name('menu-items.destroy');

        Route::get('/kitchen-photos', [KitchenDocumentationController::class, 'index'])
            ->name('kitchen-photos.index');

        Route::post('/kitchen-photos', [KitchenDocumentationController::class, 'store'])
            ->name('kitchen-photos.store');

        Route::put('/kitchen-photos/{kitchenDocumentation}', [KitchenDocumentationController::class, 'update'])
            ->name('kitchen-photos.update');

        Route::patch('/kitchen-photos/{kitchenDocumentation}/publication', [KitchenDocumentationController::class, 'togglePublication'])
            ->name('kitchen-photos.publication');

        Route::delete('/kitchen-photos/{kitchenDocumentation}', [KitchenDocumentationController::class, 'destroy'])
            ->name('kitchen-photos.destroy');
    });

