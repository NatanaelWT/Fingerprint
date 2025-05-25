<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Staff Pendidikan') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">

          <!-- Header dan tombol tambah -->
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold">Daftar Staff</h3>
            <a href="{{ route('staff.create') }}"
              class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
              + Tambah Staff
            </a>
          </div>

          <!-- Filter Form -->
          <form method="GET" class="mb-6 flex flex-wrap gap-4 items-end">
            <!-- Fitur Search -->
            <div>
              <label class="block mb-1 text-sm font-medium">Fitur Pencarian</label>
              <input
                placeholder="Cari..."
                class="p-2 w-48 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600"
              >
            </div>

            <!-- Filter Tahun -->
            <div>
              <label for="tahun" class="block mb-1 text-sm font-medium">Filter Tahun</label>
              <input
                type="number"
                name="tahun"
                id="tahun"
                value="{{ old('tahun', $tahun) }}"
                class="p-2 w-32 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600"
              >
            </div>

            <!-- Filter Tanggal -->
            <div>
              <label for="tanggal" class="block mb-1 text-sm font-medium">Filter Tanggal</label>
              <input
                type="date"
                name="tanggal"
                id="tanggal"
                value="{{ old('tanggal', $tanggal) }}"
                class="p-2 w-48 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600"
              >
            </div>

            <!-- Tombol Filter -->
            <div>
              <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Filter
              </button>
            </div>
          </form>

          <!-- Dua tabel berdampingan -->
          <div class="flex flex-col lg:flex-row gap-6 overflow-x-auto">

            <!-- Tabel Staff -->
            <div class="flex-1 min-w-[600px] overflow-auto">
              <div class="bg-white dark:bg-gray-800 rounded shadow p-4">
                <h4 class="text-md font-semibold mb-3">Data Staff</h4>
                <table class="min-w-full table-auto border-collapse border border-gray-300 dark:border-gray-700">
                  <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                      <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">NIP</th>
                      <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Nama</th>
                      <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Jabatan</th>
                      <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Alamat</th>
                      <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Nomor Telepon</th>
                      <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Jenis Kelamin</th>
                      <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Tahun</th>
                      <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Masuk</th>
                      <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Pulang</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($staff as $s)
                      <tr>
                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">{{ $s->nip }}</td>
                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-left">{{ $s->nama }}</td>
                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">{{ $s->jabatan }}</td>
                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-left">{{ $s->alamat }}</td>
                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">{{ $s->nomor_telepon }}</td>
                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">{{ $s->jenis_kelamin }}</td>
                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">{{ $s->tahun }}</td>
                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">{{ $s->masuk }}</td>
                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">{{ $s->pulang }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="9" class="px-4 py-4 border border-gray-300 dark:border-gray-700 text-center text-gray-500 dark:text-gray-400">
                          Tidak ada data staff yang ditemukan.
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Tabel Log -->
            <div class="flex-1 min-w-[600px] overflow-auto">
              <div class="bg-white dark:bg-gray-800 rounded shadow p-4">
                <h4 class="text-md font-semibold mb-3">Log Kehadiran Staff</h4>
                <table class="min-w-full table-auto border-collapse border border-gray-300 dark:border-gray-700">
                  <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                      <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">NIP</th>
                      <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Nama</th>
                      <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Waktu</th>
                      <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Keterangan</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($logs as $log)
                      <tr>
                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">{{ $log->nip }}</td>
                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-left">{{ $log->nama }}</td>
                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">{{ $log->waktu }}</td>
                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-left">{{ $log->keterangan }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="px-4 py-4 border border-gray-300 dark:border-gray-700 text-center text-gray-500 dark:text-gray-400">
                          Tidak ada data kehadiran yang ditemukan.
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

          </div> <!-- End Flex -->
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
