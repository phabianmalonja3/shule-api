<x-layout>
    <x-slot:title>
        {{ isset($announcement) ? 'Edit Announcement' : 'Add New Announcement' }}
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <livewire:announcements.announcement-create :announcement="$announcement" />
            </div>
        </section>
    </div>
</x-layout>


{{-- <x-layout>
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
                                    <!-- Button to add a new teacher -->
                                   
                                    <a href="{{ route('announcements.create') }}" class="btn btn-success">
                                        <i class="fas fa-plus"></i> Add New Announcement 
                                    </a>
                                <div class="d-flex align-items-center">
                                   
                                    <div class="ml-3 input-group">
                                        <input type="text" class="form-control" placeholder="Search announcements..." name="search" id="search">
                                    </div>
                                </div>
                                </div>
                            </div>

                           
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
</x- --}}