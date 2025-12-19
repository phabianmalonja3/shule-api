@forelse ($announcements->where('user_id','!=',auth()->id()) as $index => $announcement)
    <tr>
        <td class="text-center">{{ $loop->iteration }}</td> <td class="p-0 text-center">
           
        </td>
        <td>{{ $announcement->title }}</td>
        <td>{{ $announcement->type ?? '' }}</td>
        <td>{{ $announcement->user->name ?? '' }}</td>
        {{-- <td>{{ $announcement->formatDates()['start_date'] }}</td> --}}
<td>{{ $announcement->formatDates()['end_date'] }}</td>
{{-- <td> --}}
    
    {{-- <label class="custom-switch">

        
        <input type="checkbox" class="custom-switch-input toggle-status" 
               data-id="{{ $announcement->id }}" 

               {{ $announcement->status == 1 ? 'checked' : '' }}>
        <span class="custom-switch-indicator"></span>
        <span class="custom-switch-description">
            {{ $announcement->status == 1 ? 'Published' : 'Unpublished' }}
        </span>
    </label> --}}
{{-- </td> --}}
        
        <td>
            <a href="{{ route('announcements.show', ['announcement' => $announcement->id]) }}" class="btn btn-primary"><i class="fas fa-eye"></i> View</a>
           
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
@empty
    <tr>
        <td colspan="7" class="text-center">No announcements found.</td> </tr>
@endforelse
<script src="{{asset('js/jquery.min.js') }}"></script>

<script>
    $(document).on('change', '.toggle-status', function () {
        let checkbox = $(this);
        let announcementId = $(this).data('id');
        let isPublished = $(this).is(':checked');
        let status = isPublished ? 1 : 0;
        let descriptionSpan = checkbox.siblings('.custom-switch-description');
        descriptionSpan.text(isPublished ? 'Published' : 'Unpublished');


        // console.log(status)

        $.ajax({
            url: "{{ route('announcements.toggleStatus') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                announcementId: announcementId,
                status: status
            },
            success: function (response) {
                if (response.success) {

                    location.reload();
                   
                }
            },
            error: function (error) {
                alert('Failed to update status. Please try again.' +error);
                console.log('Failed to update status. Please try again.' +error);
            }
        });
    });
</script>
