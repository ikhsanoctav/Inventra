@extends('layouts.app')

@section('header_title', 'Master Shift Kerja')

@section('main_content')
<div class="space-y-6" x-data="{ showModal: false, editMode: false, item: {} }">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('system.schedules.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center text-sm font-semibold transition-colors">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke Jadwal
                </a>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 font-headline">Master Shift Kerja</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola daftar jam shift operasional yang tersedia</p>
        </div>
        <div>
            <button @click="showModal = true; editMode = false; item = { color_hex: '#4f46e5' }" class="h-10 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold flex items-center gap-2 shadow-sm shadow-indigo-500/20 transition-colors">
                <span class="material-symbols-outlined text-[18px]">add</span> Tambah Shift
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3">
        <span class="material-symbols-outlined text-emerald-600">check_circle</span>
        <p class="mt-0.5">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Nama Shift</th>
                        <th class="px-6 py-4">Jam Mulai</th>
                        <th class="px-6 py-4">Jam Selesai</th>
                        <th class="px-6 py-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($shifts as $shift)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-4 h-4 rounded-full shadow-sm" style="background-color: {{ $shift->color_hex }}"></div>
                                <span class="font-semibold text-slate-800">{{ $shift->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono">{{ $shift->start_time->format('H:i') }}</td>
                        <td class="px-6 py-4 font-mono">{{ $shift->end_time->format('H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="item = { id: {{ $shift->id }}, name: '{{ $shift->name }}', start_time: '{{ $shift->start_time->format('H:i') }}', end_time: '{{ $shift->end_time->format('H:i') }}', color_hex: '{{ $shift->color_hex }}' }; editMode = true; showModal = true" class="w-8 h-8 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 flex items-center justify-center transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                                
                                <form action="{{ route('system.schedules.shifts.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus shift ini? Semua jadwal yang menggunakan shift ini juga akan terpengaruh.');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                <span class="material-symbols-outlined text-3xl">schedule</span>
                            </div>
                            <h4 class="text-slate-800 font-semibold mb-1">Belum ada Shift</h4>
                            <p class="text-sm text-slate-500">Tambahkan jam shift kerja (misal: Shift Pagi, Shift Malam) untuk ditugaskan ke karyawan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div x-show="showModal" x-transition.scale.origin.bottom class="inline-block w-full max-w-md px-4 pt-5 pb-4 text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:p-6 border border-slate-100 relative z-10">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-800 font-headline" x-text="editMode ? 'Edit Shift' : 'Tambah Shift Baru'"></h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <form :action="editMode ? '{{ url('system/schedules/shifts') }}/' + item.id : '{{ route('system.schedules.shifts.store') }}'" method="POST">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <div class="space-y-4 mb-6 text-sm">
                        
                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">Nama Shift <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="item.name" placeholder="Cth: Shift Pagi" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="font-medium text-slate-700">Jam Mulai <span class="text-red-500">*</span></label>
                                <input type="time" name="start_time" x-model="item.start_time" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                            <div class="space-y-1.5">
                                <label class="font-medium text-slate-700">Jam Selesai <span class="text-red-500">*</span></label>
                                <input type="time" name="end_time" x-model="item.end_time" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">Warna Label</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="color_hex" x-model="item.color_hex" class="h-10 w-14 rounded cursor-pointer border-0 p-0">
                                <span class="text-xs text-slate-500">Pilih warna untuk membedakan shift di kalender.</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-6 flex gap-3 justify-end">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors shadow-sm shadow-indigo-500/20">
                            Simpan Shift
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
