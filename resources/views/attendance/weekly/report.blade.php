<x-layout>
    <x-slot:title>
        Weekly Attendance Report
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Weekly Attendance Report ({{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }})</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Student Name</th>
                                                @for ($date = $startDate->copy(); $date <= $endDate; $date->addDay())
                                                    <th>{{ $date->format('D d-m') }}</th>
                                                @endfor
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($students as $student)
                                                <tr>
                                                    <td>{{ $student->user->name }}</td>
                                                    @for ($date = $startDate->copy(); $date <= $endDate; $date->addDay())
                                                        <td>
                                                            @php
                                                                $attendance = $attendances[$student->id]->firstWhere('date', $date->format('Y-m-d')) ?? null;
                                                            @endphp
                                                            @if ($attendance)
                                                                @if($attendance->status == 'present')
                                                                    ✔️
                                                                @elseif($attendance->status == 'absent')
                                                                    ❌
                                                                @else
                                                                    {{ $attendance->status }}
                                                                @endif
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    @endfor
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>
