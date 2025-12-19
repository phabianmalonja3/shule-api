<!-- resources/views/components/teacher/teacher_table.blade.php -->
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                
                <th>#</th>
                <th>Full Name</th>
                <th>Role</th>
                <th>Phone Number</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="teacher-list-body">
            @forelse ($teachers as $index => $teacher)
            <tr>
                
                <td>{{ $loop->iteration }}</td>
                <td>{{ $teacher->name }}</td>
                <td>{{ $teacher->roles->first()->name ?? 'No Role Assigned' }}</td>
                <td>{{ $teacher->phone ?? 'No Phone Number' }}</td>
                <td>
                    <span class="badge {{ $teacher->is_verified ? 'badge-success' : 'badge-danger' }}">
                        {{ $teacher->is_verified ? 'Active' : 'Deactivate' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('teachers.show', $teacher->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> View
                    </a>
                    @role('header teacher')
                    <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this teacher?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                    @endrole
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No teachers found.</td>
            </tr>
            @endforelse
            
          </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="px-2">
        {{-- {{ $teachers->links() }} --}}
    </div>
</div>
