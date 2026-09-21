@extends('layouts.app')

@section('header_title', 'Penjualan per Cabang')

@section('main_content')
<div class="space-y-6" x-data="branchReport()">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline text-2xl font-bold text-[#111827]">Laporan Penjualan Cabang</h2>
            <p class="text-sm text-[#6B7280]">Bandingkan performa penjualan seluruh cabang perusahaan.</p>
        </div>
        <div class="flex gap-3">
            <select class="pl-4 pr-10 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm bg-white shadow-sm disabled:opacity-50" :disabled="isLoading">
                <option>Bulan Ini</option>
                <option>Bulan Lalu</option>
                <option>Tahun Ini</option>
            </select>
            <button class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-2 disabled:opacity-50" :disabled="isLoading">
                <span class="material-symbols-outlined text-[18px]">download</span> Export PDF
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top Performer -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-[2rem] p-8 text-white shadow-lg flex flex-col justify-between relative overflow-hidden">
            <!-- Skeleton Layer -->
            <div x-show="isLoading" class="absolute inset-0 bg-indigo-600 z-10 p-8 flex flex-col justify-between">
                <div>
                    <div class="w-32 h-6 bg-indigo-400 rounded-full mb-4 animate-pulse"></div>
                    <div class="w-3/4 h-8 bg-indigo-400 rounded mb-2 animate-pulse"></div>
                    <div class="w-1/2 h-4 bg-indigo-400 rounded animate-pulse"></div>
                </div>
                <div class="mt-8">
                    <div class="w-2/3 h-10 bg-indigo-400 rounded mb-2 animate-pulse"></div>
                    <div class="w-1/2 h-5 bg-indigo-400 rounded animate-pulse"></div>
                </div>
            </div>

            <!-- Content -->
            <div>
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-white/20 rounded-full text-xs font-semibold backdrop-blur-sm mb-4">
                    <span class="material-symbols-outlined text-[14px]">military_tech</span> Cabang Terbaik
                </span>
                <h3 class="text-3xl font-bold font-headline mb-1" x-text="topBranchName"></h3>
                <p class="text-indigo-100 text-sm">Cabang dengan pendapatan tertinggi bulan ini</p>
            </div>
            <div class="mt-8">
                <div class="text-3xl font-bold mb-1" x-text="formatRupiah(topBranchRevenue)"></div>
                <div class="flex items-center gap-2 text-emerald-300 text-sm font-semibold">
                    <span class="material-symbols-outlined text-[18px]">trending_up</span>
                    Data Real-Time
                </div>
            </div>
        </div>

        <!-- Bar Chart Data -->
        <div class="lg:col-span-2 bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 flex flex-col relative overflow-hidden min-h-[300px]">
            <h4 class="font-bold text-slate-800 mb-6 font-headline">Komparasi Pendapatan (Bulan Ini)</h4>
            
            <!-- Skeleton Layer -->
            <div x-show="isLoading" class="absolute inset-0 bg-white z-10 p-6 flex flex-col mt-12 space-y-6">
                <template x-for="i in 4">
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <div class="w-1/3 h-4 bg-slate-200 rounded animate-pulse"></div>
                            <div class="w-1/4 h-4 bg-slate-200 rounded animate-pulse"></div>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-slate-200 h-3 rounded-full animate-pulse" :style="`width: ${Math.random() * 60 + 30}%`"></div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Content -->
            <div class="space-y-5 flex-1 overflow-y-auto pr-2" x-show="!isLoading">
                <template x-if="branches.length === 0">
                    <div class="flex flex-col items-center justify-center h-full text-slate-400 py-8">
                        <span class="material-symbols-outlined text-4xl mb-2">analytics</span>
                        <p>Belum ada data penjualan cabang.</p>
                    </div>
                </template>

                <template x-for="(branch, index) in branches" :key="index">
                    <div>
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-sm font-semibold text-slate-700" x-text="branch.name"></span>
                            <span class="text-sm font-bold text-indigo-600" x-text="formatRupiah(branch.revenue)"></span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all duration-1000 ease-out" 
                                 :class="index === 0 ? 'bg-indigo-500' : (index === 1 ? 'bg-indigo-400' : 'bg-indigo-300')"
                                 :style="`width: ${(branch.revenue / maxRevenue) * 100}%`"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('branchReport', () => ({
            isLoading: true,
            branches: [],
            
            get topBranchName() {
                return this.branches.length > 0 ? this.branches[0].name : '-';
            },
            
            get topBranchRevenue() {
                return this.branches.length > 0 ? this.branches[0].revenue : 0;
            },
            
            get maxRevenue() {
                return this.branches.length > 0 ? this.branches[0].revenue : 1; // avoid div by 0
            },

            formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
            },

            async init() {
                await new Promise(r => setTimeout(r, 600)); // Demo skeleton delay
                await this.fetchData();
            },

            async fetchData() {
                try {
                    const response = await fetch("{{ route('reports.sales_branch') }}", {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        this.branches = data.branches;
                        
                        // Trigger reflow for progress bar animation
                        setTimeout(() => {
                            window.dispatchEvent(new Event('resize'));
                        }, 50);
                    }
                } catch (error) {
                    console.error('Failed to fetch branch data:', error);
                } finally {
                    this.isLoading = false;
                }
            }
        }));
    });
</script>
@endsection
