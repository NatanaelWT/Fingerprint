<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Staf') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="text-gray-900 dark:text-gray-100">

        <!-- Header dan tombol tambah -->
        @if (optional(Auth::user())->name === 'Admin')
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold">Daftar Staff</h3>
            <a href="{{ route('staff.create') }}"
              class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
              + Tambah Staff
            </a>
          </div>
        @endif

        <!-- Filter Form -->
        <form method="GET" class="mb-6 flex flex-wrap gap-4 items-end">
          <!-- Filter Tanggal -->
          <div>
            <label for="tanggal" class="block mb-1 text-sm font-medium">Filter Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}"
              class="p-2 w-48 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium">Pencarian (Nama/NIP)</label>
            <input name="search" placeholder="Cari..." value="{{ request('search') }}"
              class="p-2 w-48 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
          </div>
          <!-- Tombol Filter -->
          <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
              Filter
            </button>
          </div>
        </form>

        <!-- Tabel Staff -->
        <div class="overflow-auto">
          <div class="rounded shadow">
            <table class="min-w-full table-auto border-collapse border border-gray-300 dark:border-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                  @if (optional(Auth::user())->name === 'Admin')
                    <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Aksi</th>
                  @endif
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">NIP</th>
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Nama</th>
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Jabatan</th>
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Alamat</th>
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Nomor Telepon</th>
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Jenis Kelamin</th>
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Masuk</th>
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Pulang</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($staff as $s)
                  <tr>
                    @if (optional(Auth::user())->name === 'Admin')
                      <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">
                        <a href="{{ route('staff.edit', $s->id) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                      </td>
                    @endif
                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">{{ $s->nip }}
                    </td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-left">{{ $s->nama }}</td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">{{ $s->jabatan }}
                    </td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-left">{{ $s->alamat }}</td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">
                      {{ $s->nomor_telepon }}</td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">
                      {{ $s->jenis_kelamin }}</td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">{{ $s->masuk }}
                    </td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">{{ $s->pulang }}
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9"
                      class="px-4 py-4 border border-gray-300 dark:border-gray-700 text-center text-gray-500 dark:text-gray-400">
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
