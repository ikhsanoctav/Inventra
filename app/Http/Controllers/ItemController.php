<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Uom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category', 'uom', 'supplier'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $perPage = $request->filled('per_page') ? (int) $request->per_page : 10;
        $items = $query->paginate($perPage)->appends($request->all());

        $categories = Category::all();
        $uoms = Uom::all();
        $suppliers = Supplier::all();

        return view('master.items.index', compact('items', 'categories', 'uoms', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required|string|unique:items,sku',
            'name' => 'required|string|max:255',
            'type' => 'required|in:barang,jasa',
            'category_id' => 'required|exists:categories,id',
            'uom_id' => 'required|exists:uoms,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'standard_price' => 'nullable|numeric',
            'min_stock' => 'nullable|integer',
            'stock' => 'nullable|integer',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('items', 'public');
        }

        Item::create([
            'tenant_id' => 1,
            'sku' => $request->sku,
            'name' => $request->name,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'uom_id' => $request->uom_id,
            'supplier_id' => $request->supplier_id,
            'standard_price' => $request->standard_price ?? 0,
            'min_stock' => $request->min_stock ?? 0,
            'stock' => $request->stock ?? 0,
            'photo_path' => $photoPath,
            'currency_id' => null,
        ]);

        return redirect()->back()->with('success', "Barang '{$request->name}' berhasil ditambahkan!");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'sku' => 'required|string|unique:items,sku,'.$id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:barang,jasa',
            'category_id' => 'required|exists:categories,id',
            'uom_id' => 'required|exists:uoms,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'standard_price' => 'nullable|numeric',
            'min_stock' => 'nullable|integer',
            'stock' => 'nullable|integer',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $item = Item::findOrFail($id);

        $photoPath = $item->photo_path;
        if ($request->hasFile('photo')) {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('items', 'public');
        }

        $item->update([
            'sku' => $request->sku,
            'name' => $request->name,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'uom_id' => $request->uom_id,
            'supplier_id' => $request->supplier_id,
            'standard_price' => $request->standard_price ?? 0,
            'min_stock' => $request->min_stock ?? 0,
            'stock' => $request->stock ?? 0,
            'photo_path' => $photoPath,
        ]);

        return redirect()->back()->with('success', "Barang '{$request->name}' berhasil diperbarui!");
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        if ($item->photo_path && Storage::disk('public')->exists($item->photo_path)) {
            Storage::disk('public')->delete($item->photo_path);
        }
        $item->delete();

        return redirect()->back()->with('success', "Barang '{$item->name}' berhasil dihapus!");
    }
}
