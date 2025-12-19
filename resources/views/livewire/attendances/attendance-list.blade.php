<div>
    <div class="card">
        <div class="card-header">
            <h4>Take Attendance - {{ now()->format('d F Y') }}</h4>

            <div class="form-group">
                <label for="stream">Filter by Stream:</label>
                <select wire:model.live="stream" class="form-control">
               
                    @foreach ($streams as $s)
                        <option value="{{ $s->id }}"> {{ \Str::replaceFirst('Form ' ?? 'Class ', '', $s->schoolClass->name) }} {{ $s->alias }} </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form wire:submit.prevent="saveAttendance">
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" wire:model="date" class="form-control">
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Present</th>
                                <th>Absent</th>
                                <th>Late</th>
                                <th>Excused</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $student)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $student->user->name }}</td>
                                    <td><input type="radio" wire:model="attendance.{{ $student->id }}.status" value="present"></td>
                                    <td><input type="radio" wire:model="attendance.{{ $student->id }}.status" value="absent"></td>
                                    <td><input type="radio" wire:model="attendance.{{ $student->id }}.status" value="late"></td>
                                    <td><input type="radio" wire:model="attendance.{{ $student->id }}.status" value="excused"></td>
                                    <td><input type="text" wire:model="attendance.{{ $student->id }}.reason" class="form-control" placeholder="Enter reason"></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No students found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary">Submit Attendance</button>
            </form>
        </div>
    </div>

    <div class="mt-4 card">
        <div class="card-header">
            <h4>Attendance on {{ $date }}:</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Status</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $attendance->student->user->name }}</td>
                            <td>{{ $attendance->student->gender }}</td>
                            <td>{{ ucfirst($attendance->status) }}</td>
                           
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No attendance found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
