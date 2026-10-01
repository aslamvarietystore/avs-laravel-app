<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(
        protected SlugService $slugService,
    ) {}

    public function index(): View
    {
        return view('brands.index', [
            'brands' => Brand::query()->withCount('products')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return $this->index();
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules(null));

        Brand::query()->create($this->attributes($validated, null));

        return redirect()
            ->route('brands.index')
            ->with('status', 'Brand created successfully.');
    }

    public function edit(Brand $brand): View
    {
        return view('brands.edit', [
            'brand' => $brand,
            'itemCount' => $brand->products()->count(),
        ]);
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validate($this->rules($brand));

        $brand->update($this->attributes($validated, $brand));

        return redirect()
            ->route('brands.index')
            ->with('status', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->exists()) {
            return redirect()
                ->route('brands.index')
                ->withErrors(['brand' => 'Move or delete the items of this brand before removing it.']);
        }

        $brand->delete();

        return redirect()
            ->route('brands.index')
            ->with('status', 'Brand deleted successfully.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?Brand $brand): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('brands', 'slug')->ignore($brand?->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function attributes(array $validated, ?Brand $brand): array
    {
        $slug = Str::slug((string) ($validated['slug'] ?? ''));

        return [
            'name' => $validated['name'],
            'slug' => $slug !== '' ? $slug : $this->slugService->unique($validated['name'], 'brands', $brand?->id),
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ];
    }
}
