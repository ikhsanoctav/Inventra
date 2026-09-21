<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sidebar Configuration (Role Based)
    |--------------------------------------------------------------------------
    | Mapping Material Symbols Outlined to Lucide PRD suggestions.
    */

    'super_admin' => [
        [
            'title' => 'Dashboard',
            'icon' => 'dashboard',
            'route' => 'dashboard',
        ],
        [
            'title' => 'Master Data',
            'icon' => 'inventory_2',
            'submenus' => [
                ['title' => 'Barang & Jasa', 'route' => 'master.items', 'icon' => 'inventory_2'],
                ['title' => 'Kategori & Sub', 'route' => 'master.categories', 'icon' => 'account_tree'],
                ['title' => 'Satuan (UoM)', 'route' => 'master.units', 'icon' => 'square_foot'],
                ['title' => 'Data Supplier', 'route' => 'master.suppliers', 'icon' => 'local_shipping'],
                ['title' => 'Data Cabang/Outlet', 'route' => 'master.branches', 'icon' => 'store'],
            ],
        ],
        [
            'title' => 'Transaksi',
            'icon' => 'local_shipping',
            'submenus' => [
                ['title' => 'Barang Masuk (Inbound)', 'route' => 'transactions.inbound', 'icon' => 'login'],
                ['title' => 'Barang Keluar (Outbound)', 'route' => 'transactions.outbound', 'icon' => 'logout'],
                ['title' => 'Stock Opname', 'route' => 'transactions.opname.index', 'icon' => 'fact_check'],
                ['title' => 'Mutasi Antar Gudang', 'route' => 'transactions.transfer', 'icon' => 'swap_horiz'],
            ],
        ],
        [
            'title' => 'Modul POS',
            'icon' => 'point_of_sale',
            'submenus' => [
                ['title' => 'Akses Bypass Kasir', 'route' => 'pos.index', 'icon' => 'shopping_cart'],
                ['title' => 'Manajemen Shift Kasir', 'route' => 'pos.shifts', 'icon' => 'schedule'],
            ],
        ],
        [
            'title' => 'Purchase Order',
            'icon' => 'receipt_long',
            'submenus' => [
                ['title' => 'Daftar PO (Monitoring)', 'route' => 'transactions.po.index', 'icon' => 'description'],
            ],
        ],
        [
            'title' => 'Laporan',
            'icon' => 'bar_chart',
            'submenus' => [
                ['title' => 'Laporan Stok', 'route' => 'reports.stock', 'icon' => 'bar_chart'],
                ['title' => 'Laporan Mutasi', 'route' => 'reports.mutation', 'icon' => 'sync_alt'],
                ['title' => 'Laporan Penjualan', 'route' => 'reports.sales', 'icon' => 'trending_up'],
                ['title' => 'Laporan Laba/Rugi', 'route' => 'reports.profit_loss', 'icon' => 'pie_chart'],
            ],
        ],
        [
            'title' => 'Pengaturan Sistem',
            'icon' => 'settings',
            'submenus' => [
                ['title' => 'Manajemen Pengguna', 'route' => 'system.users', 'icon' => 'manage_accounts'],
                ['title' => 'Jadwal & Shift Kerja', 'route' => 'system.schedules.index', 'icon' => 'calendar_month'],
                ['title' => 'Rule Builder (RBL)', 'route' => 'system.rbl', 'icon' => 'rule'],
                ['title' => 'General Settings', 'route' => 'system.settings', 'icon' => 'settings'],
                ['title' => 'Activity/Audit Log', 'route' => 'system.audit', 'icon' => 'history'],
            ],
        ],
    ],

    'manager' => [
        [
            'title' => 'Dashboard Eksekutif',
            'icon' => 'dashboard',
            'submenus' => [
                ['title' => 'KPI Real-time', 'route' => 'dashboard', 'icon' => 'dashboard'],
                ['title' => 'Tren Penjualan', 'route' => 'manager.trends', 'icon' => 'show_chart'],
            ],
        ],
        [
            'title' => 'Laporan Keuangan',
            'icon' => 'pie_chart',
            'submenus' => [
                ['title' => 'Laporan Laba/Rugi', 'route' => 'reports.profit_loss', 'icon' => 'pie_chart'],
                ['title' => 'Penjualan per Cabang', 'route' => 'reports.sales_branch', 'icon' => 'storefront'],
                ['title' => 'Laporan Pembelian (PO)', 'route' => 'reports.purchasing', 'icon' => 'shopping_bag'],
                ['title' => 'Valuasi Persediaan', 'route' => 'reports.stock_valuation', 'icon' => 'account_balance_wallet'],
            ],
        ],
        [
            'title' => 'Audit Komprehensif',
            'icon' => 'policy',
            'submenus' => [
                ['title' => 'Audit Trail Transaksi', 'route' => 'transactions.history', 'icon' => 'policy'],
                ['title' => 'Audit Stock Opname', 'route' => 'manager.audit.opname', 'icon' => 'fact_check'],
            ],
        ],
        [
            'title' => 'Activity Log',
            'icon' => 'history',
            'route' => 'system.audit',
        ],
        [
            'title' => 'Approval Center',
            'icon' => 'check_circle',
            'route' => 'manager.approvals',
        ],
        [
            'title' => 'Manajemen SDM',
            'icon' => 'groups',
            'submenus' => [
                ['title' => 'Jadwal & Shift Kerja', 'route' => 'system.schedules.index', 'icon' => 'calendar_month'],
            ],
        ],
    ],

    'admin_gudang' => [
        [
            'title' => 'Dashboard Gudang',
            'icon' => 'dashboard',
            'route' => 'dashboard',
        ],
        [
            'title' => 'Master Data (View)',
            'icon' => 'inventory_2',
            'submenus' => [
                ['title' => 'Lihat Barang & Jasa', 'route' => 'master.items', 'icon' => 'visibility'],
                ['title' => 'Request Perubahan', 'route' => 'gudang.requests', 'icon' => 'send'],
            ],
        ],
        [
            'title' => 'Transaksi Gudang',
            'icon' => 'local_shipping',
            'submenus' => [
                ['title' => 'Barang Masuk', 'route' => 'transactions.inbound', 'icon' => 'login'],
                ['title' => 'Barang Keluar', 'route' => 'transactions.outbound', 'icon' => 'logout'],
                ['title' => 'Surat Jalan', 'route' => 'gudang.delivery_note', 'icon' => 'file_upload'],
                ['title' => 'Stock Opname', 'route' => 'transactions.opname.index', 'icon' => 'fact_check'],
            ],
        ],
        [
            'title' => 'Laporan Gudang',
            'icon' => 'bar_chart',
            'submenus' => [
                ['title' => 'Riwayat Mutasi Stok', 'route' => 'reports.mutation', 'icon' => 'sync_alt'],
                ['title' => 'Kartu Stok per Item', 'route' => 'reports.stock_card', 'icon' => 'menu_book'],
                ['title' => 'Stok Menipis', 'route' => 'reports.low_stock', 'icon' => 'warning'],
            ],
        ],
    ],

    'purchasing' => [
        [
            'title' => 'Dashboard Pengadaan',
            'icon' => 'dashboard',
            'route' => 'dashboard',
        ],
        [
            'title' => 'Kelola Supplier',
            'icon' => 'local_shipping',
            'submenus' => [
                ['title' => 'Daftar Supplier', 'route' => 'master.suppliers', 'icon' => 'local_shipping'],
                ['title' => 'Riwayat Pemesanan', 'route' => 'purchasing.history', 'icon' => 'history'],
                ['title' => 'Evaluasi Vendor', 'route' => 'purchasing.performance', 'icon' => 'star'],
            ],
        ],
        [
            'title' => 'Purchase Order (PO)',
            'icon' => 'receipt_long',
            'submenus' => [
                ['title' => 'Buat PO Baru', 'route' => 'transactions.po.create', 'icon' => 'note_add'],
                ['title' => 'Status & Tracking PO', 'route' => 'transactions.po.index', 'icon' => 'local_shipping'],
                ['title' => 'Approval Menunggu', 'route' => 'purchasing.pending_approval', 'icon' => 'schedule'],
            ],
        ],
        [
            'title' => 'Laporan Pembelian',
            'icon' => 'bar_chart',
            'submenus' => [
                ['title' => 'Laporan PO per Periode', 'route' => 'reports.po', 'icon' => 'description'],
                ['title' => 'Laporan Reorder Point', 'route' => 'reports.reorder', 'icon' => 'warning'],
            ],
        ],
    ],

    'kasir' => [
        [
            'title' => 'Portal Kasir',
            'icon' => 'dashboard',
            'route' => 'dashboard',
        ],
        [
            'title' => 'Aplikasi POS',
            'icon' => 'point_of_sale',
            'route' => 'pos.index',
        ],
        [
            'title' => 'Riwayat & Manajemen',
            'icon' => 'history',
            'submenus' => [
                ['title' => 'Transaksi Hari Ini', 'route' => 'pos.history.today', 'icon' => 'receipt_long'],
                ['title' => 'Ringkasan Shift', 'route' => 'pos.shifts', 'icon' => 'schedule'],
                ['title' => 'Retur Penjualan', 'route' => 'pos.returns', 'icon' => 'undo'],
            ],
        ],
    ],
];
