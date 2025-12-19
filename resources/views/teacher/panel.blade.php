<x-layout>
    <x-slot:title>
        Assigned Subjects 
    </x-slot:title>
    <x-navbar />
    <x-admin.sidebar />
    <div class="main-content">
        <section class="section">
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="card-content">
                                            <h5 class="font-15">Subjects You Teach</h5>
                                            <h2 class="mb-3 font-18">{{ $subjects->count() }}</h2>
                                        </div>
                                    </div>
                                    <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6"> {{-- Added text-right --}}
                                      
                                            <div class="banner-img " height='20px'>
                                                <img src= "{{ asset('assets/img/banner/classroom.png') }}" alt="student image"/>
                                            </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="card-content">
                                            <h5 class="font-15">Classes You Teach</h5>
                                            <h2 class="mb-3 font-18"> {{ $data['stream_count'] }} </h2>
                                        </div>
                                    </div>
                                    <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6"> {{-- Added text-right --}}
                                       
                                            <div class="py-2 banner-img">
                                                <img src= "{{ asset('assets/img/banner/teacher-banner.png') }}" alt="student image"/>
                                            </div>
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <a href="{{ route('students.index') }}" style="text-decoration: none; color: black;">
                                    <div class="row">
                                        <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                            <div class="card-content">
                                                <h5 class="font-15">Students You Teach</h5>
                                                <h2 class="mb-3 font-18"> {{ $data['students_count'] }} </h2>
                                            </div>
                                        </div>
                                        <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6"> {{-- Added text-right --}}
                                            <div class="py-2 banner-img">
                                                <img src= "{{ asset('assets/img/banner/student-Dykl0cqs.png') }}" alt="student image"/>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <a href="{{ route('announcements.index') }}" style="text-decoration: none; color: black;">
                                    <div class="row">
                                        <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">

                                                <div class="card-content">
                                                    <h5 class="font-15">Announcement(s)</h5>
                                                    <h2 class="mb-3 font-18"> @if(!empty($announcements)) {{ count($announcements) }} @else 0 @endif</h2>
                                                </div>
                                            
                                        </div>
                                        <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6"> {{-- Added text-right --}}
                                            <div class="py-2 banner-img" >
                                                <img src= "{{ asset('assets/img/banner/announcement.png') }}" alt="student image" height="80px"/>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

@if(count($subjects) > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4> {{ \Str::title('Assigned Subjects') }}</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Subject Name</th>
                                <th>Classes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subjects as $subject)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a href="{{ route('subjects.show',['subject'=>$subject->id]) }}">{{ $subject->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        @php
                                            $streamsByClass = $subject->streams->groupBy(function($stream) {
                                                return \Str::replaceFirst('Form ', '', \Str::replaceFirst('Class ', '', $stream->schoolClass->name));
                                            });
                                        @endphp
                                        @foreach ($streamsByClass as $className => $classStreams)
                                            @php $class_id = $classStreams->pluck('schoolClass.id')->unique()->first(); @endphp

                                            <a href="{{ route('classes.show', ['class' => $class_id]) }}">
                                                Form {{ $className }}
                                                @if(count($classStreams) > 1)
                                                    (@foreach ($classStreams as $index => $stream)
                                                        {{ $stream->alias }}@if($index == count($classStreams) - 2) & @elseif(!$loop->last), @endif
                                                    @endforeach)
                                                @else
                                                    {{ $classStreams->first()->alias }}
                                                @endif
                                            </a>@if(!$loop->last); @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{ route('subjects.resources.index',['subject'=>$subject->id])}}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-info-circle me-2"></i> Manage Teaching Materials
                                        </a>

                                        @if(auth()->user()->school->is_teacher_upload == "1")
                                            <a href="{{ route('marks.upload.form',['selectedSubject'=>$subject->id, 'subjectStatus'=>1]) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-info-circle me-2"></i> Upload Results
                                            </a>
                                        @endif
                                        {{-- The modified section for the "View Results" button --}}
                                        @php
                                            $hasMarks = $subject->marks()->exists();
                                            $disabledClass = $hasMarks ? 'btn-primary' : 'btn-secondary disabled';
                                            $href = $hasMarks ? route('marks.index',['selectedSubject'=>$subject->id, 'selectedClass'=>$class_id, 'subjectStatus'=>1]) : '#';
                                        @endphp
                                        <a href="{{ $href }}" class="btn btn-sm {{ $disabledClass }}">
                                            <i class="fas fa-info-circle me-2"></i> View Results
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
        </section>
    </div>
</x-layout>
