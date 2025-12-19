<div>
    <!-- Calendar Header -->
    <div class="card">
        <div class="d-flex justify-content-between card-header font-weight-bold">
            <!-- Previous Month Button -->
            <button wire:click="previousMonth" class="btn btn-primary">
                <i class="fa fa-chevron-left"></i> {{ __('Previous') }}
            </button>

            <div class="text-center">
                {{ __('Attendance for ') }} {{ $student->user->name }} - {{ $currentDate->format('F Y') }}
            </div>

            <!-- Next Month Button (Disabled if at the current month) -->
            <button wire:click="nextMonth" class="btn btn-primary" {{ !$canViewNextMonth ? 'disabled' : '' }}>
                {{ __('Next') }} <i class="fa fa-chevron-right"></i>
            </button>
        </div>

        <!-- Calendar Body -->
        <div class="card-body">
            <div class="calendar">
                <!-- Weekday Headers -->
                <div class="text-center d-flex border-bottom font-weight-bold">
                    @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                        <div class="p-2 border flex-fill">{{ $day }}</div>
                    @endforeach
                </div>

                <!-- Calendar Days -->
                <div class="flex-wrap d-flex">
                    @php
                        use Carbon\Carbon;

                        $startOfMonth = $currentDate->copy()->startOfMonth();
                        $endOfMonth = $currentDate->copy()->endOfMonth();
                        $startDay = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
                        $endDay = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);
                    @endphp

                    @for ($date = $startDay; $date->lte($endDay); $date->addDay())
                        @php
                            $isCurrentMonth = $date->isSameMonth($currentDate);
                            $attendance = $isCurrentMonth ? ($attendanceData[$date->format('Y-m-d')] ?? null) : null;
                            $status = $attendance ? $attendance['status'] : null;

                            // Define color based on status
                            $color = match ($status) {
                                'present' => 'bg-success text-white',
                                'absent' => 'bg-danger text-white',
                                'excused' => 'bg-warning text-dark',
                                default => 'bg-light'
                            };

                            // Label text
                            $statusText = match ($status) {
                                'present' => '✔',
                                'absent' => '✘',
                                'excused' => 'E',
                                default => ''
                            };
                        @endphp

                        <!-- Calendar Day Cell -->
                        <div class="border p-3 text-center flex-fill {{ $color }} {{ !$isCurrentMonth ? 'text-muted' : '' }}" style="width: 14.28%;">
                            <div>{{ $date->day }}</div>
                            @if($status)
                                <div class="text-white font-weight-bold">{{ $statusText }}</div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Attendance Legend -->
        <div class="text-center text-white card-footer">
            <strong>{{ __('Legend:') }}</strong>
            <span class="mx-2 badge bg-success">✔ Present</span>
            <span class="mx-2 badge bg-danger">✘ Absent</span>
            <span class="mx-2 badge bg-warning">E Excused</span>
        </div>
    </div>

    <!-- Date Picker for Selecting a Specific Date -->
    <input type="text" hidden id="calendarDatePicker" wire:model="calendarDate" class="mt-3 form-control" />
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#calendarDatePicker", {
            onChange: function(selectedDates, dateStr, instance) {
                @this.set('calendarDate', dateStr);  // Livewire component update
            }
        });
    </script>
@endpush
