import os

controllers = {
    'CategoryController.php': 'Kategori',
    'ItemController.php': 'Barang',
    'SupplierController.php': 'Rekanan Supplier',
    'UnitController.php': 'Satuan'
}

base_path = 'd:/PROYEKAN/inventory_Logistik/app/Http/Controllers/'

for filename, entity_name in controllers.items():
    filepath = os.path.join(base_path, filename)
    if not os.path.exists(filepath):
        continue
        
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Store
    content = content.replace(
        f"with('success', '{entity_name} berhasil ditambahkan!')",
        f"with('success', \"{entity_name} '{{$request->name}}' berhasil ditambahkan!\")"
    )
    content = content.replace(
        f"with('success', '{entity_name} berhasil ditambahkan')",
        f"with('success', \"{entity_name} '{{$request->name}}' berhasil ditambahkan!\")"
    )
    
    # Update
    content = content.replace(
        f"with('success', '{entity_name} berhasil diupdate!')",
        f"with('success', \"{entity_name} '{{$request->name}}' berhasil diperbarui!\")"
    )
    content = content.replace(
        f"with('success', '{entity_name} berhasil diperbarui')",
        f"with('success', \"{entity_name} '{{$request->name}}' berhasil diperbarui!\")"
    )
    
    # Destroy (Here we use the model instance since request doesn't have name)
    # The variable is usually $category, $item, $supplier, $unit
    var_name = '$' + filename.replace('Controller.php', '').lower()
    
    content = content.replace(
        f"with('success', '{entity_name} berhasil dihapus!')",
        f"with('success', \"{entity_name} '{{{'var_name'}->name}}' berhasil dihapus!\")"
    )
    content = content.replace(
        f"with('success', '{entity_name} berhasil dihapus')",
        f"with('success', \"{entity_name} '{{{'var_name'}->name}}' berhasil dihapus!\")"
    )
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
        
    print(f"Updated {filename}")
