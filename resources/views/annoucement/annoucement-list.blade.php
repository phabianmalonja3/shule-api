<x-layout>
    <x-slot:title>
        Announcement List Page
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ \Str::title('List of Announcements') }}</h4>
                                <div class="card-header-form d-flex justify-content-between align-items-center">
                                    @if (!auth()->user()->hasRole('teacher'))
                                        <a href="{{ route('announcements.create') }}" class="btn btn-success">
                                            <i class="fas fa-plus"></i> Add Announcement 
                                        </a>
                                    @endif
                                    <div class="d-flex align-items-center">
                                        <div class="ml-3 input-group">
                                            <input type="text" class="form-control" placeholder="Search announcements..." name="search" id="search">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (auth()->user()->hasAnyRole(['header teacher',' assistant headteacher','academic teacher','class teacher']))
                                <div class="card-body">
                                <!-- Tabs for different views -->
                                    <ul class="nav nav-tabs" id="announcementTabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="all-announcements-tab" data-toggle="tab" href="#all-announcements" role="tab" aria-controls="all-announcements" aria-selected="true">Received Announcements</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="my-announcements-tab" data-toggle="tab" href="#my-announcements" role="tab" aria-controls="my-announcements" aria-selected="false">Created Announcements</a>
                                        </li>
                                    
                                    </ul>
                                    <div class="tab-content" id="announcementTabsContent">
                                        <!-- Tab 1: All Announcements -->
                                        <div class="tab-pane fade show active" id="all-announcements" role="tabpanel" aria-labelledby="all-announcements-tab">
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="announcement-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center"></th>
                                                            <th>Title</th>
                                                            <th>Announcement Type</th>
                                                            <th>Created By</th>
                                                            <th>End Date</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {{-- {{ auth()->id() }} {{ $announcements->where('user_id','!=',auth()->id()) }} --}}
                                                                        @include('annoucement.partials.announcement_rows', ['announcements' => $announcements])
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Tab 2: My Announcements -->
                                        <div class="tab-pane fade" id="my-announcements" role="tabpanel" aria-labelledby="my-announcements-tab">
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="my-announcement-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center"></th>
                                                            <th>Title</th>
                                                            <th>Announcement Type</th>
                                                            <th>Created By</th>
                                                            <th>End Date</th>
                                                        
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($announcements->where('user_id', auth()->id()) as $announcement)
                                                        <tr>
                                                            <td class="text-center">{{ $loop->iteration }}</td>
                                                            <td class="p-0 text-center">
                                                                
                                                            </td>
                                                            <td>{{ $announcement->title }}</td>
                                                            <td>{{ $announcement->type }}</td>
                                                            <td>{{ $announcement->user->name }}</td>
                                                            <td>{{ $announcement->formatDates()['end_date'] }}</td>
                                                        
                                                            <td>
                                                                <a href="{{ route('announcements.show', $announcement->id) }}" class="btn btn-primary"><i class="fas fa-info-circle me-2"></i> View</a>
                                                                @can('update', $announcement)
                                                                <a href="{{ route('announcements.edit', $announcement->id) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
                            
                                                            @endcan
                                                            @can('delete', $announcement)
                                                                <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                                                                </form>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Tab 3: Announcements by Status -->
                                        <div class="tab-pane fade" id="announcement-status" role="tabpanel" aria-labelledby="announcement-status-tab">
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="status-announcement-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center"></th>
                                                            <th>Title</th>
                                                            <th>Announcement Type</th>
                                                            <th>Created By</th>
                                                            <th>End Date</th>
                                                        
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                              @include('annoucement.partials.announcement_rows', ['announcements' => $announcements])
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                           @else
                                <div class="p-0 card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="announcement-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">#</th>
                                                    <th class="text-center"></th>
                                                    <th>Title</th>
                                                    <th>Announcement Type</th>
                                                    <th>Created By</th>
                                                    <th>End Date</th>
                                                    {{-- <th style="margin-left: 20%;">Status</th> --}}
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @include('annoucement.partials.announcement_rows', ['announcements' => $announcements])
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="pagination-container" class="px-2">
                                        {{ $announcements->links() }}
                                    </div>
                                </div>
                        
                            @endif
                        </div>
                    </div>
                </div> 
            </div>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            function fetchAnnouncements(page = null) {
                let search = $('#search').val();
                let url = "{{ route('announcements.index') }}?ajax=1&search=" + search;

                
                if (page) {
                    url += "&page=" + page;
                }

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        

                        
                        $('#announcement-table tbody').html(data.table);
                        $('#pagination-container').html(data.pagination);
                    },
                    error: function (error) {
                        console.log(error)

                    
                    }
                });
            }

            $('#search').on('keyup', function () {
                 fetchAnnouncements();
            });

            $(document).on('click', '.pagination a', function (event) {
                event.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                fetchAnnouncements(page);
            });

            fetchAnnouncements(); // Initial load
        });
    </script>
</x-layout>