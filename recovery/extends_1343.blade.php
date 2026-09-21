@extends('layouts.guest')
2: 
3: @section('title', 'INVENTRA Dashboard')
4: 
5: @section('body_class', 'bg-[#F8FAFC] text-[#111827] font-body antialiased selection:bg-indigo-500 selection:text-white')
6: 
7: @section('content')
8: <div class="flex h-screen overflow-hidden">
9:     <!-- SIDEBAR -->
10:     @include('layouts.sidebar')
11: 
12:     <!-- MAIN CONTENT AREA -->
13:     <div class="flex-1 flex flex-col h-screen overflow-hidden bg-[#F8FAFC]">
14:         
15:         <!-- TOPBAR -->
16:         @include('layouts.topbar')
17: 
18:         <!-- MAIN SCROLLABLE CONTENT -->
19:         <main class="flex-1 overflow-y-auto p-6 md:p-8 custom-scrollbar">
20:             @yield('main_content')
21:         </main>
22:         
23:     </div>
24: </div>
25: 
26: <style>
27:     /* Custom Scrollbar for better UI */
28:     .custom-scrollbar::-webkit-scrollbar {
29:         width: 6px;
30:         height: 6px;
31:     }
32:     .custom-scrollbar::-webkit-scrollbar-track {
33:         background: transparent;
34:     }
35:     .custom-scrollbar::-webkit-scrollbar-thumb {
36:         background-color: #CBD5E1;
37:         border-radius: 20px;
38:     }
39:     .custom-scrollbar:hover::-webkit-scrollbar-thumb {
40:         background-color: #94A3B8;
41:     }
42: </style>
43: @endsection