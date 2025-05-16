<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Tambah Siswa') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          <form method="POST" action="{{ route('siswa.store') }}">
            @csrf

            <div class="mb-4 md:flex md:space-x-4">
              <!-- Tahun -->
              <div class="md:w-1/3 mb-4 md:mb-0">
                <label for="tahun" class="block font-medium">Tahun</label>
                <input type="number" name="tahun" id="tahun" value="{{ old('tahun', date('Y')) }}"
                  class="w-full p-2 rounded dark:bg-gray-700 dark:text-white" required>
              </div>

              <!-- NIS -->
              <div class="md:w-1/3 mb-4 md:mb-0">
                <label class="block font-medium mb-1" for="nis">NIS</label>
                <input type="text" name="nis" id="nis"
                  class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white" required>
              </div>

              <!-- Kelas -->
              <div class="md:w-1/3">
                <label class="block font-medium mb-1" for="kelas">Kelas</label>
                <input type="text" name="kelas" id="kelas"
                  class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white" required>
              </div>
            </div>


            <div class="mb-4">
              <label class="block font-medium mb-1" for="nama">Nama</label>
              <input type="text" name="nama" id="nama"
                class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white" required>
            </div>

            <div class="mb-4">
              <label class="block font-medium mb-1" for="alamat">Alamat</label>
              <textarea name="alamat" id="alamat" class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white" required></textarea>
            </div>

            <div class="mb-4 md:flex md:space-x-4">
              <!-- Nomor Telepon -->
              <div class="md:w-1/3 mb-4 md:mb-0">
                <label class="block font-medium mb-1" for="nomor_telepon">Nomor Telepon</label>
                <input type="text" name="nomor_telepon" id="nomor_telepon"
                  class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white" required>
              </div>

              <!-- Tanggal Lahir -->
              <div class="md:w-1/3 mb-4 md:mb-0">
                <label class="block font-medium mb-1" for="tanggal_lahir">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                  class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white" required>
              </div>

              <!-- Jenis Kelamin -->
              <div class="md:w-1/3">
                <label class="block font-medium mb-1" for="jenis_kelamin">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin"
                  class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white" required>
                  <option value="">-- Pilih --</option>
                  <option value="Laki-laki">Laki-laki</option>
                  <option value="Perempuan">Perempuan</option>
                </select>
              </div>
            </div>


            <div class="flex justify-end">
              <a href="{{ route('siswa.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded mr-2">Batal</a>
              <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
