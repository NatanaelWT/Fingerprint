<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Kehadiran') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

      <a href="{{ route('kehadiran.staff') }}"
         class="block bg-white dark:bg-gray-800 p-6 shadow rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">
        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Kehadiran Staff</h3>
      </a>

      <a href="{{ route('kehadiran.siswa') }}"
         class="block bg-white dark:bg-gray-800 p-6 shadow rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">
        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Kehadiran Siswa</h3>
      </a>

    </div>
  </div>
</x-app-layout>
