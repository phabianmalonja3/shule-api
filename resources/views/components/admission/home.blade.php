<x-layout>
    <x-slot:title>
        Admission Officer's Panel
    </x-slot:title>

    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-navbar />
        <x-admin.sidebar />


        <div class="main-content">
            <section class="section">
                

                <!-- Modern Metric Cards Row -->
                <div class="row">
                    <!-- Total Applications -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
                        <div class="card h-100 shadow-sm border-0 rounded-lg">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-uppercase font-weight-bold text-muted font-12" style="letter-spacing: 0.5px;">Total applications</span>
                                    <div class="p-2 bg-light-indigo rounded text-primary">
                                        <i class="far fa-copy font-16"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <h2 class="font-30 font-weight-bold text-dark mb-0 mr-2">{{ $applicationCount }}</h2>
                                    <span class="text-muted font-12">Across active centers</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- In Progress -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
                        <div class="card h-100 shadow-sm border-0 rounded-lg">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-uppercase font-weight-bold text-muted font-12" style="letter-spacing: 0.5px;">In progress</span>
                                    <div class="p-2 bg-light-purple rounded text-purple">
                                        <i class="fas fa-pencil-alt font-16"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <h2 class="font-30 font-weight-bold text-dark mb-0 mr-2">{{ $inprogressCount }}</h2>
                                    <span class="text-muted font-12">Active processing</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submitted / Completed -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
                        <div class="card h-100 shadow-sm border-0 rounded-lg">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-uppercase font-weight-bold text-muted font-12" style="letter-spacing: 0.5px;">Submitted</span>
                                    <div class="p-2 bg-light-success rounded text-success">
                                        <i class="far fa-check-circle font-16"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <h2 class="font-30 font-weight-bold text-dark mb-0 mr-2">{{ $submittedCount }}</h2>
                                    <span class="text-muted font-12">Ready for review</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completed -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
                        <div class="card h-100 shadow-sm border-0 rounded-lg">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-uppercase font-weight-bold text-muted font-12" style="letter-spacing: 0.5px;">Completed</span>
                                    <div class="p-2 bg-light-warning rounded text-warning">
                                        <i class="fas fa-exclamation-triangle font-16"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <h2 class="font-30 font-weight-bold text-dark mb-0 mr-2">{{ $completedCount }}</h2>
                                    <span class="text-muted font-12">Finalized</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Applications Table / Component -->
                <div class="mt-4">
                    <livewire:admission.application-list />
                </div>

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