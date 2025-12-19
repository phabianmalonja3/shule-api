<x-layout>
    <x-slot:title>
        Academic Years
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
                                <h4>Academic Years</h4>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#addAcademicYearModal">
                                    Add Academic Year
                                </button>
                            </div>

                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Start Year</th>
                                                <th>End Year</th>
                                                <th>Midterm Start Date</th>
                                                <th>Midterm End Date</th>
                                                <th>Annual Start Date</th>
                                                <th>Annual End Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($academicYears as $academicYear)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $academicYear->start_date }}</td>
        <td>{{ $academicYear->end_date }}</td>
        <td>{{ $academicYear->midterm_start_date }}</td>
        <td>{{ $academicYear->midterm_end_date }}</td>
        <td>{{ $academicYear->annual_start_date }}</td>
        <td>{{ $academicYear->annual_end_date }}</td>
        <td>
            <button class="btn btn-warning btn-sm edit-academic-year-button" 
                    data-id="{{ $academicYear->id }}"
                    data-start="{{ $academicYear->start_date }}"
                    data-end="{{ $academicYear->end_date }}"
                    data-midterm-start="{{ $academicYear->midterm_start_date }}"
                    data-midterm-end="{{ $academicYear->midterm_end_date }}"
                    data-annual-start="{{ $academicYear->annual_start_date }}"
                    data-annual-end="{{ $academicYear->annual_end_date }}"
                    data-description="{{ $academicYear->description }}"
                    data-toggle="modal" 
                    data-target="#editAcademicYearModal">
                Edit
            </button>
            <form action="{{ route('academic-years.destroy', $academicYear->id) }}" method="POST" class="d-inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this academic year?');">
                    Delete
                </button>
            </form>
        </td>
    </tr>
@endforeach

                                        </tbody>
                                    </table>
                                </div>

                                <div class="pagination-wrapper">
                                    {{ $academicYears->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Edit Modal -->
<!-- Edit Modal -->
<!-- Edit Modal (Outside Loop) -->
<div class="modal fade" id="editAcademicYearModal" tabindex="-1" role="dialog" aria-labelledby="editAcademicYearModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Academic Year</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editAcademicYearForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Form Fields -->
                    <div class="form-group">
                        <label for="start_year">Start Year</label>
                        <input type="date" name="start_year" id="start_year" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="end_year">End Year</label>
                        <input type="date" name="end_year" id="end_year" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="midterm_start_date">Midterm Start Date</label>
                        <input type="date" name="midterm_start_date" id="midterm_start_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="midterm_end_date">Midterm End Date</label>
                        <input type="date" name="midterm_end_date" id="midterm_end_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="annual_start_date">Annual Start Date</label>
                        <input type="date" name="annual_start_date" id="annual_start_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="annual_end_date">Annual End Date</label>
                        <input type="date" name="annual_end_date" id="annual_end_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>



    <!-- Add Modal -->
    <div class="modal fade" id="addAcademicYearModal" tabindex="-1" role="dialog" aria-labelledby="addAcademicYearModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Academic Year</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addAcademicYearForm">
                    @csrf
                    <div class="modal-body">
                        <!-- Description Field -->
                       
                        <!-- Row 1: Start Year and End Year -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="start_year">Start Year</label>
                                <input type="date" name="start_year" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="end_year">End Year</label>
                                <input type="date" name="end_year" class="form-control" required>
                            </div>
                        </div>
    
                        <!-- Row 2: Midterm Start Date and Midterm End Date -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="midterm_start_date">Midterm Start Date</label>
                                <input type="date" name="midterm_start_date" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="midterm_end_date">Midterm End Date</label>
                                <input type="date" name="midterm_end_date" class="form-control" required>
                            </div>
                        </div>
    
                        <!-- Row 3: Annual Start Date and Annual End Date -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="annual_start_date">Annual Start Date</label>
                                <input type="date" name="annual_start_date" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="annual_end_date">Annual End Date</label>
                                <input type="date" name="annual_end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Enter a brief description for the academic year" required></textarea>
                        </div>
    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Academic Year</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    

   
</x-layout>
<script>


$(document).ready(function() {
    $('.edit-academic-year-button').on('click', function() {
        // Get the data from the button's data attributes
        var id = $(this).data('id');
        var start = $(this).data('start');
        var end = $(this).data('end');
        var midtermStart = $(this).data('midterm-start');
        var midtermEnd = $(this).data('midterm-end');
        var annualStart = $(this).data('annual-start');
        var annualEnd = $(this).data('annual-end');
        var description = $(this).data('description');

        // Populate the modal with this data
        $('#editAcademicYearForm').attr('action', '/academic-years/' + id); // Set the form action for the correct route
        $('#start_year').val(start);
        $('#end_year').val(end);
        $('#midterm_start_date').val(midtermStart);
        $('#midterm_end_date').val(midtermEnd);
        $('#annual_start_date').val(annualStart);
        $('#annual_end_date').val(annualEnd);
        $('#description').val(description);
    });


    $('#addAcademicYearForm').on('submit', function (e) {
  e.preventDefault(); // Prevent default form submission

  $.ajax({
    url: "{{ route('academic-years.store') }}", // Laravel route for form submission
    type: "POST",
    data: $(this).serialize(), // Serialize form data
    success: function (response) {

    
      // Handle success with SweetAlert2
      swal({
        title: 'Success!',
        text: 'Academic Year added successfully!',
        icon: 'success',
        confirmButtonText: 'Close'
      }).then((result) => {
        if (result.isConfirmed) {
          $('#addAcademicYearModal').modal('hide'); // Close the modal
          // Optionally reload the page
          location.reload();
        }
      })
    },
    error: function (xhr) {
      // Handle errors with SweetAlert2

      console.log(xhr)
      let errors = xhr.responseJSON.errors;
      let errorMessages = '';
      for (let key in errors) {
        errorMessages += errors[key] + '\n';
      }
      swal({
        title: 'Error!',
        text: 'Failed to add academic year:\n' + errorMessages,
        icon: 'error',
        confirmButtonText: 'Close'
      });
    },
  });
});

$('#editAcademicYearModal form').on('submit', function (e) {
        e.preventDefault();

        var form = $(this);

        // Handle AJAX request for form submission
        $.ajax({
            url: form.attr('action'),
            type: 'PUT',
            data: form.serialize(),
            success: function (response) {
                

                swal({
        title: 'Success!',
        text: 'Academic Year added successfully!',
        icon: 'success',
        confirmButtonText: 'Close'
      })
          $('#addAcademicYearModal').modal('hide'); // Close the modal
          // Optionally reload the page
          location.reload();

        
            },
            error: function (xhr) {

                console.log(xhr)
      let errors = xhr.responseJSON.errors;
      let errorMessages = '';
      for (let key in errors) {
        errorMessages += errors[key] + '\n';
      }
      swal({
        title: 'Error!',
        text: 'Failed to add academic year:\n' + errorMessages,
        icon: 'error',
        confirmButtonText: 'Close'
      });
                // Handle error gracefully
                console.error('Error updating academic year: ', error);
                alert('Error updating academic year. Please try again later.');
            }
        });
    });
});

 




</script>

    