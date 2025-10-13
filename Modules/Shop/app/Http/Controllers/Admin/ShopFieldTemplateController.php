<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Shop\App\Models\ShopProductFieldTemplate;

class ShopFieldTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = ShopProductFieldTemplate::ordered()->get();

        return view('shop::admin.field-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('shop::admin.field-templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191|unique:shop_product_field_templates,name',
            'description' => 'nullable|string|max:1000',
            'fields' => 'required|array|min:1',
            'fields.*.name' => 'required|string|max:100',
            'fields.*.type' => 'required|in:input,textarea,checkbox',
            'fields.*.order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        // Sort order: son template'in sort_order'ından +1
        $lastOrder = ShopProductFieldTemplate::max('sort_order') ?? 0;
        $validated['sort_order'] = $lastOrder + 1;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        ShopProductFieldTemplate::create($validated);

        return redirect()
            ->route('admin.shop.field-templates.index')
            ->with('success', __('shop::admin.template_created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $template = ShopProductFieldTemplate::findOrFail($id);

        return view('shop::admin.field-templates.edit', compact('template'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $template = ShopProductFieldTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:191|unique:shop_product_field_templates,name,' . $id . ',template_id',
            'description' => 'nullable|string|max:1000',
            'fields' => 'required|array|min:1',
            'fields.*.name' => 'required|string|max:100',
            'fields.*.type' => 'required|in:input,textarea,checkbox',
            'fields.*.order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $template->update($validated);

        return redirect()
            ->route('admin.shop.field-templates.index')
            ->with('success', __('shop::admin.template_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $template = ShopProductFieldTemplate::findOrFail($id);
        $template->delete();

        return redirect()
            ->route('admin.shop.field-templates.index')
            ->with('success', __('shop::admin.template_deleted'));
    }

    /**
     * Toggle active status (AJAX)
     */
    public function toggleActive(Request $request, $id)
    {
        $template = ShopProductFieldTemplate::findOrFail($id);
        $template->is_active = !$template->is_active;
        $template->save();

        return response()->json([
            'success' => true,
            'message' => __('shop::admin.template_status_updated'),
            'is_active' => $template->is_active,
        ]);
    }

    /**
     * Update sort order (AJAX)
     */
    public function updateOrder(Request $request)
    {
        $order = $request->input('order', []);

        foreach ($order as $item) {
            ShopProductFieldTemplate::where('template_id', $item['id'])
                ->update(['sort_order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => __('shop::admin.order_updated'),
        ]);
    }
}
