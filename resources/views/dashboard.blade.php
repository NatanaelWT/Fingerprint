<x-app-layout>
    {{-- ISI HALAMAN FULL WIDTH DAN FULL HEIGHT --}}
    <div class="flex flex-col md:flex-row h-screen w-full bg-gray-900 overflow-hidden">
        
        {{-- KIRI --}}
        <div class="w-full md:w-1/2 flex flex-col justify-center px-8 md:px-20 py-10 md:py-16 space-y-6 bg-[#0F172A]">
            <h1 class="text-3xl md:text-5xl font-bold text-white leading-tight tracking-wide">
                Selamat Datang di<br>Dashboard Pengmas <span class="text-indigo-400">Fingerprint</span>
            </h1>
            <p class="text-base md:text-lg text-slate-300 leading-relaxed tracking-normal">
                Kelola data <span class="text-white font-medium">kehadiran</span>, 
                <span class="text-white font-medium">siswa</span>, dan 
                <span class="text-white font-medium">staff</span> melalui dashboard ini.
                <br>
                Gunakan menu di atas untuk mengelola data dan aktivitas Anda.
            </p>

            {{-- Ringkasan Kehadiran Hari Ini --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-800 p-4 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <p class="text-gray-300 text-sm">Total Siswa</p>
                    <p class="text-2xl font-bold text-white">{{ $totalStudents }}</p>
                </div>
                <div class="bg-gray-800 p-4 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <p class="text-gray-300 text-sm">Hadir Hari Ini</p>
                    <p class="text-2xl font-bold text-green-400">{{ $presentCount }}</p>
                </div>
                <div class="bg-gray-800 p-4 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <p class="text-gray-300 text-sm">Sudah Pulang</p>
                    <p class="text-2xl font-bold text-blue-400">{{ $checkOutCount }}</p>
                </div>
                <div class="bg-gray-800 p-4 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <p class="text-gray-300 text-sm">Tidak Hadir</p>
                    <p class="text-2xl font-bold text-red-400">{{ $totalStudents - $presentCount }}</p>
                </div>
            </div>
        </div>

        {{-- KANAN --}}
        <div class="w-full md:w-1/2 h-64 md:h-auto bg-gray-800 relative overflow-hidden"
             x-data="{
                active: 0,
                images: [
                    '{{ asset('images/1.jpeg') }}',
                    '{{ asset('images/2.jpeg') }}',
                    '{{ asset('images/3.jpeg') }}'
                ]
             }" 
             x-init="setInterval(() => active = (active + 1) % images.length, 3000)">
            
            <template x-for="(image, index) in images" :key="index">
                <img :src="image" x-show="active === index"
                     class="w-full h-full object-cover absolute inset-0 transition-opacity duration-700"
                     x-transition:enter="transition-opacity ease-in duration-700"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-out duration-700"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
            </template>
        </div>
    </div>
</x-app-layout>