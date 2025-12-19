<x-layout>
    <x-slot:title>
        Class Teacher Panel
    </x-slot:title>
    <x-navbar />
    <x-admin.sidebar />
    <div class="main-content">
        <section class="section">
            <div class="row">
                <!-- Students Card -->
                <div class="mb-4 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="card-content">
                                            <h5 class="font-15">Students in Class</h5>
                                            <h2 class="mb-3 font-18">{{ $studentCount }}</h2>
                                        </div>
                                    </div>
                                    <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <i class="fas fa-users fa-5x text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teachers Card (Class Teachers Only) -->
                <div class="mb-4 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="card-content">
                                            <h5 class="font-15">Teachers in Department</h5>
                                            <h2 class="mb-3 font-18">{{ $teachersCount }}</h2>
                                        </div>
                                    </div>
                                    <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <i class="fas fa-user-tie fa-5x text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Announcements Card (Optional, based on teacher's needs) -->
                <div class="mb-4 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="card-content">
                                            <h5 class="font-15">Announcements</h5>
                                            <h2 class="mb-3 font-18">{{ $annoucementCount }}</h2>
                                        </div>
                                    </div>
                                    <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <i class="fas fa-bullhorn fa-5x text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teachers List -->
                <div class="mb-4 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="card-content">
                                            <h5 class="font-15">Teachers List</h5>
                                            <h2 class="mb-3 font-18">{{ $teachersListCount }}</h2>
                                        </div>
                                    </div>
                                    <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <i class="fas fa-users fa-5x text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List of Teachers (Class Teachers) -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Teachers in Your Department</h4>
                            <div class="card-header-form d-flex justify-content-between align-items-center">
                                <!-- Search form -->
                                <form>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search by teacher name or email" name="search">
                                        <div class="input-group-btn">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="p-0 card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Full Name</th>
                                            <th>Role</th>
                                            <th>Phone Number</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($teachers as $teacher)
                                            <tr>
                                                <td>{{ $teacher->name }}</td>
                                                <td>{{ $teacher->roles->first()->name ?? 'No Role Assigned' }}</td>
                                                <td>{{ $teacher->phone ?? 'No Phone Number' }}</td>
                                                <td>
                                                    <span class="badge {{ $teacher->is_verified ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $teacher->is_verified ? 'Active' : 'Deactivate' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No teachers found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <!-- Pagination Links -->
                                <div class="px-2">
                                    {{ $teachers->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>
