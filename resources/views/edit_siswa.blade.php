<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Edit Siswa') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          <form method="POST" action="{{ route('siswa.update', $siswa->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4 md:flex md:space-x-4">
              <!-- Tahun -->
              <div class="md:w-1/3 mb-4 md:mb-0">
                <label for="tahun" class="block font-medium">Tahun</label>
                <input type="number" name="tahun" id="tahun" value="{{ old('tahun', $siswa->tahun) }}"
                  class="w-full p-2 rounded dark:bg-gray-700 dark:text-white" required>
              </div>

              <!-- NIS -->
              <div class="md:w-1/3 mb-4 md:mb-0">
                <label class="block font-medium mb-1" for="nis">NIS</label>
                <input type="text" name="nis" id="nis" value="{{ old('nis', $siswa->nis) }}"
                  class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white"
                  inputmode="numeric" pattern="\d*" required
                  oninput="this.value = this.value.replace(/\D/g, '')">
              </div>

              <!-- Kelas -->
              <div class="md:w-1/3">
                <label class="block font-medium mb-1" for="kelas">Kelas</label>
                <input type="text" name="kelas" id="kelas" value="{{ old('kelas', $siswa->kelas) }}"
                  class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white" required>
              </div>
            </div>

            <div class="mb-4">
              <label class="block font-medium mb-1" for="nama">Nama</label>
              <input type="text" name="nama" id="nama" value="{{ old('nama', $siswa->nama) }}"
                class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white"
                required oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
            </div>

            <div class="mb-4">
              <label class="block font-medium mb-1" for="alamat">Alamat</label>
              <textarea name="alamat" id="alamat" class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white" required>{{ old('alamat', $siswa->alamat) }}</textarea>
            </div>

            <div class="mb-4 md:flex md:space-x-4">
              <!-- Nomor Telepon -->
              <div class="md:w-1/3 mb-4 md:mb-0">
                <label class="block font-medium mb-1" for="nomor_ortu">Nomor Orang Tua</label>
                <input type="text" name="nomor_ortu" id="nomor_ortu" value="{{ old('nomor_ortu', $siswa->nomor_ortu) }}"
                  class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white"
                  maxlength="13" inputmode="numeric" pattern="\d*" required
                  oninput="this.value = this.value.replace(/\D/g, '').slice(0, 13)">
              </div>

              <!-- ID Template -->
              <div class="md:w-1/3 mb-4 md:mb-0">
                <label class="block font-medium mb-1" for="id_template">ID Template</label>
                <input type="number" name="id_template" id="id_template"
                  class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white" required
                  value="{{ old('id_template', $siswa->id_template) }}">

                @error('id_template')
                  <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
              </div>

              <!-- Jenis Kelamin -->
              <div class="md:w-1/3">
                <label class="block font-medium mb-1" for="jenis_kelamin">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin"
                  class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white" required>
                  <option value="">-- Pilih --</option>
                  <option value="Laki-laki" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                  <option value="Perempuan" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
              </div>
            </div>

            <div class="flex justify-end">
              <a href="{{ route('siswa.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded mr-2">Batal</a>
              <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>