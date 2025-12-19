<x-layout>
    <x-slot:title>
        Announcement Details
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12 ">
                        <div class="px-4 card">
                            <div class="card-header">
                                <h2 class="mb-4">{{ $announcement->title }}</h2>
                            </div>

                            <div class="card-body">
                                <p><strong>Content:</strong></p>
                                <p>{{ $announcement->content }}</p>

                                @if($announcement->image)
                                <p><strong>Image:</strong></p>
                                <img src="{{ asset('storage/' . $announcement->image) }}" alt="Announcement Image"
                                    class="mb-4 img-fluid" width="500">
                                @endif

                                <p><strong>Status:</strong>
                                    @if($announcement->is_active)
                                    <span class="text-success">Published</span>
                                    @else
                                    <span class="text-danger">Not Published</span>
                                    @endif
                                </p>
                            </div>

                            <div class="card-footer">
                                <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Back to
                                    Announcements</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>