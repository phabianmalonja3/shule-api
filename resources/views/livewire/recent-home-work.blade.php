<div class="col-md-12">
    <h4>Recent Homeworks</h4>
    @if ($recentHomeworks->isEmpty())
        <p>No recent homeworks available.</p>
    @else
        <ul class="list-group">
            @foreach ($recentHomeworks as $homework)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <strong>{{ $homework->title }}</strong>
                    <p>{{ $homework->subject->name }}</p>
                    <small>Due Date: {{\Carbon\Carbon::parse( $homework->due_date)->format('M d, Y') }}</small>
                    <a href="{{ route('homeworks.show', ['homework' => $homework->id]) }}" class="text-white badge bg-success">
                        <i class="bi bi-info-circle"></i> Details
                    </a>
                  </div>
                  @php
                      $submission = $homework->submissions()
                          ->where('student_id', auth()->user()->student->id)
                          ->first();
                  @endphp
                  @if (!$submission)
                      <a href="{{ route('homeworks.show',['homework'=>$homework->id]) }}" class="btn btn-primary btn-sm">Submit</a>
                  @endif
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