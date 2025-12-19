<style>
/* 1. Hides the logo/brand area */
.sidebar-mini .sidebar-brand {
    display: none !important; 
}

/* 2. Hides the subsequent school/headteacher info container for teacher roles */
/* This targets the first container immediately following the sidebar brand */
.sidebar-mini #sidebar-wrapper > div.container.mb-2:nth-of-type(1) {
    display: none !important;
}

/* 3. Hides the second container that appears below the sidebar-brand closing tag */
.sidebar-mini #sidebar-wrapper > div.container.mb-2:nth-of-type(2) {
    display: none !important;
}

/* 3. Hides the second container that appears below the sidebar-brand closing tag */
.sidebar-mini #sidebar-wrapper > ul.sidebar-menu {
    padding-top: 50px; 
}
.main-content { 
    /* Adjust this value to be slightly more than the height of your top navbar */
    padding-top: 100px; 
    /* Or whatever height your top navbar is */
}
</style>
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand" style="height: 20%; text-align: center;">
            @if (auth()->user()->hasAnyRole(['header teacher', 'academic teacher', 'class teacher', 'teacher']))
                <a @if(auth()->user()->hasRole('teacher')) href= "{{ route('teacher.panel') }}" @else href= "{{ route('welcome.head.teacher') }}" @endif>
                    <div class="banner-img py-2">
                        <img  
                            src="{{ auth()->user()->school->logo ? asset('storage/' . auth()->user()->school->logo) : asset('logo/logo_thumbnail.png') }}" 
                            class="mt-4 rounded-circle" 
                            style="width: 100px; height: 100px; object-fit: cover;" /> 
                    </div>
                </a>
            @endif

            @php

                if(auth()->user()->hasAnyRole(['administrator','parent','student'])){
                    $subject_assignments = 0;
                }else{
                    $subject_assignments = \App\Models\StreamSubjectTeacher::whereHas('stream.schoolClass', function($q){$q->where('school_id',auth()->user()->school->id);})
                                    ->count();
                    $examStatus = \App\Models\Student::where('school_id',auth()->user()->school_id)
                                                        ->whereHas('marks')->count();
                }

            @endphp
            @role('administrator')
                <a href="{{ route('admin.home') }}">
                    <div class="logo-wrapper">
                        <img alt="Admin Logo" src="{{ asset('logo/logo-thumbnail.png') }}" class="mt-4 rounded-circle"
                             style="width: 80px; height: 80px; object-fit: cover;" />
                    </div>
                </a>
            @endrole

            @if (!auth()->user()->hasAnyRole(['administrator','parent','student']))
                @php
                    $headTeacher = \App\Models\User::role('header teacher')
                                    ->where('school_id', auth()->user()->school->id)
                                    ->first();

                    Str::contains(strtolower(auth()->user()->school->name),'secondary')? $role = "Head of School" : $role = "Headteacher";
                @endphp
            @endif
            @role('student')

                <div class="container mb-2">

                    @php

                        $classSetupStatus = \App\Models\SchoolClass::where('school_id', auth()->user()->school_id)
                                                       ->whereNotNull('teacher_class_id')
                                                       ->orHas('streams')
                                                       ->first();
                    @endphp

                    <div class="font-size: 0.86em">{{ $headTeacher->name }}   
                        <span class="text-muted" style="font-size: 0.86em;">
                            ({{ $role }}) <br> {{ $headTeacher->phone }}
                        </span>
                    </div>
                </div>
            @endrole
           
            @role('parent')
                @php
                    $user = auth()->user(); 
                    $students = $user->parent->students; 
                    $school= $students->first()->school;

                    $headTeacher = \App\Models\User::role('header teacher')
                    ->where('school_id', $school->id)
                    ->first();

                    Str::contains(strtolower($school->name),'secondary')? $role = "Head of School" : $role = "Headteacher";
                @endphp

                <a href="{{ route('parents.index') }}">
                    <div class="logo-wrapper">
                        <img alt="School Logo" src="{{ asset('storage/' .$school->logo ?? 'logo/logo2.png') }}" class="mt-4 shadow-sm rounded-circle"
                             style="width: 80px; height: 80px; object-fit: cover;" />
                    </div>
                </a>

                <div class="container mb-2">
                    <div style="height: 5%;">
                        <strong>{{ Str::contains(strtolower($school->name ?? ''), 'school') 
                               ? Str::of($school->name)->replace('school', '')->trim() 
                               : $school->name }}</strong>
                    </div>
                    <div class="font-size: 0.86em">{{ $headTeacher->name }}   
                        <span class="text-muted" style="font-size: 0.86em;">
                            ({{ $role }}) <br> {{ $headTeacher->phone }}
                        </span>
                    </div>
                </div>
            @endrole
        </div>

        @if (auth()->user()->hasAnyRole(['header teacher', 'academic teacher', 'class teacher', 'teacher']))
            <div class="container mb-2">
                <div style="height: 5%;">
                    <strong>
                        {{ Str::contains(strtolower(auth()->user()->school->name ?? ''), 'school') 
                           ? Str::of(auth()->user()->school->name)->replace('school', '')->trim() 
                           : auth()->user()->school->name }}
                    </strong>
                </div>
                <div class="font-size: 0.86em">{{ $headTeacher->name }}   
                    <span class="text-muted" style="font-size: 0.86em;">
                        ({{ $role }}) <br> {{ $headTeacher->phone }}
                    </span>
                </div>
            </div>
        @endif

        <ul class="sidebar-menu">
            <li class="menu-header"></li>

            @role('administrator')
                <li class="dropdown">
                    <a href="#" class="menu-toggle nav-link has-dropdown">
                        <i class="fa fa-graduation-cap"></i> <span>Schools</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="{{ route('school.list') }}" >School List</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="menu-toggle nav-link has-dropdown">
                        <i class="fa fa-school"></i> <span>Application</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="{{ route('application.list') }}" >Application List</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="menu-toggle nav-link has-dropdown">
                     
                        <i class="fas fa-credit-card"></i> <span class="icon-name">Payment</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="{{ route('payments.index') }}">Payment List</a></li>
                    </ul>
                </li>
            @endrole

            @if (auth()->user()->hasAnyRole(['header teacher', 'academic teacher', 'class teacher', 'teacher']))

                <li class="dropdown">
                    <a href="#" class="menu-toggle nav-link has-dropdown">
                        <i class="fas fa-chalkboard"></i> <span>Classes</span>
                    </a>
                    
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="{{ route('classes.index') }}">List of Classes</a></li>
                    </ul>
                </li>
                
                <li class="dropdown">
                    <a href="#" class="menu-toggle nav-link has-dropdown">
                        <i class="fas fa-book-open"></i> <span>Combinations</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="{{ route('subjects.index') }}">List of Combinations </a></li>
                        <li><a class="nav-link" href="{{ route('subjects.index') }}">List of Subjects </a></li>
                        @if (count(auth()->user()->streamSubjects) !== 0 )
                        <li><a class="nav-link" href="{{ route('teacher.panel') }}">Assigned Subjects</a></li>

                        @endif
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="menu-toggle nav-link has-dropdown">
                        <i class="fas fa-chalkboard-teacher"></i> <span>Teachers</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="{{ route('teachers.index') }}" >List of Teachers </a></li>
                    </ul>
                </li>

                @php
                    $classSetupStatus = \App\Models\SchoolClass::where('school_id', auth()->user()->school_id)->first();
                @endphp

                @if(count($classSetupStatus->streams) > 0 || $classSetupStatus->teacher_class_id != '')
                    @if (auth()->user()->hasAnyRole(['header teacher', 'academic teacher', 'class teacher', 'teacher']))
                        @if (auth()->user()->hasRole('teacher'))
                            @if(count(auth()->user()->streamSubjects) !== 0)
                                <li class="dropdown">
                                    <a href="#" class="menu-toggle nav-link has-dropdown">
                                        <i class="fas fa-user-graduate"></i> <span>Students</span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="nav-link" href="{{ route('students.index') }}" > List of Students</a></li>
                                    </ul>
                                </li>
                            @endif
                        @else
                            <li class="dropdown">
                                <a href="#" class="menu-toggle nav-link has-dropdown">
                                    <i class="fas fa-user-graduate"></i> <span>Students</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="nav-link" href="{{ route('students.index') }}" > List of Students</a></li>
                                </ul>
                            </li>
                        @endif

                    @endif
                @endif   
                @php
                    $user = Auth::user();
                    $isStreamTeacher = \App\Models\Stream::where('teacher_id', $user->id)->orWhere('stream_teacher_id',$user->id)->exists();
                @endphp

                @role('class teacher')
                    <li class="dropdown">
                        <a href="#" class="menu-toggle nav-link has-dropdown">
                            <i class="fas fa-calendar-check"></i> <span>Attendance</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="{{ route('attendance.daily.index') }}">Create Today's Attendance</a></li>
                            <li><a class="nav-link" href="{{ route('attendance.weekly.index') }}">Create Weekly Attendance</a></li>
                            <li><a class="nav-link" href="{{ route('attendance.monthly.index') }}">Create Monthly Attendance</a></li>
                        </ul>
                    </li>
                @endrole

                
                @if (count(auth()->user()->streamSubjects) !== 0 )
                    <li class="dropdown">
                    
                        <a href="#" class="menu-toggle nav-link has-dropdown">
                            <i class="fas fa-tasks"></i> <span>Assignments</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="{{ route('assignments.index') }}" >List of Assignment</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="menu-toggle nav-link has-dropdown">
                            <i class="fas fa-file-alt"></i> <span>Homework</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="{{ route('homeworks.index') }}" > List of Homeworks</a></li>
                        </ul>
                    </li>
                @endif
                   
                {{-- </li> --}}
                @if(count($classSetupStatus->streams) > 0 || $classSetupStatus->teacher_class_id != '')
                    <li class="dropdown">
                        <a href="#" class="menu-toggle nav-link has-dropdown">
                            <i class="fas fa-bullhorn"></i> <span>Announcements</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="{{ route('announcements.index') }}" >List of Announcements</a></li>
                        </ul>
                    </li>
                @endif

                @if(count($classSetupStatus->streams) > 0 || $classSetupStatus->teacher_class_id != '')
                    @role('academic teacher')
                        <li class="dropdown">
                            <a href="#" class="menu-toggle nav-link has-dropdown">
                                <i class="fas fa-book-open"></i> <span>Academic Settings</span> 
                            </a>
                    
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="{{ route('grades.index') }}" >Set Grades</a></li>
                                
                            </ul>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="{{ route('school-calander') }}" > 
                                    Create School Calendar</a></li>
                                
                            </ul>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="{{ route("marks.upload.setup") }}" > 
                                    Marks Upload Setup</a></li>
                                
                            </ul>
                        </li>
                    @endrole
                @endif
            @endif
           
            @if(auth()->user()->school && !auth()->user()->hasRole('teacher'))
                @if($examStatus > 0 || $subject_assignments > 0)
                    <li class="dropdown">
                        <a href="{{ route('student.panel') }}" class="menu-toggle nav-link has-dropdown">
                            <i class="fas fa-chalkboard-teacher"></i> <span>Class Results</span>
                        </a>
                        <ul class="dropdown-menu">
                            @if($examStatus > 0)
                                <li><a class="nav-link" href="{{ route('examinations.index') }}" >View Summary Results</a></li> 
                                <li><a class="nav-link" href="{{ route('marks.index') }}" >View Detailed Results</a></li>
                            @endif
                            @if (auth()->user()->hasRole('academic teacher') && $subject_assignments > 0)
                                <li><a class="nav-link" href="{{ route('marks.class.upload.form') }}" >Upload Results</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
            @endif

        </ul>
    </aside>
</div>


