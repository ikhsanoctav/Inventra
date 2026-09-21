@extends('layouts.app')



@section('header_title', 'Riwayat Transaksi')



@section('main_content')

<div class="space-y-6" x-data="{ showModal: false }">

    <!-- Header Section -->

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">

        <div>

            <h2 class="font-headline text-2xl font-bold text-[#111827]">Riwayat Transaksi</h2>

            <p class="text-sm text-[#6B7280]">Kelola dan pantau informasi riwayat transaksi dalam sistem logistik.</p>

        </div>

        

        <div class="flex items-center gap-3">

            <div class="relative">

                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] text-[18px]">search</span>

                <input type="text" placeholder="Cari data..." class="w-full sm:w-64 h-10 pl-9 pr-4 text-sm bg-white border border-[#E5E7EB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all shadow-sm">

            </div>

            

            <button @click="showModal = true" class="h-10 px-4 rounded-lg bg-[#4F46E5] hover:bg-[#4338CA] text-white text-sm font-semibold flex items-center gap-2 shadow-sm shadow-indigo-500/20 transition-all shrink-0">

                <span class="material-symbols-outlined text-[18px]">add</span> <span class="hidden sm:inline">Tambah Data</span>

            </button>

        </div>

    </div>



    <!-- Data Table -->

    <div class="bg-white rounded-2xl border border-[#E5E7EB] shadow-sm overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-left text-sm text-[#4B5563]">

                <thead class="bg-[#F8FAFC] text-xs uppercase text-[#6B7280] font-semibold border-b border-[#E5E7EB]">

                    <tr>

                        <th scope="col" class="px-6 py-4">Waktu</th>
                        <th scope="col" class="px-6 py-4">No. Transaksi</th>
                        <th scope="col" class="px-6 py-4">Tipe Mutasi</th>
                        <th scope="col" class="px-6 py-4">SKU</th>
                        <th scope="col" class="px-6 py-4">Perubahan Stok</th>
                        <th scope="col" class="px-6 py-4">User</th>

                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-[#E5E7EB]">

                    <!-- Empty State Placeholder -->

                    <tr>

                        <td colspan="7" class="px-6 py-12 text-center">

                            <div class="flex flex-col items-center justify-center">

                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">

                                    <span class="material-symbols-outlined text-3xl text-[#D1D5DB]">inventory_2</span>

                                </div>

                                <h3 class="text-sm font-semibold text-[#111827] mb-1">Belum ada riwayat transaksi</h3>

                                <p class="text-xs text-[#6B7280]">Data yang Anda tambahkan akan muncul di sini.</p>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        

        <!-- Pagination Placeholder -->

        <div class="px-6 py-4 border-t border-[#E5E7EB] flex items-center justify-between text-xs text-[#6B7280]">

            <span>Menampilkan 0 data</span>

            <div class="flex items-center gap-1">

                <button class="p-1 rounded hover:bg-gray-100 disabled:opacity-50"><span class="material-symbols-outlined text-[18px]">chevron_left</span></button>

                <button class="p-1 rounded hover:bg-gray-100 disabled:opacity-50"><span class="material-symbols-outlined text-[18px]">chevron_right</span></button>

            </div>

        </div>

    </div>



    <!-- Add Data Modal Overlay -->

    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <!-- Background backdrop -->

        <div x-show="showModal" 

             x-transition:enter="ease-out duration-300" 

             x-transition:enter-start="opacity-0" 

             x-transition:enter-end="opacity-100" 

             x-transition:leave="ease-in duration-200" 

             x-transition:leave-start="opacity-100" 

             x-transition:leave-end="opacity-0" 

             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 

             @click="showModal = false"></div>



        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">

            <!-- Modal panel -->

            <div x-show="showModal" 

                 x-transition:enter="ease-out duration-300" 

                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 

                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 

                 x-transition:leave="ease-in duration-200" 

                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 

                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 

                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-[#E5E7EB]">

                

                <div class="bg-white px-6 pb-6 pt-6">

                    <div class="flex items-center justify-between mb-5 border-b border-[#E5E7EB] pb-4">

                        <h3 class="text-lg font-headline font-bold text-[#111827]" id="modal-title">Tambah Riwayat Transaksi</h3>

                        <button @click="showModal = false" class="text-[#9CA3AF] hover:text-[#4B5563] transition-colors rounded-lg p-1 hover:bg-gray-100">

                            <span class="material-symbols-outlined text-[20px]">close</span>

                        </button>

                    </div>

                    

                    <form action="#" method="POST" class="space-y-4">

                        @csrf

                        

                        <div>

                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Waktu</label>

                            <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all" placeholder="Masukkan waktu...">

                        </div>

                        <div>

                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">No. Transaksi</label>

                            <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all" placeholder="Masukkan no. transaksi...">

                        </div>

                        <div>

                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tipe Mutasi</label>

                            <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all" placeholder="Masukkan tipe mutasi...">

                        </div>

                        <div>

                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">SKU</label>

                            <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all" placeholder="Masukkan sku...">

                        </div>

                        <div>

                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Perubahan Stok</label>

                            <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all" placeholder="Masukkan perubahan stok...">

                        </div>

                        <div>

                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">User</label>

                            <input type="text" class="w-full h-10 px-3 text-sm bg-white border border-[#D1D5DB] rounded-lg text-[#111827] placeholder-[#9CA3AF] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all" placeholder="Masukkan user...">

                        </div>

                        

                        <div class="mt-6 sm:flex sm:flex-row-reverse gap-2 pt-4 border-t border-[#E5E7EB]">

                            <button type="button" class="inline-flex w-full justify-center rounded-lg bg-[#4F46E5] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#4338CA] sm:w-auto transition-colors">

                                Simpan Data

                            </button>

                            <button @click="showModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-[#111827] shadow-sm ring-1 ring-inset ring-[#D1D5DB] hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">

                                Batal

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>



<!-- Add Alpine.js for simple modal state management without writing custom JS -->

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

@endsection