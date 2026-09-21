@extends('layouts.app')

@section('header_title', 'Master Data Barang & Jasa')

@section('main_content')
<div class="space-y-6" x-data="{ showModal: false, editMode: false, item: {} }">
    <!-- Header Section -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="min-w-[200px] flex-1">
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Data Barang & Jasa</h2>
            <p class="text-sm text-[#6B7280]">Kelola katalog barang, jasa, pantau stok, dan harga.</p>
        </div>
        
        <div class="flex items-center gap-3 shrink-0">
            <button @click="showModal = true; editMode = false; item = {}" class="h-10 px-4 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold flex items-center gap-2 shadow-sm shadow-indigo-500/20 transition-all shrink-0">
                <span class="material-symbols-outlined text-[18px]">add</span> <span>Tambah Data</span>
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <form method="GET" action="{{ route('master.items') }}" 
          hx-get="{{ route('master.items') }}"
          hx-target="#table-container"
          hx-select="#table-container"
          hx-swap="outerHTML"
          hx-trigger="input changed delay:500ms from:input[name='search'], change from:select"
          class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-sm flex flex-col sm:flex-row flex-wrap gap-4 items-end relative"
          x-data="{ loading: false }"
          @htmx:before-request.camel="loading = true"
          @htmx:after-request.camel="loading = false">
          
        <div x-show="loading" style="display: none;" class="absolute -top-3 right-4 bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-[10px] font-bold flex items-center gap-1 shadow-sm">
            <span class="material-symbols-outlined text-[12px] animate-spin">refresh</span> Loading...
        </div>

        <div class="flex-1 min-w-[200px] w-full sm:w-auto">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Cari Item</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] text-[18px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari SKU / Nama..." class="w-full h-10 pl-9 pr-4 text-sm bg-slate-50 border border-[#E5E7EB] rounded-lg text-[#111827] focus:outline-none focus:bg-white focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all">
            </div>
        </div>
        <div class="w-full sm:w-48">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe</label>
            <select name="type" class="w-full h-10 px-3 text-sm bg-slate-50 border border-[#E5E7EB] rounded-lg focus:outline-none focus:bg-white focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all cursor-pointer">
                <option value="">Semua Tipe</option>
                <option value="barang" {{ request('type') == 'barang' ? 'selected' : '' }}>Barang Fisik</option>
                <option value="jasa" {{ request('type') == 'jasa' ? 'selected' : '' }}>Jasa / Layanan</option>
            </select>
        </div>
        <div class="w-full sm:w-48">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
            <select name="category_id" class="w-full h-10 px-3 text-sm bg-slate-50 border border-[#E5E7EB] rounded-lg focus:outline-none focus:bg-white focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all cursor-pointer">
                <option value="">Semua Kategori</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-32">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tampilkan</label>
            <select name="per_page" class="w-full h-10 px-3 text-sm bg-slate-50 border border-[#E5E7EB] rounded-lg focus:outline-none focus:bg-white focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all cursor-pointer">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 baris</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
            </select>
        </div>
        @if(request()->anyFilled(['search', 'type', 'category_id']) || (request('per_page') && request('per_page') != 10))
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('master.items') }}" class="h-10 px-4 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold flex items-center justify-center gap-2 shadow-sm transition-all w-full md:w-auto">
                    <span class="material-symbols-outlined text-[18px]">clear_all</span> <span class="hidden lg:inline">Reset</span>
                </a>
            </div>
        @endif
    </form>

    <!-- Data Table -->
    <div id="table-container" class="space-y-6">
        <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-[#4B5563]">
                    <thead class="bg-[#F8FAFC] text-xs uppercase text-[#6B7280] font-semibold border-b border-[#E5E7EB]">
                        <tr>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Tipe</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">SKU / Kode</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Nama Item</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Kategori</th>
                            <th scope="col" class="px-6 py-4 text-right">Harga (Rp)</th>
                            <th scope="col" class="px-6 py-4 text-right">Stok</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB]">
                        @forelse($items as $i)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                @if($i->type === 'jasa')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-purple-50 text-purple-700 text-xs font-semibold border border-purple-100">
                                        <span class="material-symbols-outlined text-[14px]">design_services</span> Jasa
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100">
                                        <span class="material-symbols-outlined text-[14px]">inventory_2</span> Barang
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">{{ $i->sku }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($i->photo_path)
                                        <img src="{{ Storage::url($i->photo_path) }}" alt="{{ $i->name }}" class="w-10 h-10 rounded-lg object-cover border border-slate-200">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400">
                                            <span class="material-symbols-outlined text-[20px]">image</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-slate-800">{{ $i->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $i->category->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-right font-medium text-indigo-600">{{ number_format($i->standard_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($i->type === 'jasa')
                                    <span class="text-slate-400 italic">N/A</span>
                                @else
                                    <span class="{{ $i->stock <= $i->min_stock ? 'text-red-600 font-bold' : 'text-emerald-600 font-medium' }}">{{ $i->stock }} {{ $i->uom->name ?? '' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="item = {{ json_encode($i) }}; editMode = true; showModal = true" class="text-slate-400 hover:text-indigo-600 transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <form action="{{ route('master.items.destroy', $i->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus item ini?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-3xl text-[#D1D5DB]">category</span>
                                    </div>
                                    <h3 class="text-sm font-semibold text-[#111827] mb-1">Belum ada data barang/jasa</h3>
                                    <p class="text-xs text-[#6B7280]">Klik tombol Tambah Data untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-[#E5E7EB] flex items-center justify-between text-xs text-[#6B7280]">
                <div class="hidden sm:block">
                    <span>Menampilkan <strong>{{ $items->firstItem() ?? 0 }}</strong> sampai <strong>{{ $items->lastItem() ?? 0 }}</strong> dari <strong>{{ $items->total() }}</strong> total data</span>
                </div>
                <div class="flex-1 sm:flex-none">
                    {{ $items->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" aria-hidden="true" @click="showModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <!-- Modal Panel -->
            <div x-show="showModal" x-transition.scale.origin.bottom class="inline-block w-full max-w-2xl px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:p-6 relative z-10">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-800 font-headline" x-text="editMode ? 'Edit Data' : 'Tambah Data'"></h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <form :action="editMode ? '{{ url('master/items') }}/' + item.id : '{{ route('master.items') }}'" method="POST" enctype="multipart/form-data">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-sm">
                        
                        <div class="space-y-1 md:col-span-2">
                            <label class="font-medium text-slate-700">Tipe Item <span class="text-red-500">*</span></label>
                            <div class="flex gap-4 mt-1">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="type" value="barang" x-model="item.type" class="text-indigo-600 focus:ring-indigo-500" required>
                                    <span class="text-slate-700">Barang Fisik</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="type" value="jasa" x-model="item.type" class="text-indigo-600 focus:ring-indigo-500" required>
                                    <span class="text-slate-700">Layanan / Jasa</span>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="font-medium text-slate-700">SKU / Kode <span class="text-red-500">*</span></label>
                            <input type="text" name="sku" x-model="item.sku" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        
                        <div class="space-y-1">
                            <label class="font-medium text-slate-700">Nama Item <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="item.name" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        
                        <div class="space-y-1">
                            <label class="font-medium text-slate-700">Kategori <span class="text-red-500">*</span></label>
                            <select name="category_id" x-model="item.category_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Pilih Kategori...</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="font-medium text-slate-700">Satuan (UoM) <span class="text-red-500">*</span></label>
                            <select name="uom_id" x-model="item.uom_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Pilih Satuan...</option>
                                @foreach($uoms as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1" x-show="item.type !== 'jasa'">
                            <label class="font-medium text-slate-700">Stok Saat Ini</label>
                            <input type="number" name="stock" x-model="item.stock" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="space-y-1" x-show="item.type !== 'jasa'">
                            <label class="font-medium text-slate-700">Batas Aman (Min Stock)</label>
                            <input type="number" name="min_stock" x-model="item.min_stock" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="space-y-1">
                            <label class="font-medium text-slate-700">Harga Standar (Rp)</label>
                            <input type="number" name="standard_price" x-model="item.standard_price" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="space-y-1 md:col-span-2">
                            <label class="font-medium text-slate-700 block mb-2">Foto Barang</label>
                            <div x-data="{ fileName: '' }" class="relative w-full h-32 rounded-xl bg-slate-50 border-2 border-dashed border-slate-300 hover:border-indigo-400 hover:bg-indigo-50/50 transition-colors flex flex-col items-center justify-center cursor-pointer overflow-hidden group">
                                <input type="file" name="photo" accept="image/*" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                
                                <div x-show="!fileName" class="text-center pointer-events-none transition-all">
                                    <span class="material-symbols-outlined text-4xl text-slate-400 group-hover:text-indigo-500 mb-2 transition-colors">cloud_upload</span>
                                    <p class="text-sm font-medium text-slate-700">Klik atau seret foto ke sini</p>
                                    <p class="text-xs text-slate-500 mt-1">Maks. 2MB (JPG, PNG, GIF)</p>
                                </div>

                                <div x-show="fileName" style="display: none;" class="text-center p-4 pointer-events-none w-full transition-all">
                                    <span class="material-symbols-outlined text-4xl text-emerald-500 mb-2">check_circle</span>
                                    <p class="text-sm font-medium text-emerald-700 truncate px-4" x-text="fileName"></p>
                                    <p class="text-xs text-slate-500 mt-1 text-emerald-600/70">Klik untuk mengganti foto</p>
                                </div>
                            </div>
                        </div>


                    </div>
                    
                    <div class="mt-5 sm:mt-6 flex gap-3 justify-end">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection