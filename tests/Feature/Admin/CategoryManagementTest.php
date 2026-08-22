<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_view_and_search_categories(): void
    {
        $admin = $this->createUserWithRole('admin');
        $news = Category::factory()->create([
            'name' => 'Product News',
            'description' => 'Latest product updates.',
        ]);
        $guides = Category::factory()->create([
            'name' => 'Guides',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Content structure')
            ->assertSee($news->name)
            ->assertSee($guides->name)
            ->assertSee('href="'.route('admin.categories.create').'"', false);

        $this->actingAs($admin)
            ->get(route('admin.categories.index', ['search' => 'Product']))
            ->assertOk()
            ->assertSee($news->name)
            ->assertDontSee($guides->name);
    }

    public function test_manager_can_open_category_creation_and_edit_forms(): void
    {
        $manager = $this->createUserWithRole('manager');
        $category = Category::factory()->create(['name' => 'Engineering']);

        $this->actingAs($manager)
            ->get(route('admin.categories.create'))
            ->assertOk()
            ->assertSee('Create a category')
            ->assertSee('data-rich-text-editor', false)
            ->assertSee('enctype="multipart/form-data"', false);

        $this->actingAs($manager)
            ->get(route('admin.categories.edit', $category))
            ->assertOk()
            ->assertSee('Edit Engineering')
            ->assertSee('Delete category');
    }

    public function test_manager_can_save_a_formatted_category_description(): void
    {
        $manager = $this->createUserWithRole('manager');
        $description = '<p><strong>Featured</strong> articles.</p><ul><li>Guides</li></ul>';

        $this->actingAs($manager)
            ->post(route('admin.categories.store'), [
                'name' => 'Featured content',
                'description' => $description,
                'slug' => 'featured-content',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'slug' => 'featured-content',
            'description' => $description,
        ]);
    }

    public function test_manager_can_create_a_category_with_a_banner(): void
    {
        Storage::fake('public');

        $manager = $this->createUserWithRole('manager');

        $response = $this->actingAs($manager)
            ->post(route('admin.categories.store'), [
                'name' => '  Product Updates  ',
                'description' => '  News about the product.  ',
                'slug' => 'Product Updates',
                'banner' => UploadedFile::fake()->image('banner.jpg', 1600, 900),
            ]);

        $category = Category::query()->where('slug', 'product-updates')->firstOrFail();

        $response
            ->assertRedirect(route('admin.categories.edit', $category))
            ->assertSessionHas('status', 'Category has been created.');

        $this->assertSame('Product Updates', $category->name);
        $this->assertSame('News about the product.', $category->description);
        $this->assertNotNull($category->banner);
        Storage::disk('public')->assertExists($category->banner);
    }

    public function test_slug_is_generated_from_the_name_when_it_is_empty(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'SEO Guides',
                'description' => '',
                'slug' => '',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'SEO Guides',
            'description' => null,
            'slug' => 'seo-guides',
        ]);
    }

    public function test_category_validation_rejects_a_duplicate_slug_and_invalid_banner(): void
    {
        $admin = $this->createUserWithRole('admin');
        Category::factory()->create(['slug' => 'existing-category']);

        $this->actingAs($admin)
            ->from(route('admin.categories.create'))
            ->post(route('admin.categories.store'), [
                'name' => 'Another Category',
                'slug' => 'existing-category',
                'banner' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('admin.categories.create'))
            ->assertSessionHasErrors(['slug', 'banner']);
    }

    public function test_category_validation_requires_a_16_9_banner(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
            ->from(route('admin.categories.create'))
            ->post(route('admin.categories.store'), [
                'name' => 'Uncropped Banner',
                'slug' => 'uncropped-banner',
                'banner' => UploadedFile::fake()->image('banner.jpg', 1200, 500),
            ])
            ->assertRedirect(route('admin.categories.create'))
            ->assertSessionHasErrors([
                'banner' => 'The banner must have a 16:9 aspect ratio.',
            ]);

        $this->assertDatabaseMissing('categories', ['slug' => 'uncropped-banner']);
    }

    public function test_manager_can_update_a_category_and_replace_its_banner(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('categories/old-banner.jpg', 'old banner');

        $manager = $this->createUserWithRole('manager');
        $category = Category::factory()->create([
            'name' => 'Old name',
            'slug' => 'old-name',
            'banner' => 'categories/old-banner.jpg',
        ]);

        $this->actingAs($manager)
            ->put(route('admin.categories.update', $category), [
                'name' => 'New name',
                'description' => 'Updated description.',
                'slug' => 'new-name',
                'banner' => UploadedFile::fake()->image('new-banner.webp', 1600, 900),
            ])
            ->assertRedirect(route('admin.categories.edit', $category))
            ->assertSessionHas('status', 'Category has been updated.');

        $category->refresh();

        $this->assertSame('New name', $category->name);
        $this->assertSame('new-name', $category->slug);
        $this->assertSame('Updated description.', $category->description);
        Storage::disk('public')->assertMissing('categories/old-banner.jpg');
        Storage::disk('public')->assertExists($category->banner);
    }

    public function test_manager_can_remove_a_category_banner(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('categories/banner.jpg', 'banner');

        $manager = $this->createUserWithRole('manager');
        $category = Category::factory()->create([
            'banner' => 'categories/banner.jpg',
        ]);

        $this->actingAs($manager)
            ->put(route('admin.categories.update', $category), [
                'name' => $category->name,
                'description' => $category->description,
                'slug' => $category->slug,
                'remove_banner' => '1',
            ])
            ->assertSessionHasNoErrors();

        $this->assertNull($category->refresh()->banner);
        Storage::disk('public')->assertMissing('categories/banner.jpg');
    }

    public function test_manager_can_delete_a_category_and_its_banner(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('categories/banner.jpg', 'banner');

        $manager = $this->createUserWithRole('manager');
        $category = Category::factory()->create([
            'banner' => 'categories/banner.jpg',
        ]);

        $this->actingAs($manager)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('status', 'Category has been deleted.');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        Storage::disk('public')->assertMissing('categories/banner.jpg');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createUserWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }
}
