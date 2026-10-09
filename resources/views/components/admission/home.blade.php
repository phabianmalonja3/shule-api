
<x-layout >
    <x-slot:title>
        Application Dashboard
    </x-slot:title>
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
        <x-navbar />
        <x-admin.sidebar />
        <div class="main-content">
            <section class="section">
                <!-- Workspace Header Bar -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <span class="text-uppercase text-muted font-weight-bold font-12" style="letter-spacing: 1px;">Application Dashboard</span>

                    </div>

                    <!-- Right Side: Upload & Download Buttons -->
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <button class="btn btn-primary font-weight-bold shadow-sm">
                            <i class="fas fa-plus mr-1"></i> Upload applications
                        </button>
                        <button class="btn btn-outline-light text-dark bg-white border shadow-sm">
                            <i class="fas fa-download mr-1"></i> Download applications
                        </button>
                    </div>
                </div>

                <!-- Greeting & Timestamp -->
                <div class="mb-4">
                    <h3 class="font-28 font-weight-bold text-dark mb-1">
                        Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ Auth::user()->name ?? 'Maya' }}
                    </h3>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="text-muted mb-0">Here's an overview of all applications for your school.</p>
                        <span class="text-muted font-12">
                            <i class="fas fa-sync-alt mr-1"></i> Updated just now
                        </span>
                    </div>
                </div>

                <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <a href="{{ route('classes.index') }}" style="text-decoration: none; color: black;">
                                <div class="row">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="card-content">
                                            <h5 class="font-15">Applications</h5>
                                            <h2 class="mb-3 font-18">{{ $applicationCount }}</h2>
                                        </div>
                                    </div>
                                    <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="banner-img " height='20px'>
                                            <img src= "{{ asset('assets/img/banner/classroom.png') }}" alt="student image"/>
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
                                <a href="{{ route('teachers.index') }}" style="text-decoration: none; color: black;">
                                <div class="row">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="card-content">
                                            <h5 class="font-15">In progress</h5>
                                            <h2 class="mb-3 font-18">{{ $inprogressCount }}</h2>
                                        </div>
                                    </div>
                                    <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="banner-img py-2">
                                            <img src= "{{ asset('assets/img/banner/teacher-banner.png') }}" alt="student image"/>
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
                                <a href="{{ route('students.index') }}" style="text-decoration: none; color: black;">
                                    <div class="row">
                                        <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                            <div class="card-content">
                                                <h5 class="font-15">Completed</h5>
                                                <h2 class="mb-3 font-18">{{ $completedCount }}</h2>
                                            </div>
                                        </div>
                                        <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                            <div class="banner-img py-2">
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
                                    <div class="row">
                                        <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                            <div class="card-content">
                                                <h5 class="font-15">Submitted</h5>
                                                <h2 class="mb-3 font-18">{{ $submittedCount }}</h2>
                                            </div>
                                        </div>
                                        <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                            <div class="banner-img py-2" >
                                                <img src= "{{ asset('assets/img/banner/announcement.png') }}" alt="student image" height="80px"/>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <livewire:teacher.teacher-list />

            </section>
        </div>
    </div>
  {{-- <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> --}}
</x-layout>
 
  <!-- General JS Scripts -->
  