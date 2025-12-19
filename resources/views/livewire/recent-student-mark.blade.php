<div class="mt-4 col-md-12">
    <h4>Latest Results</h4>
   
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Score</th>
                  
                    <th>Grade</th>
                    <th>Remarks</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($marks as $index =>$mark)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $mark->subject->name }}</td>
                        <td>{{ $mark->obtained_marks }}</td>
                        <td>{{ $mark->grade }}</td>
                        <td class="{{ $mark->remark != 'Fail' ? 'text-success' : 'text-danger' }}">{{ $mark->remark }}</td>
                        <td>{{ $mark->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    {{-- @endif --}}
  </div>