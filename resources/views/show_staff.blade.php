<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Detail Staff') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold">Informasi Staff</h3>
            <a href="{{ route('staff.index') }}"
              class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded">Kembali</a>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">Nama</p>
              <p class="font-medium">{{ $staff->nama }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">Jabatan</p>
              <p class="font-medium">{{ $staff->jabatan }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">Nomor Telepon</p>
              <p class="font-medium">{{ $staff->nomor_telepon ?? '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">Jenis Kelamin</p>
              <p class="font-medium">{{ $staff->jenis_kelamin ?? '-' }}</p>
            </div>
          </div>

          <div class="border-t border-gray-200 dark:border-gray-700 mb-6"></div>

          <div class="flex items-center justify-between mb-4">
            <div>
              <h4 class="text-md font-semibold">Rekap Kehadiran</h4>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ $monthLabel }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <form method="GET" action="{{ route('staff.show', $staff->id) }}" class="flex items-center gap-2">
                <input type="month" name="bulan" value="{{ $month }}"
                  class="p-2 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                  Tampilkan
                </button>
              </form>
              <form method="GET" action="{{ route('staff.export.staff-month', $staff->id) }}">
                <input type="hidden" name="bulan" value="{{ $month }}">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">
                  Export Excel
                </button>
              </form>
            </div>
          </div>

          @php
            $statusPalette = [
              'Hadir' => [
                'bg' => '#dcfce7',
                'border' => '#86efac',
                'text' => '#166534',
                'accent' => '#22c55e',
                'badge' => '#bbf7d0',
              ],
              'Telat' => [
                'bg' => '#ffedd5',
                'border' => '#fdba74',
                'text' => '#c2410c',
                'accent' => '#f97316',
                'badge' => '#fed7aa',
              ],
              'Tidak Absen' => [
                'bg' => '#fee2e2',
                'border' => '#fecaca',
                'text' => '#b91c1c',
                'accent' => '#ef4444',
                'badge' => '#fecaca',
              ],
              'Belum' => [
                'bg' => '#e2e8f0',
                'border' => '#cbd5e1',
                'text' => '#475569',
                'accent' => '#94a3b8',
                'badge' => '#e2e8f0',
              ],
            ];

            $summaryPalette = [
              'Hadir' => $statusPalette['Hadir'],
              'Telat' => $statusPalette['Telat'],
              'Tidak Hadir' => $statusPalette['Tidak Absen'],
            ];
          @endphp

          <div class="flex flex-nowrap gap-4 mb-4 overflow-x-auto">
            <div class="min-w-[170px] flex-1 border rounded-lg p-4"
              style="background-color: {{ $summaryPalette['Hadir']['bg'] }}; border-color: {{ $summaryPalette['Hadir']['border'] }};">
              <p class="text-sm" style="color: {{ $summaryPalette['Hadir']['text'] }};">Hadir</p>
              <p class="text-2xl font-semibold" style="color: {{ $summaryPalette['Hadir']['text'] }};">{{ $summary['hadir'] }}</p>
            </div>
            <div class="min-w-[170px] flex-1 border rounded-lg p-4"
              style="background-color: {{ $summaryPalette['Telat']['bg'] }}; border-color: {{ $summaryPalette['Telat']['border'] }};">
              <p class="text-sm" style="color: {{ $summaryPalette['Telat']['text'] }};">Telat</p>
              <p class="text-2xl font-semibold" style="color: {{ $summaryPalette['Telat']['text'] }};">{{ $summary['telat'] }}</p>
            </div>
            <div class="min-w-[170px] flex-1 border rounded-lg p-4"
              style="background-color: {{ $summaryPalette['Tidak Hadir']['bg'] }}; border-color: {{ $summaryPalette['Tidak Hadir']['border'] }};">
              <p class="text-sm" style="color: {{ $summaryPalette['Tidak Hadir']['text'] }};">Tidak Hadir</p>
              <p class="text-2xl font-semibold" style="color: {{ $summaryPalette['Tidak Hadir']['text'] }};">{{ $summary['tidak_absen'] }}</p>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600 dark:text-gray-300 mb-3">
            <span class="inline-flex items-center px-2 py-0.5 rounded font-medium"
              style="background-color: {{ $statusPalette['Hadir']['badge'] }}; color: {{ $statusPalette['Hadir']['text'] }};">
              Hadir
            </span>
            <span class="inline-flex items-center px-2 py-0.5 rounded font-medium"
              style="background-color: {{ $statusPalette['Telat']['badge'] }}; color: {{ $statusPalette['Telat']['text'] }};">
              Telat
            </span>
            <span class="inline-flex items-center px-2 py-0.5 rounded font-medium"
              style="background-color: {{ $statusPalette['Tidak Absen']['badge'] }}; color: {{ $statusPalette['Tidak Absen']['text'] }};">
              Tidak Hadir
            </span>
            <span class="inline-flex items-center px-2 py-0.5 rounded font-medium"
              style="background-color: {{ $statusPalette['Belum']['badge'] }}; color: {{ $statusPalette['Belum']['text'] }};">
              Belum
            </span>
          </div>

          @php
            $dayNames = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            $dayHeaderClasses = [
              'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-900/40 dark:text-sky-200 dark:border-sky-800',
              'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-200 dark:border-emerald-800',
              'bg-lime-50 text-lime-700 border-lime-200 dark:bg-lime-900/40 dark:text-lime-200 dark:border-lime-800',
              'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/40 dark:text-amber-200 dark:border-amber-800',
              'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-900/40 dark:text-orange-200 dark:border-orange-800',
              'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/40 dark:text-rose-200 dark:border-rose-800',
              'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-900/40 dark:text-teal-200 dark:border-teal-800',
            ];
            $dayBadgeClasses = [
              'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-200',
              'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200',
              'bg-lime-100 text-lime-700 dark:bg-lime-900/40 dark:text-lime-200',
              'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200',
              'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-200',
              'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200',
              'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-200',
            ];
          @endphp

          <div class="mb-8 rounded-2xl border border-slate-200 dark:border-slate-700 bg-gradient-to-br from-slate-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-4 shadow">
            <table class="w-full table-fixed border-separate border-spacing-2 text-sm">
              <thead>
                <tr class="text-center text-xs font-semibold text-gray-600 dark:text-gray-300">
                  @foreach ($dayNames as $idx => $dayName)
                    <th class="px-1 py-1">
                      <div class="rounded-lg border px-2 py-2 {{ $dayHeaderClasses[$idx] ?? '' }}">
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
                            $entry = $attendanceByDate[$dateKey] ?? null;
                            $isFuture = $day->isFuture();
                            $status = $isFuture ? 'Belum' : ($entry['status'] ?? 'Tidak Absen');
                            $masuk = $entry['masuk'] ?? null;
                            $pulang = $entry['pulang'] ?? null;

                            $displayStatus = $status === 'Tidak Absen' ? 'Tidak Hadir' : $status;
                            $dayIndex = $day->dayOfWeekIso - 1;
                            $dayBadgeClass = $dayBadgeClasses[$dayIndex] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200';
                            $palette = $statusPalette[$status] ?? $statusPalette['Belum'];
                            $badgeStyle = "background-color: {$palette['badge']}; color: {$palette['text']};";
                          @endphp
                          <div class="min-h-[110px] p-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm transition hover:shadow-md {{ $day->isToday() ? 'ring-2 ring-blue-500' : '' }}">
                            <div class="flex items-center justify-between mb-2">
                              <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $dayBadgeClass }}">
                                {{ $day->day }}
                              </span>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                              style="{{ $badgeStyle }}">
                              {{ $displayStatus }}
                            </span>
                            <div class="mt-2 text-xs text-gray-600 dark:text-gray-300 space-y-1">
                              <div>Masuk: {{ $masuk ?? '-' }}</div>
                              <div>Pulang: {{ $pulang ?? '-' }}</div>
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
    </div>
  </div>
</x-app-layout>
