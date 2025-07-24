<x-app-layout>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="text-gray-900 dark:text-gray-100">

        <!-- Header dan tombol tambah -->
        @if (optional(Auth::user())->name === 'Admin')
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold">Daftar Staff ({{ $staff->count() }})</h3>
            <div class="flex gap-2">
              <!-- Export Bulanan Baru -->
              <form method="GET" action="{{ route('staff.export.month') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="month" name="bulan" value="{{ request('bulan', now()->format('Y-m')) }}"
                  class="p-2 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
                <button type="submit"
                  class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded">
                  Rekap
                </button>
              </form>

              <!-- Export Harian -->
              <form method="GET" action="{{ route('staff.export') }}">
                <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                  Export
                </button>
              </form>

              <a href="{{ route('staff.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                + Tambah Staff
              </a>
            </div>
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
            <label class="block mb-1 text-sm font-medium">Pencarian (Nama)</label>
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
            <table id="staffTable"
              class="min-w-full table-auto border-collapse border border-gray-300 dark:border-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                  @if (optional(Auth::user())->name === 'Admin')
                    <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Aksi</th>
                  @endif
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Nama</th>
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Jabatan</th>
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Nomor Telepon</th>
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Jenis Kelamin</th>
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Masuk</th>
                  <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center">Pulang</th>
                </tr>
              </thead>
              <tbody id="staffTableBody">
                @forelse ($staff as $s)
                  <tr>
                    @if (optional(Auth::user())->name === 'Admin')
                      <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">
                        <a href="{{ route('staff.edit', $s->id) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                      </td>
                    @endif
                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-left">{{ $s->nama }}
                    </td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">{{ $s->jabatan }}
                    </td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">
                      {{ $s->nomor_telepon }}</td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center">
                      {{ $s->jenis_kelamin }}</td>
                    @php
                      $warna = 'text-red-600 font-semibold'; // default merah

                      if (!empty($s->masuk) && $s->masuk !== '-') {
                          try {
                              $masukTime = \Carbon\Carbon::parse($s->masuk);
                              $batasMasuk = \Carbon\Carbon::createFromTimeString('07:10');
                              $warna = $masukTime->gt($batasMasuk)
                                  ? 'text-yellow-600 font-semibold'
                                  : 'text-green-600 font-semibold';
                          } catch (\Exception $e) {
                              // tetap merah jika gagal parse
                              $warna = 'text-red-600 font-semibold';
                          }
                      }
                    @endphp

                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-center {{ $warna }}">
                      {{ $s->masuk ?? '-' }}
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
    <div id="pagination" class="mt-4 flex justify-center gap-2 flex-wrap"></div>

    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const table = document.getElementById("staffTableBody");
        const rows = table.querySelectorAll("tr");
        const rowsPerPage = 10;
        const totalPages = Math.ceil(rows.length / rowsPerPage);
        const pagination = document.getElementById("pagination");

        function showPage(page) {
          const start = (page - 1) * rowsPerPage;
          const end = start + rowsPerPage;

          rows.forEach((row, index) => {
            row.style.display = index >= start && index < end ? "" : "none";
          });

          // Highlight current button
          document.querySelectorAll("#pagination button").forEach((btn, idx) => {
            btn.classList.toggle("bg-blue-600", idx === page - 1);
            btn.classList.toggle("text-white", idx === page - 1);
            btn.classList.toggle("bg-gray-200", idx !== page - 1);
            btn.classList.toggle("text-gray-700", idx !== page - 1);
          });
        }

        function setupPagination() {
          pagination.innerHTML = "";
          for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement("button");
            btn.textContent = i;
            btn.className = "px-3 py-1 rounded border border-gray-400 bg-gray-200 text-gray-700 hover:bg-gray-300";
            btn.addEventListener("click", () => showPage(i));
            pagination.appendChild(btn);
          }
        }

        if (rows.length > 0) {
          setupPagination();
          showPage(1);
        }
      });
    </script>

  </div>

</x-app-layout>
