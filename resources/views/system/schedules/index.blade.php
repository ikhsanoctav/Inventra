@extends('layouts.app')

@section('header_title', 'Jadwal & Shift Kerja')

@section('main_content')
<div class="space-y-6" x-data="{ showModal: false, showShiftModal: false, selectedUser: null, selectedDate: null, selectedShift: '' }">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 font-headline">Jadwal & Shift Kerja</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola pembagian shift kerja karyawan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('system.schedules.shifts') }}" class="h-10 px-4 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold flex items-center gap-2 shadow-sm transition-colors">
                <span class="material-symbols-outlined text-[18px]">settings</span> Master Shift
            </a>
            <button @click="showModal = true" class="h-10 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold flex items-center gap-2 shadow-sm shadow-indigo-500/20 transition-colors">
                <span class="material-symbols-outlined text-[18px]">add</span> Tugaskan Shift
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
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h3 class="font-bold text-slate-800">Roster Bulan {{ Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</h3>
            
            <form action="{{ route('system.schedules.index') }}" method="GET" class="flex gap-2">
                <input type="month" name="month" value="{{ $month }}" class="h-10 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" onchange="this.form.submit()">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 min-w-max">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold border-b border-slate-200 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 sticky left-0 bg-slate-50 border-r border-slate-200 z-10 w-48">Karyawan</th>
                        @php
                            $daysInMonth = $startOfMonth->daysInMonth;
                        @endphp
                        @for($i = 1; $i <= $daysInMonth; $i++)
                            @php
                                $date = $startOfMonth->copy()->addDays($i - 1);
                                $isWeekend = $date->isWeekend();
                            @endphp
                            <th class="px-2 py-3 text-center border-r border-slate-100 {{ $isWeekend ? 'bg-red-50/50 text-red-500' : '' }}">
                                <div class="font-bold">{{ $i }}</div>
                                <div class="text-[10px] opacity-75">{{ substr($date->translatedFormat('D'), 0, 3) }}</div>
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-4 py-3 sticky left-0 bg-white group-hover:bg-slate-50/50 border-r border-slate-200 z-10">
                            <div class="font-semibold text-slate-800 truncate">{{ $user->name }}</div>
                            <div class="text-xs text-slate-500 truncate capitalize">{{ str_replace('_', ' ', $user->role) }}</div>
                        </td>
                        @for($i = 1; $i <= $daysInMonth; $i++)
                            @php
                                $currentDate = $startOfMonth->copy()->addDays($i - 1)->format('Y-m-d');
                                $userSchedule = isset($schedules[$user->id]) ? $schedules[$user->id]->firstWhere('date', clone $startOfMonth->copy()->addDays($i - 1)) : null;
                                $isWeekend = $startOfMonth->copy()->addDays($i - 1)->isWeekend();
                            @endphp
                            <td class="px-1 py-2 text-center border-r border-slate-100 relative {{ $isWeekend ? 'bg-red-50/30' : '' }}">
                                @if($userSchedule && $userSchedule->workShift)
                                    <div class="w-full h-full flex flex-col items-center justify-center cursor-pointer group/cell relative" 
                                         onclick="if(confirm('Hapus jadwal ini?')) { document.getElementById('delete-schedule-{{ $userSchedule->id }}').submit(); }">
                                        
                                        <div class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-bold text-white shadow-sm" 
                                             style="background-color: {{ $userSchedule->workShift->color_hex }}"
                                             title="{{ $userSchedule->workShift->name }} ({{ $userSchedule->workShift->start_time->format('H:i') }} - {{ $userSchedule->workShift->end_time->format('H:i') }})">
                                            {{ substr($userSchedule->workShift->name, 0, 1) }}
                                        </div>
                                        
                                        <!-- Hidden delete form -->
                                        <form id="delete-schedule-{{ $userSchedule->id }}" action="{{ route('system.schedules.destroy', $userSchedule->id) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                @else
                                    <div class="w-full h-full flex items-center justify-center min-h-[24px]">
                                        <button @click="selectedUser = '{{ $user->id }}'; selectedDate = '{{ $currentDate }}'; showModal = true" 
                                                class="w-6 h-6 rounded flex items-center justify-center text-slate-300 hover:bg-indigo-50 hover:text-indigo-600 transition-colors opacity-0 group-hover:opacity-100">
                                            <span class="material-symbols-outlined text-[14px]">add</span>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Penugasan Shift -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div x-show="showModal" x-transition.scale.origin.bottom class="inline-block w-full max-w-md px-4 pt-5 pb-4 text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:p-6 border border-slate-100 relative z-10">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-800 font-headline">Tugaskan Shift</h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <form action="{{ route('system.schedules.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4 mb-6 text-sm">
                        
                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">Karyawan</label>
                            <select name="user_id" x-model="selectedUser" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Pilih Karyawan...</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ str_replace('_', ' ', $u->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">Tanggal</label>
                            <input type="date" name="date" x-model="selectedDate" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="font-medium text-slate-700">Pilih Shift</label>
                            <div class="grid grid-cols-2 gap-2 mt-1">
                                @forelse($shifts as $shift)
                                <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-colors" 
                                       :class="selectedShift == '{{ $shift->id }}' ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 hover:bg-slate-50'">
                                    <input type="radio" name="work_shift_id" value="{{ $shift->id }}" x-model="selectedShift" class="text-indigo-600 focus:ring-indigo-500" required>
                                    <div>
                                        <div class="font-semibold text-slate-800 flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $shift->color_hex }}"></div>
                                            {{ $shift->name }}
                                        </div>
                                        <div class="text-xs text-slate-500 mt-0.5">{{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}</div>
                                    </div>
                                </label>
                                @empty
                                <div class="col-span-2 text-sm text-amber-600 bg-amber-50 p-3 rounded-xl border border-amber-200">
                                    Belum ada Master Shift yang dibuat. Silakan buat Master Shift terlebih dahulu.
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 transition-colors" {{ count($shifts) == 0 ? 'disabled' : '' }}>
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
