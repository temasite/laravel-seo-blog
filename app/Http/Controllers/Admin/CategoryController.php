<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $categories = Category::query()
            ->search($filters['search'] ?? null)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $category = Category::createWithBanner(
            $request->safe()->except('banner'),
            $request->file('banner'),
        );

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('status', 'Category has been created.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->updateWithBanner(
            $request->safe()->except(['banner', 'remove_banner']),
            $request->file('banner'),
            $request->boolean('remove_banner'),
        );

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('status', 'Category has been updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->deleteWithBanner();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Category has been deleted.');
    }
}
