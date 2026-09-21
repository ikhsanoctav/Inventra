<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\Uom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Tenant exists
        $tenant = Tenant::firstOrCreate(
            ['id' => 1],
            ['name' => 'INVENTRA Utama']
        );

        // 2. Create Default Users (Roles: super_admin, kasir, manager, admin_gudang, purchasing)
        $roles = [
            'super_admin' => 'Super Admin',
            'kasir' => 'Kasir',
            'manager' => 'Manager',
            'admin_gudang' => 'Admin Gudang',
            'purchasing' => 'Purchasing',
        ];

        $index = 10000001;
        foreach ($roles as $role => $name) {
            User::updateOrCreate(
                ['email' => $role.'@inventra.go.id'],
                [
                    'nip' => (string) $index++,
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => $role,
                ]
            );
        }

        // 3. Create Categories
        $categories = [
            ['name' => 'Elektronik & IT', 'description' => 'Laptop, Komputer, Server, Aksesoris'],
            ['name' => 'Alat Tulis Kantor', 'description' => 'Kertas, Pulpen, Binder'],
            ['name' => 'Medis & Safety', 'description' => 'Masker, APD, Obat-obatan'],
            ['name' => 'Layanan Jasa', 'description' => 'Servis, Perbaikan, Konsultasi'],
            ['name' => 'Fasilitas & Kebersihan', 'description' => 'Sapu, Pel, Sabun'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                ['description' => $cat['description'], 'tenant_id' => $tenant->id]
            );
        }

        $uoms = ['Unit', 'Pcs', 'Box', 'Rim', 'Kg', 'Pack', 'Jam', 'Paket'];
        foreach ($uoms as $uom) {
            Uom::firstOrCreate(
                ['name' => $uom],
                ['tenant_id' => $tenant->id]
            );
        }

        // 5. Create Suppliers
        $suppliers = [
            ['name' => 'PT Sentra Medika Tama', 'contact_person' => 'Budi', 'phone' => '08123456789'],
            ['name' => 'CV IT Komputindo', 'contact_person' => 'Anton', 'phone' => '082233445566'],
            ['name' => 'Toko Alat Tulis Makmur', 'contact_person' => 'Sari', 'phone' => '083344556677'],
            ['name' => 'PT Jasa Logistik Service', 'contact_person' => 'Joko', 'phone' => '089988776655'],
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(
                ['name' => $sup['name']],
                ['contact_person' => $sup['contact_person'], 'phone' => $sup['phone']]
            );
        }

        // 6. Generate Dummy Items (Barang & Jasa)
        // Ensure we have some categories and uoms
        $catIT = Category::where('name', 'Elektronik & IT')->first();
        $catATK = Category::where('name', 'Alat Tulis Kantor')->first();
        $catMed = Category::where('name', 'Medis & Safety')->first();
        $catJas = Category::where('name', 'Layanan Jasa')->first();

        $uomUnit = Uom::where('name', 'Unit')->first();
        $uomRim = Uom::where('name', 'Rim')->first();
        $uomBox = Uom::where('name', 'Box')->first();
        $uomJam = Uom::where('name', 'Jam')->first();

        $items = [
            [
                'name' => 'Laptop ThinkPad L14 Gen 4',
                'sku' => 'SKU-IT-0082',
                'category_id' => $catIT->id,
                'uom_id' => $uomUnit->id,
                'price' => 12500000,
                'stock' => 48,
                'type' => 'barang',
                'image' => 'images/items/laptop.jpg',
            ],
            [
                'name' => 'Kertas HVS A4 80gr PaperOne',
                'sku' => 'SKU-ATK-0194',
                'category_id' => $catATK->id,
                'uom_id' => $uomRim->id,
                'price' => 55000,
                'stock' => 12, // Kritis
                'type' => 'barang',
                'image' => 'images/items/kertas.jpg',
            ],
            [
                'name' => 'Masker Medis 3-Ply Earloop',
                'sku' => 'SKU-MED-0031',
                'category_id' => $catMed->id,
                'uom_id' => $uomBox->id,
                'price' => 25000,
                'stock' => 320,
                'type' => 'barang',
                'image' => 'images/items/masker.jpg',
            ],
            [
                'name' => 'Monitor Dell 24 Inch Ultrasharp',
                'sku' => 'SKU-IT-0105',
                'category_id' => $catIT->id,
                'uom_id' => $uomUnit->id,
                'price' => 3200000,
                'stock' => 5, // Kritis
                'type' => 'barang',
                'image' => null, // Provide null if no image generated for it
            ],
            [
                'name' => 'Tinta Printer Epson 003 Black',
                'sku' => 'SKU-ATK-0221',
                'category_id' => $catATK->id,
                'uom_id' => $uomUnit->id,
                'price' => 85000,
                'stock' => 0, // Habis
                'type' => 'barang',
                'image' => 'images/items/tinta.jpg',
            ],
            [
                'name' => 'Mouse Wireless Logitech M170',
                'sku' => 'SKU-IT-0301',
                'category_id' => $catIT->id,
                'uom_id' => $uomUnit->id,
                'price' => 120000,
                'stock' => 30,
                'type' => 'barang',
                'image' => null,
            ],
            [
                'name' => 'Pulpen Snowman V5 Hitam',
                'sku' => 'SKU-ATK-0402',
                'category_id' => $catATK->id,
                'uom_id' => $uomBox->id,
                'price' => 25000,
                'stock' => 50,
                'type' => 'barang',
                'image' => null,
            ],
            [
                'name' => 'Hand Sanitizer 500ml',
                'sku' => 'SKU-MED-0503',
                'category_id' => $catMed->id,
                'uom_id' => $uomUnit->id,
                'price' => 45000,
                'stock' => 100,
                'type' => 'barang',
                'image' => null,
            ],
            // Tambahan Item Baru
            [
                'name' => 'Flashdisk SanDisk 64GB',
                'sku' => 'SKU-IT-0604',
                'category_id' => $catIT->id,
                'uom_id' => $uomUnit->id,
                'price' => 75000,
                'stock' => 150,
                'type' => 'barang',
                'image' => null,
            ],
            [
                'name' => 'Keyboard Mechanical Rexus',
                'sku' => 'SKU-IT-0605',
                'category_id' => $catIT->id,
                'uom_id' => $uomUnit->id,
                'price' => 350000,
                'stock' => 20,
                'type' => 'barang',
                'image' => null,
            ],
            [
                'name' => 'Kabel LAN UTP Cat6 50m',
                'sku' => 'SKU-IT-0606',
                'category_id' => $catIT->id,
                'uom_id' => $uomUnit->id,
                'price' => 125000,
                'stock' => 15,
                'type' => 'barang',
                'image' => null,
            ],
            [
                'name' => 'Spidol Papan Tulis Snowman',
                'sku' => 'SKU-ATK-0701',
                'category_id' => $catATK->id,
                'uom_id' => $uomBox->id,
                'price' => 65000,
                'stock' => 40,
                'type' => 'barang',
                'image' => null,
            ],
            [
                'name' => 'Buku Tulis Sinar Dunia 58 Lembar',
                'sku' => 'SKU-ATK-0702',
                'category_id' => $catATK->id,
                'uom_id' => $uomPack->id ?? $uomBox->id,
                'price' => 45000,
                'stock' => 80,
                'type' => 'barang',
                'image' => null,
            ],
            [
                'name' => 'Lakban Bening Daimaru',
                'sku' => 'SKU-ATK-0703',
                'category_id' => $catATK->id,
                'uom_id' => $uomUnit->id,
                'price' => 12000,
                'stock' => 200,
                'type' => 'barang',
                'image' => null,
            ],
            [
                'name' => 'Kotak P3K Lengkap',
                'sku' => 'SKU-MED-0801',
                'category_id' => $catMed->id,
                'uom_id' => $uomUnit->id,
                'price' => 150000,
                'stock' => 25,
                'type' => 'barang',
                'image' => null,
            ],
            [
                'name' => 'Termometer Gun Infrared',
                'sku' => 'SKU-MED-0802',
                'category_id' => $catMed->id,
                'uom_id' => $uomUnit->id,
                'price' => 250000,
                'stock' => 10,
                'type' => 'barang',
                'image' => null,
            ],
            [
                'name' => 'Sarung Tangan Medis (Latex)',
                'sku' => 'SKU-MED-0803',
                'category_id' => $catMed->id,
                'uom_id' => $uomBox->id,
                'price' => 45000,
                'stock' => 85,
                'type' => 'barang',
                'image' => null,
            ],
            [
                'name' => 'Sabun Cuci Tangan Lifebuoy',
                'sku' => 'SKU-FAS-0901',
                'category_id' => $catMed->id, // Menggunakan kategori medis/safety sementara
                'uom_id' => $uomUnit->id,
                'price' => 35000,
                'stock' => 60,
                'type' => 'barang',
                'image' => null,
            ],
            [
                'name' => 'Tisu Wajah Paseo 250 Sheets',
                'sku' => 'SKU-FAS-0902',
                'category_id' => $catMed->id,
                'uom_id' => $uomUnit->id,
                'price' => 18000,
                'stock' => 120,
                'type' => 'barang',
                'image' => null,
            ],
            [
                'name' => 'Kopi Kapal Api Mix',
                'sku' => 'SKU-FAS-0903',
                'category_id' => $catATK->id, // ATK/Pantry
                'uom_id' => $uomPack->id ?? $uomUnit->id,
                'price' => 15000,
                'stock' => 150,
                'type' => 'barang',
                'image' => null,
            ],
            // JASA
            [
                'name' => 'Jasa Instalasi Jaringan LAN',
                'sku' => 'SRV-IT-001',
                'category_id' => $catJas->id,
                'uom_id' => $uomJam->id,
                'price' => 150000,
                'stock' => 0, // Jasa tidak punya stok fisik
                'type' => 'jasa',
                'image' => null,
            ],
            [
                'name' => 'Jasa Perbaikan AC Standing',
                'sku' => 'SRV-MTC-002',
                'category_id' => $catJas->id,
                'uom_id' => $uomUnit->id,
                'price' => 250000,
                'stock' => 0,
                'type' => 'jasa',
                'image' => null,
            ],
            [
                'name' => 'Konsultasi Keamanan Siber',
                'sku' => 'SRV-SEC-003',
                'category_id' => $catJas->id,
                'uom_id' => $uomJam->id,
                'price' => 500000,
                'stock' => 0,
                'type' => 'jasa',
                'image' => null,
            ],
        ];

        foreach ($items as $itemData) {
            Item::updateOrCreate(
                ['sku' => $itemData['sku']],
                [
                    'tenant_id' => $tenant->id,
                    'name' => $itemData['name'],
                    'category_id' => $itemData['category_id'],
                    'uom_id' => $itemData['uom_id'],
                    'standard_price' => $itemData['price'],
                    'stock' => $itemData['stock'],
                    'type' => $itemData['type'],
                    'image' => $itemData['image'],
                ]
            );
        }

        // 7. Generate Dummy Transactions (Inbound & Outbound) for Chart Visualization
        $itemLaptop = Item::where('sku', 'SKU-IT-0082')->first();
        $itemMasker = Item::where('sku', 'SKU-MED-0031')->first();
        $admin = User::where('role', 'admin_gudang')->first();
        $kasir = User::where('role', 'kasir')->first();

        // Inbound Transactions (3 days ago, 7 days ago, 15 days ago)
        $inboundDates = [now()->subDays(3), now()->subDays(7), now()->subDays(15)];
        foreach ($inboundDates as $index => $date) {
            $trx = Transaction::firstOrCreate(
                ['ref_number' => 'INB-DUMMY-'.$index],
                [
                    'tenant_id' => $tenant->id,
                    'user_id' => $admin->id ?? 1,
                    'type' => 'Inbound',
                    'transaction_date' => $date,
                    'status' => 'Completed',
                    'total_amount' => 12500000 * 2, // Dummy value
                ]
            );

            TransactionLine::firstOrCreate(
                ['transaction_id' => $trx->id, 'item_id' => $itemLaptop->id],
                ['quantity' => 2, 'unit_price' => 12500000]
            );
        }

        // Outbound Transactions (1 day ago, 2 days ago, 5 days ago, 12 days ago)
        $outboundDates = [now()->subDays(1), now()->subDays(2), now()->subDays(5), now()->subDays(12)];
        foreach ($outboundDates as $index => $date) {
            $trx = Transaction::firstOrCreate(
                ['ref_number' => 'OUT-DUMMY-'.$index],
                [
                    'tenant_id' => $tenant->id,
                    'user_id' => $kasir->id ?? 1,
                    'type' => 'Outbound',
                    'transaction_date' => $date,
                    'status' => 'Completed',
                    'total_amount' => 25000 * 10, // Dummy value
                ]
            );

            TransactionLine::firstOrCreate(
                ['transaction_id' => $trx->id, 'item_id' => $itemMasker->id],
                ['quantity' => 10, 'unit_price' => 25000]
            );
        }

        echo "Dummy Data for INVENTRA has been populated successfully!\n";
    }
}
