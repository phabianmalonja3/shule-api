<x-layout>
    <x-slot:title>
        Marks Edit
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="col-6">
                    <div class="p-3 card">
                        <div class="card-body">
                            @php
                                $nameParts = explode(' ', $student->user->name);
                                $firstName = $nameParts[0] ?? '';
                                $middleName = $nameParts[1] ?? '';
                                $surname = $nameParts[2] ?? '';
                                $fullName = strtoupper($surname) . ', ' . ucfirst($firstName) . ' ' . ucfirst(substr($middleName, 0, 1)) . '.';
                            @endphp
                            <h4> Edit Marks for {{ $fullName }}</h4> &nbsp;

                            @if(count($marks) > 0)
                                @php
                                    $accordionKey = null;
                                    $selectedexaminationsType = App\Models\ExaminationType::findOrfail($request->selectedexaminationsType);
                                    if($selectedexaminationsType->name == 'Monthly'){
                                        $accordionKey = $marks->first()->month;
                                    }else{
                                        $accordionKey = 0;
                                    }
                                @endphp

                                @if($request->editStatus == 1)
                                    @php
                                        $mark = $marks->first();
                                    @endphp
                                    <form action="{{ route('marks.update', ['studentId' => $mark->student_id, 'marksId' => $mark->id]) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="editStatus" value="1">
                                        <input type="hidden" name="selectedexaminationsType" value="{{ $request->selectedexaminationsType }}">
                                        <input type="hidden" name="selectedSubject" value="{{ $request->selectedSubject }}">
                                        <input type="hidden" name="selectedClass" value="{{ $request->selectedClass }}">                                        
                                        <input type="hidden" name="selectedStream" value="{{ $request->selectedStream }}">
                                        <input type="hidden" name="search" value="{{ $request->search }}">
                                        <input type="hidden" name="accordionKey" value="{{ $accordionKey }}">

                                        <div class="mb-3 row">
                                            <div class="mb-4 col-md-4">
                                                <label>Subject</label>
                                                <input type="text" class="form-control" value="{{ $mark->subject->name }}" readonly>
                                                <input type="hidden" name="subject_id" value="{{ $mark->subject->id }}">
                                            </div>

                                            <div class="mb-4 col-md-4">
                                                <label>Obtained Marks</label>
                                                <input type="number" 
                                                    name="obtained_marks" 
                                                    class="form-control @error("obtained_marks") is-invalid @enderror" 
                                                    value="{{ old("obtained_marks", $mark->obtained_marks) }}" 
                                                    required>
                                                
                                                @error("obtained_marks")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-4 col-md-4">
                                                <label>Remarks</label>
                                                <input type="text" class="form-control" value="{{ $mark->remark }}" readonly>
                                                <input type="hidden" name="remark" value="{{ $mark->remark }}">
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Update Marks</button>
                                        <a href="{{ route('marks.index', [
                                                        'selectedClass' => $request->selectedClass,
                                                        'selectedStream' => $request->selectedStream,
                                                        'selectedSubject' => $request->selectedSubject,
                                                        'search' => $request->search,
                                                        'selectedexaminationsType' => $request->selectedexaminationsType,
                                                        'editStatus' => 1,
                                                        'accordionKey' => $accordionKey
                                                    ]) }}" 
                                        class="btn btn-danger">Cancel</a>
                                    </form>
                                @else
                                    <form action="{{ route('marks.update.class') }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="editStatus" value="2">
                                        <input type="hidden" name="selectedexaminationsType" value="{{ $request->selectedexaminationsType }}">
                                        <input type="hidden" name="selectedClass" value="{{ $request->selectedClass }}">
                                        <input type="hidden" name="selectedSubject" value="{{ $request->selectedSubject }}">
                                        <input type="hidden" name="selectedStream" value="{{ $request->selectedStream }}">
                                        <input type="hidden" name="search" value="{{ $request->search }}">
                                        <input type="hidden" name="accordionKey" value="{{ $accordionKey }}">

                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        @foreach($marks as $mark)
                                            <div class="mb-3 row">
                                                <input type="hidden" name="marks[{{ $mark->id }}][id]" value="{{ $mark->id }}">

                                                <div class="mb-4 col-md-4">
                                                    <label>Subject</label>
                                                    <input type="text" class="form-control" value="{{ $mark->subject->name }}" readonly>
                                                    <input type="hidden" name="marks[{{ $mark->id }}][subject_id]" value="{{ $mark->subject->id }}">
                                                </div>

                                                <div class="mb-4 col-md-4">
                                                    <label>Obtained Marks</label>
                                                    <input type="number" 
                                                        name="marks[{{ $mark->id }}][obtained_marks]" 
                                                        class="form-control @error("marks.{$mark->id}.obtained_marks") is-invalid @enderror" 
                                                        value="{{ old("marks.{$mark->id}.obtained_marks", $mark->obtained_marks) }}" 
                                                        required>
                                                    
                                                    @error("marks.{$mark->id}.obtained_marks")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-4 col-md-4">
                                                    <label>Remarks</label>
                                                    <input type="text" class="form-control" value="{{ $mark->remark }}" readonly>
                                                    <input type="hidden" name="marks[{{ $mark->id }}][remark]" value="{{ $mark->remark }}">
                                                </div>
                                            </div>
                                        @endforeach

                                        <button type="submit" class="btn btn-primary">Update Marks</button>
                                        <a href="{{ route('marks.index', [
                                                        'selectedClass' => $request->selectedClass,
                                                        'selectedSubject' => $request->selectedSubject,
                                                        'selectedStream' => $request->selectedStream,
                                                        'search' => $request->search,
                                                        'selectedexaminationsType' => $request->selectedexaminationsType,
                                                        'editStatus' => 2,
                                                        'accordionKey' => $accordionKey
                                                    ]) }}" 
                                        class="btn btn-danger">Cancel</a>
                                    </form>
                                @endif
                            @else
                                <div class="alert alert-info">
                                    No marks found for this class.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
</x-layout>