<x-app-layout>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="text-gray-900 dark:text-gray-100">

        <!-- Header dan tombol tambah -->
        @if (optional(Auth::user())->name === 'Admin')
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold">Daftar Staff ({{ $staff->count() }})</h3>
            <div class="flex gap-2">
              <a href="{{ route('staff.calendar') }}"
                class="staff-calendar-btn"
                title="Calendar" aria-label="Calendar">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                  <path d="M16 2v4" />
                  <path d="M8 2v4" />
                  <path d="M3 10h18" />
                </svg>
              </a>

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
                        <div class="inline-flex items-center justify-center gap-2">
                          <a href="{{ route('staff.show', $s->id) }}"
                            class="staff-action-btn staff-action-btn--view"
                            title="View" aria-label="View">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                              stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z" />
                              <circle cx="12" cy="12" r="3" />
                            </svg>
                          </a>
                          <a href="{{ route('staff.edit', $s->id) }}"
                            class="staff-action-btn staff-action-btn--edit"
                            title="Edit" aria-label="Edit">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                              stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <path d="M12 20h9" />
                              <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                            </svg>
                          </a>
                        </div>
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

    <style>
      .staff-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.5rem;
        border: 1px solid;
        transition: transform 150ms ease, box-shadow 150ms ease, background-color 150ms ease, color 150ms ease,
          border-color 150ms ease;
      }

      .staff-action-btn:hover {
        transform: translateY(-1px) scale(1.03);
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.15);
      }

      .staff-action-btn:active {
        transform: translateY(0) scale(0.98);
      }

      .staff-action-btn--view {
        background-color: #e0f2fe;
        border-color: #7dd3fc;
        color: #0369a1;
      }

      .staff-action-btn--view:hover {
        background-color: #bae6fd;
        border-color: #38bdf8;
        color: #0c4a6e;
      }

      .staff-action-btn--edit {
        background-color: #dcfce7;
        border-color: #86efac;
        color: #166534;
      }

      .staff-action-btn--edit:hover {
        background-color: #bbf7d0;
        border-color: #4ade80;
        color: #14532d;
      }

      .staff-calendar-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        border: 1px solid #22c55e;
        background-color: #16a34a;
        color: #f0fdf4;
        transition: transform 150ms ease, box-shadow 150ms ease, background-color 150ms ease, color 150ms ease,
          border-color 150ms ease;
      }

      .staff-calendar-btn:hover {
        background-color: #15803d;
        border-color: #16a34a;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 8px 16px rgba(20, 83, 45, 0.25);
      }

      .staff-calendar-btn:active {
        transform: translateY(0);
      }
    </style>

  </div>

</x-app-layout>
