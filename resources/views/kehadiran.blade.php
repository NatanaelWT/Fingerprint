<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Kehadiran') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

      {{-- 5 Tercepat Masuk --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
          <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">5 Guru Datang Paling Awal</h3>
          <ol class="list-decimal list-inside text-gray-700 dark:text-gray-300 space-y-1">
            <li>Pak Andi - 06:30</li>
            <li>Bu Rina - 06:32</li>
            <li>Pak Joko - 06:35</li>
            <li>Bu Sari - 06:36</li>
            <li>Pak Dani - 06:38</li>
          </ol>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
          <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">5 Siswa Datang Paling Awal</h3>
          <ol class="list-decimal list-inside text-gray-700 dark:text-gray-300 space-y-1">
            <li>Ani - 06:45</li>
            <li>Budi - 06:47</li>
            <li>Citra - 06:49</li>
            <li>Dedi - 06:50</li>
            <li>Eva - 06:51</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

      {{-- Tabel Kehadiran Staff (Dulu) --}}
      <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Kehadiran Staff</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
          <div class="bg-green-100 dark:bg-green-800 p-4 rounded">
            <p class="text-sm text-gray-700 dark:text-gray-200 font-medium">Hadir</p>
            <p class="text-xl font-bold text-green-700 dark:text-white">25</p>
          </div>
          <div class="bg-yellow-100 dark:bg-yellow-700 p-4 rounded">
            <p class="text-sm text-gray-700 dark:text-gray-200 font-medium">Sakit</p>
            <p class="text-xl font-bold text-yellow-700 dark:text-white">3</p>
          </div>
          <div class="bg-blue-100 dark:bg-blue-700 p-4 rounded">
            <p class="text-sm text-gray-700 dark:text-gray-200 font-medium">Izin</p>
            <p class="text-xl font-bold text-blue-700 dark:text-white">2</p>
          </div>
          <div class="bg-purple-100 dark:bg-purple-700 p-4 rounded">
            <p class="text-sm text-gray-700 dark:text-gray-200 font-medium">Dispen</p>
            <p class="text-xl font-bold text-purple-700 dark:text-white">0</p>
          </div>
        </div>
      </div>

      {{-- Tabel Kehadiran Siswa (Setelah) --}}
      <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Kehadiran Siswa</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
          <div class="bg-green-100 dark:bg-green-800 p-4 rounded">
            <p class="text-sm text-gray-700 dark:text-gray-200 font-medium">Hadir</p>
            <p class="text-xl font-bold text-green-700 dark:text-white">120</p>
          </div>
          <div class="bg-yellow-100 dark:bg-yellow-700 p-4 rounded">
            <p class="text-sm text-gray-700 dark:text-gray-200 font-medium">Sakit</p>
            <p class="text-xl font-bold text-yellow-700 dark:text-white">10</p>
          </div>
          <div class="bg-blue-100 dark:bg-blue-700 p-4 rounded">
            <p class="text-sm text-gray-700 dark:text-gray-200 font-medium">Izin</p>
            <p class="text-xl font-bold text-blue-700 dark:text-white">12</p>
          </div>
          <div class="bg-purple-100 dark:bg-purple-700 p-4 rounded">
            <p class="text-sm text-gray-700 dark:text-gray-200 font-medium">Dispen</p>
            <p class="text-xl font-bold text-purple-700 dark:text-white">8</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</x-app-layout>
