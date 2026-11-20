<?php

namespace Tests\Feature;

use App\Models\DailyMenu;
use App\Models\DailyMenuItem;
use App\Models\Feedback;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuNutritionAndDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_master_nutrition_and_linked_menu_items(): void
    {
        $admin = User::factory()->create();
        $menuItem = MenuItem::create(['name' => 'Nasi Putih']);
        $menu = DailyMenu::create([
            'menu_date' => '2026-11-20',
            'title' => 'Menu Uji',
            'status' => 'draft',
        ]);
        $dailyItem = DailyMenuItem::create([
            'daily_menu_id' => $menu->id,
            'menu_item_id' => $menuItem->id,
            'portion_type' => 'large',
            'quantity' => 1,
            'unit' => 'porsi',
        ]);

        $response = $this->actingAs($admin)->put("/admin/menu-items/{$menuItem->id}", [
            'name' => 'Nasi Putih',
            'category' => 'Makanan Pokok',
            'description' => null,
            'calories' => 204,
            'protein' => 4.2,
            'carbs' => 44.5,
            'fiber' => 1.8,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('menu_items', [
            'id' => $menuItem->id,
            'calories' => 204,
            'protein' => 4.2,
            'carbs' => 44.5,
            'fiber' => 1.8,
        ]);
        $this->assertDatabaseHas('daily_menu_items', [
            'id' => $dailyItem->id,
            'calories' => 204,
            'protein' => 4.2,
            'carbs' => 44.5,
            'fiber' => 1.8,
        ]);
    }

    public function test_admin_can_delete_draft_menu_and_it_removes_related_items(): void
    {
        $admin = User::factory()->create();
        $menuItem = MenuItem::create(['name' => 'Sayur Sop']);
        $menu = DailyMenu::create([
            'menu_date' => '2026-11-20',
            'title' => 'Draft Uji',
            'status' => 'draft',
        ]);
        $dailyItem = DailyMenuItem::create([
            'daily_menu_id' => $menu->id,
            'menu_item_id' => $menuItem->id,
            'portion_type' => 'small',
            'quantity' => 1,
            'unit' => 'porsi',
        ]);
        $feedback = Feedback::create([
            'daily_menu_id' => $menu->id,
            'category' => 'rasa',
            'message' => 'Uji hapus draft',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/menus/{$menu->id}");

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('daily_menus', ['id' => $menu->id]);
        $this->assertDatabaseMissing('daily_menu_items', ['id' => $dailyItem->id]);
        $this->assertDatabaseHas('feedback', ['id' => $feedback->id, 'daily_menu_id' => null]);
    }

    public function test_published_menu_cannot_be_deleted_directly(): void
    {
        $admin = User::factory()->create();
        $menu = DailyMenu::create([
            'menu_date' => '2026-11-20',
            'title' => 'Menu Terbit',
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/menus/{$menu->id}");

        $response->assertSessionHasErrors('menu');
        $this->assertDatabaseHas('daily_menus', ['id' => $menu->id]);
    }

    public function test_negative_nutrition_values_are_rejected(): void
    {
        $admin = User::factory()->create();
        $menuItem = MenuItem::create(['name' => 'Nasi Putih']);

        $response = $this->actingAs($admin)->from('/admin/menu-items')
            ->put("/admin/menu-items/{$menuItem->id}", [
                'name' => 'Nasi Putih',
                'calories' => -1,
                'protein' => 0,
                'carbs' => 0,
                'fiber' => 0,
            ]);

        $response->assertSessionHasErrors(['calories']);
    }
}
