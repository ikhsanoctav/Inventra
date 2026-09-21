@extends('layouts.app')

@section('header_title', 'Request Perubahan Data')

@section('main_content')
<div class="space-y-6" x-data="requestManager()">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Request Perubahan Data</h2>
            <p class="text-sm text-[#6B7280]">Kelola permintaan penyesuaian stok, barang rusak, atau perubahan master data.</p>
        </div>
        <div class="flex gap-3">
            <button @click="showForm = true" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add</span> Buat Request
            </button>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex space-x-1 border-b border-slate-200">
        <button @click="setStatus('Menunggu')" :class="currentStatus === 'Menunggu' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-3 text-sm font-semibold transition-colors">
            Menunggu (<span x-text="counts.Menunggu"></span>)
        </button>
        <button @click="setStatus('Disetujui')" :class="currentStatus === 'Disetujui' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-3 text-sm font-semibold transition-colors">
            Disetujui (<span x-text="counts.Disetujui"></span>)
        </button>
        <button @click="setStatus('Ditolak')" :class="currentStatus === 'Ditolak' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-3 text-sm font-semibold transition-colors">
            Ditolak (<span x-text="counts.Ditolak"></span>)
        </button>
    </div>

    <!-- Requests List -->
    <div class="grid grid-cols-1 gap-4 relative min-h-[400px]">
        
        <!-- Skeleton Loader -->
        <div x-show="isLoading" class="absolute inset-0 z-10 flex flex-col gap-4 bg-slate-50">
            <template x-for="i in 3">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex gap-4 items-start w-full">
                        <div class="w-12 h-12 rounded-xl bg-slate-200 animate-pulse shrink-0"></div>
                        <div class="w-full">
                            <div class="flex gap-2 mb-2">
                                <div class="w-16 h-5 bg-slate-200 rounded animate-pulse"></div>
                                <div class="w-24 h-5 bg-slate-200 rounded animate-pulse"></div>
                            </div>
                            <div class="w-1/3 h-6 bg-slate-200 rounded mb-2 animate-pulse"></div>
                            <div class="w-2/3 h-4 bg-slate-200 rounded animate-pulse"></div>
                        </div>
                    </div>
                    <div class="flex gap-2 w-full md:w-auto mt-4 md:mt-0">
                        <div class="w-20 h-10 bg-slate-200 rounded-xl animate-pulse"></div>
                        <div class="w-20 h-10 bg-slate-200 rounded-xl animate-pulse"></div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Real Data -->
        <div x-show="!isLoading" class="flex flex-col gap-4">
            <template x-if="requests.length === 0">
                <div class="flex flex-col items-center justify-center py-12 text-slate-400">
                    <span class="material-symbols-outlined text-[48px] mb-4 opacity-50">inbox</span>
                    <p>Tidak ada request dengan status <span x-text="currentStatus"></span>.</p>
                </div>
            </template>

            <template x-for="req in requests" :key="req.id">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:border-indigo-100 hover:shadow-md transition-all">
                    <div class="flex gap-4 items-start">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                             :class="req.type === 'Penyesuaian Stok' ? 'bg-amber-50 text-amber-500' : 'bg-blue-50 text-blue-500'">
                            <span class="material-symbols-outlined" x-text="req.type === 'Penyesuaian Stok' ? 'warning' : 'edit_note'"></span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold px-2 py-1 rounded-md"
                                      :class="{
                                          'bg-amber-100 text-amber-700': req.status === 'Menunggu',
                                          'bg-emerald-100 text-emerald-700': req.status === 'Disetujui',
                                          'bg-red-100 text-red-700': req.status === 'Ditolak'
                                      }" x-text="req.status"></span>
                                <span class="text-sm text-slate-400" x-text="req.request_number"></span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 font-headline" x-text="req.title"></h3>
                            <p class="text-sm text-slate-500 mt-1" x-text="req.description || 'Tidak ada deskripsi'"></p>
                            <div class="flex items-center gap-4 mt-3 text-xs text-slate-400">
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">person</span> <span x-text="req.user_name"></span></span>
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">schedule</span> <span x-text="req.time_ago"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2 w-full md:w-auto">
                        <button class="flex-1 md:flex-none px-4 py-2 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 text-sm font-semibold transition-colors">Lihat</button>
                        <button x-show="req.status === 'Menunggu'" class="flex-1 md:flex-none px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl hover:bg-indigo-100 text-sm font-semibold transition-colors">Proses</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Form Modal -->
    <div x-show="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4" style="display: none;">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-lg" @click.away="showForm = false">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold font-headline">Buat Request Baru</h3>
                <button @click="showForm = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form @submit.prevent="submitRequest">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Tipe Request</label>
                        <select x-model="form.type" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option>Penyesuaian Stok</option>
                            <option>Perubahan Lokasi Rak</option>
                            <option>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Judul Request</label>
                        <input type="text" x-model="form.title" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Deskripsi & Alasan</label>
                        <textarea x-model="form.description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showForm = false" class="px-4 py-2 text-slate-600 font-semibold hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit" :disabled="isSubmitting" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 flex items-center gap-2">
                        <span x-show="isSubmitting" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                        Kirim Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('requestManager', () => ({
            isLoading: true,
            currentStatus: 'Menunggu',
            requests: [],
            counts: { Menunggu: 0, Disetujui: 0, Ditolak: 0 },
            showForm: false,
            isSubmitting: false,
            form: {
                type: 'Penyesuaian Stok',
                title: '',
                description: ''
            },

            async init() {
                await this.fetchData();
            },

            async setStatus(status) {
                this.currentStatus = status;
                await this.fetchData();
            },

            async fetchData() {
                this.isLoading = true;
                // Add a small delay so user can appreciate the skeleton loading state
                await new Promise(r => setTimeout(r, 600));

                try {
                    const response = await fetch(`{{ route('gudang.requests') }}?status=${this.currentStatus}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        this.requests = data.requests;
                        this.counts = data.counts;
                    }
                } catch (error) {
                    console.error('Error fetching requests:', error);
                } finally {
                    this.isLoading = false;
                }
            },

            async submitRequest() {
                this.isSubmitting = true;
                try {
                    const response = await fetch("{{ route('gudang.requests.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.form)
                    });

                    if (response.ok) {
                        this.showForm = false;
                        this.form.title = '';
                        this.form.description = '';
                        
                        // Switch to Menunggu tab to see the new request
                        if (this.currentStatus !== 'Menunggu') {
                            this.currentStatus = 'Menunggu';
                        }
                        await this.fetchData();
                    } else {
                        alert('Gagal mengirim request');
                    }
                } catch (error) {
                    console.error('Error submitting request:', error);
                } finally {
                    this.isSubmitting = false;
                }
            }
        }));
    });
</script>
@endsection
