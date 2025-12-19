@foreach ($students as $student)
@php
    $nameParts = explode(' ', $student->user->name);
    $firstName = $nameParts[0] ?? '';
    $middleName = $nameParts[1] ?? '';
    $surname = $nameParts[2] ?? '';
    $fullName = strtoupper($surname) . ', ' . ucfirst($firstName) . ' ' . ucfirst(substr($middleName, 0, 1)) . '.';

    $gender = trim($student->user->gender);
                if ($gender == 'female') {
                    $gender = 'F'; // Convert Female to 'F'
                }else {
                    $gender = 'M';  // In case of invalid or missing value
                }
@endphp
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $student->reg_number }}</td>
    <td>{{ $fullName }}</td>
    <td>{{ $gender }}</td>
    <td>{{ $student->stream->name ?? 'N/A' }}</td>
    <td>
        @if ($student->payment_status === 'paid')
            <span class="badge badge-success">Paid</span>
        @elseif ($student->payment_status === 'partial')
            <span class="badge badge-warning">Partial</span>
        @else
            <span class="badge badge-danger">Unpaid</span>
        @endif
    </td>
    <td>
        @if ($student->status)
            <span class="badge badge-success">Active</span>
        @else
            <span class="badge badge-danger">Inactive</span>
        @endif
    </td>
    <td>
        <a href="{{ route('students.show',['id'=>$student->id]) }}" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> View</a>
        <a href="{{ route("students.edit",['student'=>$student->id]) }}" @if(auth()->user()->roles->first()->name != 'class teacher') onclick="return false;" @endif  class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Edit</a>
        <form action="{{ route('student.destroy', $student->id) }}" method="POST" style="display: inline-block;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" @if(auth()->user()->roles->first()->name != 'class teacher') disabled   @endif>
                <i class="fa fa-trash"></i> Delete
            </button>
        </form>
    </td>
</tr>
@endforeach