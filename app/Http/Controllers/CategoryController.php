<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\SlugService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected SlugService $slugService,
    ) {}

    public function index(): View
    {
        return view('categories.index', [
            'categories' => $this->catalog(),
            'parents' => $this->parents(),
        ]);
    }

    public function create(): View
    {
        return $this->index();
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules(null));

        $category = Category::query()->create($this->attributes($validated, null));

        return redirect()
            ->route('categories.index')
            ->with('status', $category->isSubcategory() ? 'Sub-category created successfully.' : 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', [
            'category' => $category,
            'parents' => $this->parents($category),
            'itemCount' => $this->itemCount($category),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate($this->rules($category));

        if ($this->parentId($validated) !== null && $category->children()->exists()) {
            return redirect()
                ->route('categories.edit', $category)
                ->withErrors(['parent_id' => 'This category owns sub-categories, so it cannot become a sub-category itself.']);
        }

        $category->update($this->attributes($validated, $category));

        return redirect()
            ->route('categories.index')
            ->with('status', $category->isSubcategory() ? 'Sub-category updated successfully.' : 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->exists()) {
            return redirect()
                ->route('categories.index')
                ->withErrors(['category' => 'Delete or move the sub-categories in this category before removing it.']);
        }

        if ($category->products()->exists() || $category->subcategoryProducts()->exists()) {
            return redirect()
                ->route('categories.index')
                ->withErrors(['category' => 'Move or delete the items in this category before removing it.']);
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('status', 'Category deleted successfully.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?Category $category): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($category?->id)],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->whereNull('parent_id'),
                Rule::notIn(array_filter([$category?->id])),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function attributes(array $validated, ?Category $category): array
    {
        $slug = Str::slug((string) ($validated['slug'] ?? ''));

        return [
            'parent_id' => $this->parentId($validated),
            'name' => $validated['name'],
            'slug' => $slug !== '' ? $slug : $this->slugService->unique($validated['name'], 'categories', $category?->id),
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function parentId(array $validated): ?int
    {
        $parentId = $validated['parent_id'] ?? null;

        return blank($parentId) ? null : (int) $parentId;
    }

    private function itemCount(Category $category): int
    {
        return $category->isSubcategory()
            ? $category->subcategoryProducts()->count()
            : $category->products()->count();
    }

    /**
     * Top-level categories with their sub-categories and item counts.
     *
     * @return Collection<int, Category>
     */
    private function catalog()
    {
        return Category::query()
            ->withCount(['products', 'subcategoryProducts', 'children'])
            ->with(['children' => fn ($query) => $query->withCount(['products', 'subcategoryProducts'])])
            ->orderBy('name')
            ->get();
    }

    /**
     * Top-level categories that can be picked as a parent.
     *
     * @return Collection<int, Category>
     */
    private function parents(?Category $category = null)
    {
        return Category::query()
            ->parents()
            ->when($category, fn ($query) => $query->whereKeyNot($category->id))
            ->withCount('children')
            ->orderBy('name')
            ->get();
    }
}
