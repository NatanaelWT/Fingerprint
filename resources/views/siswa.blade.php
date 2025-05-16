<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Siswa') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Daftar Siswa</h3>
            <a href="{{ route('siswa.create') }}"
              class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
              + Tambah Siswa
            </a>
          </div>
          <form method="GET" class="mb-4 flex flex-wrap gap-4">
            <div>
              <label for="kelas" class="block mb-1">Filter Kelas</label>
              <select name="kelas" id="kelas" class="p-2 rounded dark:bg-gray-700 dark:text-white">
                <option value="">Semua</option>
                @foreach ($kelasList as $k)
                  <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}
                  </option>
                @endforeach
              </select>
            </div>

            <div>
              <label for="tahun" class="block mb-1">Filter Tahun</label>
              <input type="number" name="tahun" id="tahun" value="{{ request('tahun', $tahun) }}"
                class="p-2 rounded dark:bg-gray-700 dark:text-white">
            </div>

            <div class="flex items-end">
              <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
            </div>
          </form>


          <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse">
              <thead>
                <tr>
                  <th class="px-4 py-2 text-left">NIS</th>
                  <th class="px-4 py-2 text-left">Nama</th>
                  <th class="px-4 py-2 text-left">Kelas</th>
                  <th class="px-4 py-2 text-left">Alamat</th>
                  <th class="px-4 py-2 text-left">Nomor Telepon</th>
                  <th class="px-4 py-2 text-left">Tanggal Lahir</th>
                  <th class="px-4 py-2 text-left">Jenis Kelamin</th>
                  <th class="px-4 py-2 text-left">Tahun</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($siswa as $s)
                  <tr class="bg-gray-100 dark:bg-gray-700">
                    <td class="px-4 py-2">{{ $s->nis }}</td>
                    <td class="px-4 py-2">{{ $s->nama }}</td>
                    <td class="px-4 py-2">{{ $s->kelas }}</td>
                    <td class="px-4 py-2">{{ $s->alamat }}</td>
                    <td class="px-4 py-2">{{ $s->nomor_telepon }}</td>
                    <td class="px-4 py-2">{{ $s->tanggal_lahir }}</td>
                    <td class="px-4 py-2">{{ $s->jenis_kelamin }}</td>
                    <td class="px-4 py-2">{{ $s->tahun }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</x-app-layout>
