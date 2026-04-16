<?php

namespace App\Livewire\Admin;

use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Livewire\Component;

class ProductCategoryQuickCreate extends Component
{
    public string $name = '';

    public string $tag = '';

    public ?string $statusMessage = null;

    public function createCategory(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:product_categories,name'],
            'tag' => ['nullable', 'string', 'max:255'],
        ]);

        $baseSlug = Str::slug($validated['name']) ?: 'category';
        $slug = $baseSlug;
        $counter = 2;

        while (ProductCategory::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        $category = ProductCategory::query()->create([
            'name' => $validated['name'],
            'slug' => $slug,
            'tag' => blank($validated['tag']) ? null : $validated['tag'],
            'is_active' => true,
        ]);

        $this->reset(['name', 'tag']);
        $this->resetValidation();
        $this->statusMessage = 'Category created and selected.';

        $this->dispatch('product-category-created', categoryId: $category->id, categoryName: $category->name);
    }

    public function render()
    {
        return view('livewire.admin.product-category-quick-create');
    }
}

