<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class BarcodePrintController extends Controller
{
    /**
     * Display the barcode label generator interface.
     */
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');
        $search = $request->query('search');

        $query = Item::with(['category', 'uom'])->where('type', 'barang');

        if (! empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->paginate(20)->appends($request->all());
        $categories = Category::orderBy('name')->get();

        return view('gudang.barcode.index', compact('items', 'categories'));
    }

    /**
     * Render the printable barcode labels page.
     */
    public function print(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'template' => 'required|string|in:rack_label,product_barcode,a4_grid',
            'code_type' => 'nullable|string|in:barcode,qrcode',
        ]);

        $selectedItemIds = array_keys($request->items);
        $quantities = $request->items; // item_id => quantity to print

        $items = Item::with(['category', 'uom'])->whereIn('id', $selectedItemIds)->get();

        $printList = [];
        foreach ($items as $item) {
            $qtyToPrint = max(1, min(100, (int) ($quantities[$item->id] ?? 1)));
            for ($k = 0; $k < $qtyToPrint; $k++) {
                $printList[] = $item;
            }
        }

        $template = $request->template;
        $codeType = $request->input('code_type', 'barcode');
        $autoFormat = $request->input('auto_format', '1') === '1';
        $showBorder = $request->input('show_border', '1') === '1';

        foreach ($printList as $item) {
            $item->display_sku = $autoFormat ? $item->formatted_sku : $item->sku;
        }

        return view('gudang.barcode.print', compact('printList', 'template', 'codeType', 'autoFormat', 'showBorder'));
    }
}
