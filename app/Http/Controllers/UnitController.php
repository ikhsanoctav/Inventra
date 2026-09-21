<?php

namespace App\Http\Controllers;

use App\Models\Uom;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $units = Uom::withCount('items')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('symbol', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($request->get('per_page', 10));

        return view('master.units.index', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        Uom::create([
            'tenant_id' => 1,
            'name' => $request->name,
            'abbreviation' => $request->abbreviation,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', "Satuan '{$request->name}' berhasil ditambahkan!");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $uom = Uom::findOrFail($id);
        $uom->update([
            'name' => $request->name,
            'abbreviation' => $request->abbreviation,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', "Satuan '{$request->name}' berhasil diperbarui!");
    }

    public function destroy($id)
    {
        $uom = Uom::findOrFail($id);
        $uom->delete();

        return redirect()->back()->with('success', "Satuan '{var_name->name}' berhasil dihapus!");
    }
}
