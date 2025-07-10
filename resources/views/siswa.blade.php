<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Siswa') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="text-gray-900 dark:text-gray-100">
        <!-- Header dan tombol tambah + export -->
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg font-semibold">Daftar Siswa</h3>
          <div class="flex gap-2">
            <!-- Tombol Export Excel -->
            <form method="GET" action="{{ route('siswa.export') }}">
              <input type="hidden" name="kelas" value="{{ request('kelas') }}">
              <input type="hidden" name="tahun" value="{{ request('tahun') }}">
              <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">
              <input type="hidden" name="search" value="{{ request('search') }}">
              <button type="submit" class="bg-blue-800 hover:bg-blue-900 text-white font-semibold py-2 px-4 rounded">
                Export Excel
              </button>
            </form>

            <!-- Tombol Tambah Siswa -->
            @if (optional(Auth::user())->name === 'Admin')
              <a href="{{ route('siswa.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                + Tambah Siswa
              </a>
            @endif
          </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" class="mb-6 flex flex-wrap gap-4">
          <!-- Filter Kelas -->
          <div>
            <label for="kelas" class="block mb-1 text-sm font-medium">Kelas</label>
            <select name="kelas" id="kelas"
              class="p-2 w-48 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
              <option value="">Semua</option>
              @foreach ($kelasList as $k)
                <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Filter Tahun -->
          <div>
            <label for="tahun" class="block mb-1 text-sm font-medium">Tahun Ajaran</label>
            <input type="number" name="tahun" id="tahun" value="{{ request('tahun', now()->year) }}"
              class="p-2 w-32 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
          </div>

          <!-- Filter Tanggal -->
          <div>
            <label for="tanggal" class="block mb-1 text-sm font-medium">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal', now()->toDateString()) }}"
              class="p-2 w-48 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
          </div>

          <!-- Pencarian -->
          <div>
            <label for="search" class="block mb-1 text-sm font-medium">Pencarian (Nama/NIS)</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari..."
              class="p-2 w-56 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
          </div>

          <!-- Tombol Filter -->
          <div class="flex items-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
              Filter
            </button>
          </div>
        </form>

        <!-- Tabel Siswa -->
        <div class="overflow-x-auto">
          <table class="min-w-full table-auto border-collapse border border-gray-300 dark:border-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700">
              <tr>
                @if (optional(Auth::user())->name === 'Admin')
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left">Aksi</th>
                @endif
                <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left">NIS</th>
                <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left">Nama</th>
                <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left">Kelas</th>
                <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left">Alamat</th>
                <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left">Nomor Orang Tua</th>
                <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left">Jenis Kelamin</th>
                <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left">Tahun</th>
                <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left">ID Template</th>
                <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left">Masuk</th>
                <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left">Pulang</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($siswa as $s)
                <tr>
                  @if (optional(Auth::user())->name === 'Admin')
                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700">
                      <a href="{{ route('siswa.edit', $s->id) }}"
                        class="text-blue-500 hover:text-blue-700 mr-2">Edit</a>
                    </td>
                  @endif
                  <td class="px-4 py-2 border border-gray-300 dark:border-gray-700">{{ $s->nis }}</td>
                  <td class="px-4 py-2 border border-gray-300 dark:border-gray-700">{{ $s->nama }}</td>
                  <td class="px-4 py-2 border border-gray-300 dark:border-gray-700">{{ $s->kelas }}</td>
                  <td class="px-4 py-2 border border-gray-300 dark:border-gray-700">{{ $s->alamat }}</td>
                  <td class="px-4 py-2 border border-gray-300 dark:border-gray-700">{{ $s->nomor_ortu }}</td>
                  <td class="px-4 py-2 border border-gray-300 dark:border-gray-700">{{ $s->jenis_kelamin }}</td>
                  <td class="px-4 py-2 border border-gray-300 dark:border-gray-700">{{ $s->tahun }}</td>
                  <td class="px-4 py-2 border border-gray-300 dark:border-gray-700">{{ $s->id_template }}</td>
                  <td class="px-4 py-2 border border-gray-300 dark:border-gray-700">{{ $s->masuk }}</td>
                  <td class="px-4 py-2 border border-gray-300 dark:border-gray-700">{{ $s->pulang }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="10"
                    class="px-4 py-4 border border-gray-300 dark:border-gray-700 text-center text-gray-500 dark:text-gray-400">
                    Tidak ada data siswa yang ditemukan.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</x-app-layout>
