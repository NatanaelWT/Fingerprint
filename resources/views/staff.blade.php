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
          <form method="GET" class="mb-6 flex flex-wrap gap-4">
            <!-- Filter Jabatan -->
            <div>
              <label for="jabatan" class="block mb-1 text-sm font-medium">Filter Jabatan</label>
              <select name="jabatan" id="jabatan"
                class="p-2 w-48 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
                <option value="">Semua</option>
                @foreach ($jabatanList as $j)
                  <option value="{{ $j }}" {{ request('jabatan') == $j ? 'selected' : '' }}>
                    {{ $j }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- Filter Tahun -->
            <div>
              <label for="tahun" class="block mb-1 text-sm font-medium">Filter Tahun</label>
              <input type="number" name="tahun" id="tahun"
                value="{{ request('tahun', $tahun) }}"
                class="p-2 w-32 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
            </div>

            <!-- Tombol Filter -->
            <div class="flex items-end">
              <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Filter
              </button>
            </div>
          </form>

          <!-- Tabel Staff -->
          <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse border border-gray-200 dark:border-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                  <th class="px-4 py-2 text-left">NIP</th>
                  <th class="px-4 py-2 text-left">Nama</th>
                  <th class="px-4 py-2 text-left">Jabatan</th>
                  <th class="px-4 py-2 text-left">Alamat</th>
                  <th class="px-4 py-2 text-left">Nomor Telepon</th>
                  <th class="px-4 py-2 text-left">Jenis Kelamin</th>
                  <th class="px-4 py-2 text-left">Tahun</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($staff as $s)
                  <tr class="border-t border-gray-200 dark:border-gray-700">
                    <td class="px-4 py-2">{{ $s->nip }}</td>
                    <td class="px-4 py-2">{{ $s->nama }}</td>
                    <td class="px-4 py-2">{{ $s->jabatan }}</td>
                    <td class="px-4 py-2">{{ $s->alamat }}</td>
                    <td class="px-4 py-2">{{ $s->nomor_telepon }}</td>
                    <td class="px-4 py-2">{{ $s->jenis_kelamin }}</td>
                    <td class="px-4 py-2">{{ $s->tahun }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                      Tidak ada data staff yang ditemukan.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</x-app-layout>
