<div class="container mt-4">
    <div class="mb-3 row">
        <div class="col-md-6">
            <label for="stream" class="form-label fw-bold">Select Stream:</label>
            <select wire:model="selectedStream" class="form-select">
                <option value="">All Streams</option>
                @foreach ($streams as $stream)
                    <option value="{{ $stream->id }}">
                        {{ $stream->schoolClass->name }} {{ $stream->alias }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="date" class="form-label fw-bold">Select Date:</label>
            <input type="date" wire:model="date" class="form-control">
        </div>
    </div>

    <h3 class="mt-3">Attendance for {{ $date }}</h3>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $index => $student)
                    @php
                        $attendance = $attendances->firstWhere('student_id', $student->id);
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $student->user->name }}</td>
                        <td>
                            <span class="badge {{ $attendance ? 'bg-success' : 'bg-danger' }}">
                                {{ $attendance->status ?? 'Not Taken' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">No students found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
