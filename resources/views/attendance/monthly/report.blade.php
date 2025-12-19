<x-layout>
    <x-slot:title>
        Monthly Attendance Report
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h4>
                        Monthly Attendance Report for {{ $stream->name }} - {{ $monthName }} {{ $year }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered " style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student Name</th>
                                    @for ($day = 1; $day <= $totalDays; $day++) <th>{{ $day }}</th>
                                        @endfor
                                        <th>Total Present</th>
                                        <th>Total Absent</th>
                                        <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $student)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    @php
    $nameParts = explode(' ', $student->user->name);
    $firstName = $nameParts[0] ?? '';
    $middleName = $nameParts[1] ?? '';
    $surname = $nameParts[2] ?? '';
    $fullName = strtoupper($surname) . ', ' . ucfirst($firstName) . ' ' . ucfirst(substr($middleName, 0, 1)) . '.';

  
@endphp
                                    <td style="width: 500px;">{{  $fullName }}</td>
                                    @for ($day = 1; $day <= $totalDays; $day++) @php $date=date('Y-m-d',
                                        strtotime("$year-$month-$day")); $attendance=$student->
                                        attendances->where('date', $date)->first();
                                        @endphp
                                        <td>
                                            @if ($attendance && $attendance->status == 'present')
                                            <i class="fas fa-check-circle text-success"></i>
                                            @elseif ($attendance && $attendance->status == 'absent')
                                            <i class="fas fa-times-circle text-danger"></i>
                                            @else
                                            <i class="fas fa-minus-circle text-secondary"></i>
                                            @endif
                                        </td>
                                        @endfor
                                        <td>{{ $student->presentDays }}</td>
                                        <td>{{ $totalDays - $student->presentDays }}</td>
                                        <td>{{ number_format($student->attendancePercentage, 2) }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>