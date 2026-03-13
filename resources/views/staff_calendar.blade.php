<x-app-layout>
  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Kalender Kehadiran Staff</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Rekap hadir, telat, dan tidak hadir per tanggal.
          </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <form method="GET" action="{{ route('staff.calendar') }}" class="flex items-center gap-2">
            <input type="month" name="bulan" value="{{ $month }}"
              class="p-2 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
              Tampilkan
            </button>
          </form>
          <form method="GET" action="{{ route('staff.export.month') }}">
            <input type="hidden" name="bulan" value="{{ $month }}">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">
              Rekap Bulanan
            </button>
          </form>
        </div>
      </div>

      <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mb-4">
        <span class="inline-flex items-center gap-2">
          <span style="width: 8px; height: 8px; border-radius: 9999px; background: #22c55e;"></span>
          Hadir
        </span>
        <span class="inline-flex items-center gap-2">
          <span style="width: 8px; height: 8px; border-radius: 9999px; background: #f97316;"></span>
          Telat
        </span>
        <span class="inline-flex items-center gap-2">
          <span style="width: 8px; height: 8px; border-radius: 9999px; background: #ef4444;"></span>
          Tidak Hadir
        </span>
      </div>

      @php
        $dayNames = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
      @endphp

      <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow">
        <table class="w-full table-fixed border-separate border-spacing-2 text-sm">
          <thead>
            <tr class="text-center text-xs font-semibold text-gray-600 dark:text-gray-300">
              @foreach ($dayNames as $dayName)
                <th class="py-2">
                  <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 py-2">
                    {{ $dayName }}
                  </div>
                </th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach ($weeks as $week)
              <tr>
                @foreach ($week as $day)
                  <td class="align-top">
                    @if (!$day)
                      <div class="min-h-[110px] rounded-lg border border-dashed border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30"></div>
                    @else
                      @php
                        $dateKey = $day->toDateString();
                        $stats = $statsByDate[$dateKey] ?? ['hadir' => 0, 'telat' => 0, 'tidak_hadir' => 0];
                        $hadir = $stats['hadir'] ?? 0;
                        $telat = $stats['telat'] ?? 0;
                        $tidakHadir = $stats['tidak_hadir'] ?? 0;
                      @endphp
                      <div class="min-h-[110px] p-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                          <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $day->day }}</span>
                        </div>

                        <div class="space-y-1 text-xs">
                          <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Hadir</span>
                            <span style="color: #22c55e; font-weight: 600;">{{ $hadir }}</span>
                          </div>
                          <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Telat</span>
                            <span style="color: #f97316; font-weight: 600;">{{ $telat }}</span>
                          </div>
                          <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Tidak Hadir</span>
                            <span style="color: #ef4444; font-weight: 600;">{{ $tidakHadir }}</span>
                          </div>
                        </div>
                      </div>
                    @endif
                  </td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-app-layout>
