<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Daftar Log Kehadiran') }}
    </h2>
  </x-slot>

  <div class="py-12">
   <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-gray-800 p-6 rounded-xl shadow-md hover:shadow-lg transition duration-300">
        <div class="flex flex-col items-start">
            <p class="text-sm text-gray-400">Siswa Hadir</p>
            <p class="text-3xl font-bold text-green-400">120</p>
        </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-xl shadow-md hover:shadow-lg transition duration-300">
        <div class="flex flex-col items-start">
            <p class="text-sm text-gray-400">Staff Hadir</p>
            <p class="text-3xl font-bold text-blue-400">30</p>
        </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-xl shadow-md hover:shadow-lg transition duration-300">
        <div class="flex flex-col items-start">
            <p class="text-sm text-gray-400">Siswa Tidak Hadir</p>
            <p class="text-3xl font-bold text-yellow-400">8</p>
        </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-xl shadow-md hover:shadow-lg transition duration-300">
        <div class="flex flex-col items-start">
            <p class="text-sm text-gray-400">Staf Tidak Hadir</p>
            <p class="text-3xl font-bold text-red-400">15</p>
        </div>
    </div>
</div>

        <div class="overflow-x-auto mt-4 rounded-lg shadow-md">
  <table class="min-w-full text-sm text-center text-gray-200 bg-gray-900 border border-gray-700">
    <thead class="text-xs uppercase bg-gray-700 text-gray-300">
      <tr>
        <th class="px-6 py-1">Nis</th>
        <th class="px-6 py-1">Nama</th>
        <th class="px-6 py-1">Kelas</th>
        <th class="px-6 py-1">Waktu</th>
        <th class="px-6 py-1">Keterangan</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-700">
  @forelse($logs as $log)
  <tr class="hover:bg-gray-800 transition-colors duration-200">
    <td class="px-6 py-1 whitespace-nowrap">{{ $log->siswa->nis ?? 'Unknown' }}</td>
    <td class="px-6 py-1 whitespace-nowrap">{{ $log->siswa->nama ?? 'Unknown' }}</td>
    <td class="px-6 py-1 whitespace-nowrap">{{ $log->siswa->kelas ?? 'Unknown' }}</td>
    <td class="px-6 py-1 whitespace-nowrap">{{ $log->check_in->translatedFormat('d F Y H:i:s') }}</td>
    <td class="px-6 py-1 whitespace-nowrap">
      {{-- Contoh logika status hadir --}}
      @if ($log->check_in->format('H:i') <= '07:00')
        <span class="text-green-400">Tepat Waktu</span>
      @elseif ($log->check_in->format('H:i') <= '08:00')
        <span class="text-yellow-400">Terlambat</span>
      @else
        <span class="text-red-400">Sangat Terlambat</span>
      @endif
    </td>
  </tr>
  @empty
  <tr>
    <td colspan="5" class="px-6 py-1 text-gray-400">Tidak ada data kehadiran</td>
  </tr>
  @endforelse
</tbody>
  </table>
</div>

          <!-- Pagination -->
          <div class="mt-4">
            {{ $logs->links() }}
          </div>

        </div>
      </div>
    </div>
  </div>
</x-app-layout>
