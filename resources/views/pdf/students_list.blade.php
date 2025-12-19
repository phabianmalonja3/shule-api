<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Registration #</th>
            <th>Names</th>
            <th>Gender</th>
            <th>Stream</th>
            <th>Paid</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
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
                } else {
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
                        Paid
                    @elseif ($student->payment_status === 'partial')
                        Partial
                    @else
                        Unpaid
                    @endif
                </td>
                <td>
                    @if ($student->status)
                        Active
                    @else
                        Inactive
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>