<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\ProductItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        $promotions = Promotion::when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Promotions/Index', [
            'promotions' => $promotions,
            'filters' => $request->only(['search', 'sort', 'direction']),
            'pageTitle' => 'Promotions'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Promotions/Create', [
            'productItems' => ProductItem::where('status', 1)->get(['id', 'name']),
            'pageTitle' => 'Add New Promotion'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|boolean',
            'items' => 'nullable|array',
            'items.*' => 'exists:product_items,id',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $promotion = Promotion::create(collect($validated)->except('items')->toArray());
                if (!empty($validated['items'])) {
                    $promotion->items()->sync($validated['items']);
                }
            });

            return redirect()->route('admin.promotions.index')->with('success', 'Promotion created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create promotion: ' . $e->getMessage());
        }
    }

    public function edit(Promotion $promotion)
    {
        $promotion->load('items:id');
        
        return Inertia::render('Admin/Promotions/Edit', [
            'promotion' => $promotion,
            'productItems' => ProductItem::where('status', 1)->get(['id', 'name']),
            'selectedItems' => $promotion->items->pluck('id'),
            'pageTitle' => 'Edit Promotion: ' . $promotion->name
        ]);
    }

    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|boolean',
            'items' => 'nullable|array',
            'items.*' => 'exists:product_items,id',
        ]);

        try {
            DB::transaction(function () use ($validated, $promotion) {
                $promotion->update(collect($validated)->except('items')->toArray());
                $promotion->items()->sync($validated['items'] ?? []);
            });

            return redirect()->route('admin.promotions.index')->with('success', 'Promotion updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update promotion: ' . $e->getMessage());
        }
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()->route('admin.promotions.index')->with('success', 'Promotion deleted successfully.');
    }
}


