<div>
    <!-- Search and Filter -->
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div>
            <input type="text" class="form-control" placeholder="Search announcements..." wire:model.debounce.500ms="search">
        </div>
        <div>
            <select class="form-control" wire:model="statusFilter">
                <option value="">Filter by Status</option>
                <option value="1">Published</option>
                <option value="0">Unpublished</option>
            </select>
        </div>
    </div>

    <!-- Tabs for different views -->
    <ul class="nav nav-tabs" id="announcementTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link @if(!$userAnnouncements) active @endif" href="#" wire:click.prevent="toggleUserAnnouncements()">All Announcements</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($userAnnouncements) active @endif" href="#" wire:click.prevent="toggleUserAnnouncements()">My Announcements</a>
        </li>
    </ul>

    <!-- Announcements Table -->
    <div class="mt-3 table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th>Title</th>
                    <th>Announcement Type</th>
                    <th>Created By</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($announcements as $announcement)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $announcement->title }}</td>
                        <td>{{ $announcement->type }}</td>
                        <td>{{ $announcement->user->name }}</td>
                        {{-- <td>{{ $announcement->end_date->format('d-m-Y') }}</td> --}}
                        <td>
                            <span class="badge {{ $announcement->status == 1 ? 'badge-success' : 'badge-danger' }}">
                                {{ $announcement->status == 1 ? 'Published' : 'Unpublished' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('announcements.show', $announcement->id) }}" class="btn btn-primary"><i class="fas fa-eye"></i> View</a>
                            <a href="{{ route('announcements.edit', $announcement->id) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
                            <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $announcements->links() }}
    </div>
</div>
