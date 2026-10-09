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
                
                <!-- Workspace Header Bar -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <span class="text-uppercase text-muted font-weight-bold font-12" style="letter-spacing: 1px;">Application Workspace</span>
                        <h2 class="font-24 font-weight-bold text-dark mb-0">All applications</h2>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <button class="btn btn-outline-light text-dark bg-white border shadow-sm">
                            <i class="far fa-bell"></i>
                        </button>
                        <button class="btn btn-outline-light text-dark bg-white border shadow-sm">
                            <i class="fas fa-download mr-1"></i> Import application
                        </button>
                        <button class="btn btn-primary font-weight-bold shadow-sm">
                            <i class="fas fa-plus mr-1"></i> New application
                        </button>
                    </div>
                </div>

                <!-- Greeting & Timestamp -->
                <div class="mb-4">
                    <h3 class="font-28 font-weight-bold text-dark mb-1">
                        Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ Auth::user()->name ?? 'Maya' }}
                    </h3>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="text-muted mb-0">Here's what's happening across your applications.</p>
                        <span class="text-muted font-12">
                            <i class="fas fa-sync-alt mr-1"></i> Updated just now
                        </span>
                    </div>
                </div>

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
        </div>
    </div>
   {{-- <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> --}} 
</x-layout>