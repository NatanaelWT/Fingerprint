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
                <h3 class="text-lg font-semibold mb-4">Daftar Siswa</h3>
                
                <table class="min-w-full table-auto border-collapse">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left">Nama</th>
                            <th class="px-4 py-2 text-left">Alamat</th>
                            <th class="px-4 py-2 text-left">Nomor Telepon</th>
                            <th class="px-4 py-2 text-left">Tanggal Lahir</th>
                            <th class="px-4 py-2 text-left">Jenis Kelamin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($siswa as $s)
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <td class="px-4 py-2">{{ $s->nama }}</td>
                                <td class="px-4 py-2">{{ $s->alamat }}</td>
                                <td class="px-4 py-2">{{ $s->nomor_telepon }}</td>
                                <td class="px-4 py-2">{{ $s->tanggal_lahir }}</td>
                                <td class="px-4 py-2">{{ $s->jenis_kelamin }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
