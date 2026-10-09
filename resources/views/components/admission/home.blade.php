
<x-layout >
    <x-slot:title>
        Teacher's Panel
    </x-slot:title>
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
        <x-navbar />
        <x-admin.sidebar />
        <div class="main-content">
          <section class="section">
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
                                  <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6"> {{-- Added text-right --}}
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
                                  <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6"> {{-- Added text-right --}}
                                      
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
                                    <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6"> {{-- Added text-right --}}
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
                                    <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6"> {{-- Added text-right --}}
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
  