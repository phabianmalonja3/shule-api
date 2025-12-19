<div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="text-center card-header font-weight-bold">
                    {{ __('Assignments for ') }} {{ $student->user->name }}
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Student Selection -->
                        <div class="col-md-4">
                            <label for="student">Student Name:</label>
                            <select class="form-control" wire:model="student_id" wire:change="fetchAssignments">
                                <option value="">-- Select Student --</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->user->name }} ({{ $student->reg_number }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Stream Selection -->
                        
                    </div>

                    @if($assignments->isEmpty())
                        <div class="mt-4 text-center">
                            {{ __('No assignments available.') }}
                        </div>
                    @else
                        <div class="pt-2">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Assignment Title</th>
                                            <th>Subject</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1; @endphp
                                        @foreach ($assignments as $assignment)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $assignment->title }}</td>
                                                <td>{{ $assignment->subject->name }}</td>
                                                <td>{{ $assignment->due_date }}</td>
                                                <td>
                                                    @if($assignment->submitted)
                                                        <span class="badge badge-success">Submitted</span>
                                                    @else
                                                        <span class="badge badge-danger">Pending</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div> 
            </div>
        </div>
    </div>
</div>
