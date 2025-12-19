<div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @forelse ($grades as $schoolType => $gradesList)
                <div class="row">
                    <div class="flex px-4 row">
                        <div>
                            <h5 class="px-4">{{ ucfirst($schoolType) }} Grades</h5>
                        </div>
                        <div>
                            {{-- Only display "Confirm All" if there are unconfirmed grades for this school type --}}
                            @isset($confirmedGrades[$schoolType])

                            @else
                                <button class="px-4 mb-4 btn btn-primary create-all-btn"
                                        wire:click="confirmAll('{{ $schoolType }}')">
                                    Confirm All
                                </button>
                            @endisset
                        </div>
                    </div>
                </div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Grade</th>
                        <th>Min Marks</th>
                        <th>Max Marks</th>
                        <th>Remarks</th>
                        @isset($confirmedGrades[$schoolType])  <th>Status</th> @endisset
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($gradesList as $grade)
                        @php
                            $confirmedGrade = collect($confirmedGrades[$schoolType] ?? [])->where('grade', $grade['grade'])->first();
                            $isConfirmed = !is_null($confirmedGrade);

                            $displayGrade = $isConfirmed ? $confirmedGrade : $grade;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $displayGrade['grade'] }}</td>
                            <td>{{ $displayGrade['min_marks'] }}</td>
                            <td>{{ $displayGrade['max_marks'] }}</td>
                            <td class="{{ $grade['grade'] != 'F' ? 'text-success' : 'text-danger' }}">
                                {{  $isConfirmed ? $displayGrade['remarks'] : $grade['remarks'] ?? 'N/A' }}
                            </td>
                            @isset($confirmedGrades[$schoolType])
                                <td>
                                    @if($isConfirmed)
                                        <span class="badge bg-success text-white">
                                            <i class="fas fa-check"></i> Confirmed
                                        </span>
                                    @else
                                        <span class="badge bg-danger text-white">Unconfirmed</span>
                                    @endif
                                </td>
                            @endisset
                            <td>
                                <a href="{{ route('grades.edit',['grade'=>$grade['id']]) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="btn btn-sm btn-primary create-btn"
                                        @if($isConfirmed) disabled @endif
                                        wire:click="confirmGrade({{ $grade['id'] }}, '{{ $schoolType }}')">
                                    <i class="fas fa-plus"></i> Confirm
                                </button>
                            </td>
                        </tr>
@endforeach
                </tbody>
            </table>
        @empty
            <div class="text-center text-muted">No grades found for any school type.</div>
        @endforelse

        <hr>

        {{-- <h5>Confirmed Grades</h5>
        @forelse ($confirmedGrades as $schoolType => $confirmedList)
            <div class="container">
                <h6>{{ ucfirst($schoolType) }} Grades</h6>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Grade</th>
                            <th>Min Marks</th>
                            <th>Max Marks</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($confirmedList as $grade)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $grade['grade'] }}</td>
                                <td>{{ $grade['min_marks'] }}</td>
                                <td>{{ $grade['max_marks'] }}</td>
                                <td class="{{ $grade['grade'] != 'F' ? 'text-success' : 'text-danger' }}">
                                    {{ $grade['remarks'] ?? 'N/A' }}
                                   
                                </td>
                                <td>
                                    <a href="{{ route('grade.edit',['grade'=>$grade['id']]) }}" class="mx-1 btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="text-center text-muted">No grades have been confirmed yet.</div>
        @endforelse --}}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@script
<script>
    $wire.on('gradeConfirmed', (message = 'Grade has been successfully confirmed!') => {
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: 'Grade has been successfully confirmed!',  // Display message dynamically
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,  // Add a progress bar
        });
    });
    $wire.on('erro', (message = 'Grade has been successfully confirmed!',error) => {
        Swal.fire({
  icon: "error",
  title: "Oops...",
  text: "Something went wrong!" + error ,
  footer: '<a href="#">Why do I have this issue?</a>'
});

    });
</script>
@endscript
