{{-- <div class="card"> --}}
    
    <div class="pt-4 col-md-6">
        <h4>List Of Subjects</h4>
        @if ($streamSubjects->isEmpty())
            <p>No Subjects  available.</p>
        @else
            <ul class="list-group">
                @foreach ($streamSubjects as $assignment)
                    <li class="list-group-item">
                        <strong>{{ $assignment->title }}</strong>
                        <p>Subject Name : <a href="{{ route('subjects.show',['subject'=>$assignment->subject->id]) }}">{{ $assignment->subject->name }}</a></p>
                        <p>Teacher Name : {{ $assignment->teacher->name }}</p>
                        <p>Teacher Phone : {{ $assignment->teacher->phone }}</p>
                        {{-- <small>Created on: {{ $assignment->created_at->format('M d, Y') }}</small>
                        <a href="{{ route('assignments.show',['assignment'=>$assignment->id]) }}" class="text-white badge bg-primary">
                            <i class="bi bi-eye"></i> View
                        </a> --}}
                    </li>
                @endforeach
            </ul>


            <button class="my-2 btn btn-primary" wire:click='loadMore()'>See More</button>
            <div wire:loading wire:target="loadMore" class="text-center">
                <div class="lds">
                    <div></div><div></div><div></div><div></div>
                </div>
            </div>
        @endif
      </div>
    {{-- </div> --}}