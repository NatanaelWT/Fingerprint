<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Dashboard Absensi Fingerprint') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      
      {{-- Ringkasan Kehadiran Hari Ini --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
          <p class="text-gray-500 dark:text-gray-300 text-sm">Siswa Hadir</p>
          <p class="text-3xl font-bold text-green-600 dark:text-green-400">120</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
          <p class="text-gray-500 dark:text-gray-300 text-sm">Staff Hadir</p>
          <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">30</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
          <p class="text-gray-500 dark:text-gray-300 text-sm">Terlambat</p>
          <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">8</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
          <p class="text-gray-500 dark:text-gray-300 text-sm">Tidak Hadir</p>
          <p class="text-3xl font-bold text-red-600 dark:text-red-400">15</p>
        </div>
      </div>

      {{-- Grafik Dummy Kehadiran (bisa pakai Chart.js jika dinamis) --}}
      <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Statistik Kehadiran Minggu Ini</h3>
        <div class="h-40 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center text-gray-400 dark:text-gray-300">
          Grafik Kehadiran (placeholder)
        </div>
      </div>

      {{-- Daftar Top 5 Datang Paling Awal --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
          <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">5 Siswa Datang Paling Awal</h3>
          <ol class="list-decimal list-inside text-gray-700 dark:text-gray-300 space-y-1">
            <li>Ani - 06:45</li>
            <li>Budi - 06:47</li>
            <li>Citra - 06:49</li>
            <li>Dedi - 06:50</li>
            <li>Eva - 06:51</li>
          </ol>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
          <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">5 Guru Datang Paling Awal</h3>
          <ol class="list-decimal list-inside text-gray-700 dark:text-gray-300 space-y-1">
            <li>Pak Andi - 06:30</li>
            <li>Bu Rina - 06:32</li>
            <li>Pak Joko - 06:35</li>
            <li>Bu Sari - 06:36</li>
            <li>Pak Dani - 06:38</li>
          </ol>
        </div>
      </div>

    </div>
  </div>
</x-app-layout>
