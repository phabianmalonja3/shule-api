<x-layout>
    <x-slot:title>
        Teacher Panel
    </x-slot:title>
    <x-navbar />
    <x-admin.sidebar /> <!-- Adjust to include a teacher-specific sidebar -->
    <div class="main-content">
        <section class="section">
            <div class="row">
                <!-- Your Classes Card -->
                <div class="mb-4 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="card-content">
                                            <h5 class="font-15">Students</h5>
                                            {{-- <h2 class="mb-3 font-18">{{$teacherClassesCount}}</h2> --}}
                                        </div>
                                    </div>
                                    <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <i class="fas fa-chalkboard-teacher fa-5x text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Announcements for You -->
                <div class="mb-4 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="card-content">
                                            <h5 class="font-15">groups</h5>
                                            {{-- <h2 class="mb-3 font-18">{{$announcementsCount}}</h2> --}}
                                        </div>
                                    </div>
                                    <div class="pl-0 text-right col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <i class="fas fa-bullhorn fa-5x text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Your Students -->
                <div class="mb-4 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="card-content">
                                            <h5 class="font-15">Your Students</h5>
                                            {{-- <h2 class="mb-3 font-18">{{$studentsCount}}</h2> --}}
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

            <!-- Class Overview -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Your Classes</h4>
                            <a href="{{ route('classes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Class
                            </a>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Class Name</th>
                                            <th>Students</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    {{-- <tbody>
                                        @forelse ($teacherClasses as $class)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $class->name }}</td>
                                                <td>{{ $class->students_count }}</td>
                                                <td>{{ $class->created_at->format('d-m-Y') }}</td>
                                                <td>
                                                    <a href="{{ route('classes.show', $class->id) }}" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No classes found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody> --}}
                                </table>
                                {{-- {{ $teacherClasses->links() }} --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>
