
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
                                        <h5 class="font-15">Classes</h5>
                                        <h2 class="mb-3 font-18">{{ $classCount }}</h2>
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
                                        <h5 class="font-15">Teachers</h5>
                                        <h2 class="mb-3 font-18">{{ $teachersCount }}</h2>
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
                          @if(\App\Models\Student::where('school_id', auth()->user()->school_id)->where('is_active',1)->count() > 1)
                            <a href="{{ route('students.index') }}" style="text-decoration: none; color: black;">
                              <div class="row">
                                  <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                      <div class="card-content">
                                          <h5 class="font-15">Students</h5>
                                          <h2 class="mb-3 font-18">{{ $studentsCount }}</h2>
                                      </div>
                                  </div>
                                  <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6"> {{-- Added text-right --}}
                                      <div class="banner-img py-2">
                                          <img src= "{{ asset('assets/img/banner/student-Dykl0cqs.png') }}" alt="student image"/>
                                      </div>
                                  </div>
                              </div>
                            </a>
                          @else
                            <div class="row">
                                <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="card-content">
                                        <h5 class="font-15">Students</h5>
                                        <h2 class="mb-3 font-18">{{ $studentsCount }}</h2>
                                    </div>
                                </div>
                                <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6"> {{-- Added text-right --}}
                                    <div class="banner-img py-2">
                                        <img src= "{{ asset('assets/img/banner/student-Dykl0cqs.png') }}" alt="student image"/>
                                    </div>
                                </div>
                            </div>
                          @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="card">
                    <div class="card-statistic-4">
                        <div class="align-items-center justify-content-between">
                          @if (auth()->user()->hasAnyRole(['header teacher', 'assistant headteacher']) && \App\Models\User::where('school_id', auth()->user()->school_id)->count() == 1)
                              <div class="row">
                                  <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                      <div class="card-content">
                                          <h5 class="font-15">Announcements</h5>
                                          <h2 class="mb-3 font-18">{{ $announcementCount }}</h2>
                                      </div>
                                  </div>
                                  <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6"> {{-- Added text-right --}}
                                      <div class="banner-img py-2" >
                                          <img src= "{{ asset('assets/img/banner/announcement.png') }}" alt="student image" height="80px"/>
                                          
                                      </div>
                                  </div>
                              </div>
                          @else
                            <a href="{{ route('announcements.index') }}" style="text-decoration: none; color: black;">
                              <div class="row">
                                  <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                      <div class="card-content">
                                          <h5 class="font-15">Announcements</h5>
                                          <h2 class="mb-3 font-18">{{ $announcementCount }}</h2>
                                      </div>
                                  </div>
                                  <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6"> {{-- Added text-right --}}
                                      <div class="banner-img py-2" >
                                          <img src= "{{ asset('assets/img/banner/announcement.png') }}" alt="student image" height="80px"/>
                                          
                                      </div>
                                  </div>
                              </div>
                            </a>
                          @endif
                        </div>
                    </div>
                </div>
            </div>
          </div>

          <livewire:teacher.teacher-list />

        </section>
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="formModal"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formModal">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="">
          <div class="form-group">
            <label>Username</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fas fa-envelope"></i>
                </div>
              </div>
              <input type="text" class="form-control" placeholder="Email" name="email">
            </div>
          </div>
          <div class="form-group">
            <label>Password</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fas fa-lock"></i>
                </div>
              </div>
              <input type="password" class="form-control" placeholder="Password" name="password">
            </div>
          </div>
          <div class="form-group mb-0">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" name="remember" class="custom-control-input" id="remember-me">
              <label class="custom-control-label" for="remember-me">Remember Me</label>
            </div>
          </div>
          <button type="button" class="btn btn-primary m-t-15 waves-effect">LOGIN</button>
        </form>
      </div>
    </div>
  </div>
</div>
    </div>
  </div>
  </div>
  {{-- <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> --}}

</x-layout>
 
  <!-- General JS Scripts -->
  