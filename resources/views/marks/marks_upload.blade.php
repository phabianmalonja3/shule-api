<x-layout>
    <x-slot:title>
        Marks Uploading Setup
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar :school="$school" />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-header">
                            <h4>Marks Uploading Setup</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('marks-upload.store') }}" id="uploadForm">
                                @csrf
                                <input type="hidden" name="school_id" value="{{ $school->id }}">
                                <input type="hidden" name="upload_by" id="upload_by_input">

                                <div class="form-group">
                                    <label for="upload_by">Who can upload marks?</label>
                                    <select class="form-control" id="upload_by" onchange="handleSelection(this)">
                                        <option value="" selected disabled>Choose Option</option>
                                        <option value="academic" @if($school->is_teacher_upload == 0) selected @endif>Academic Teacher Only</option>
                                        <option value="subject" @if($school->is_teacher_upload == 1) selected @endif>Subject Teachers</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal for Subject Teachers -->
    <div class="modal fade" id="confirmModalSubject" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Subject Teacher Selection</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to allow teachers to upload their subject's marks?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUploadBySubject()">Yes,
                        Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Academic Teacher -->
    <div class="modal fade" id="confirmModalAcademic" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Academic Teacher Selection</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to allow an academic teacher only to upload marks?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUploadByAcademic()">Yes,
                        Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        function handleSelection(select) {
            const value = select.value;
            document.getElementById('upload_by_input').value = value;

            if (value === 'subject') {
                $('#confirmModalSubject').modal('show');
            } else if (value === 'academic') {
                $('#confirmModalAcademic').modal('show');
            }
        }

        function submitFormAjax() {
            const form = $('#uploadForm');
            const url = form.attr('action');
            const formData = form.serialize();

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },


                success: function(response) {
                    // Hide all modals
                    $('.modal').modal('hide');
                    Toastify({
                        text: response.message || "Upload permission updated successfully.",
                        duration: 3000,
                        gravity: "top", // top or bottom
                        position: "right", // left, center or right
                        backgroundColor: "#4CAF50", // green
                    }).showToast();
                },

                error: function(xhr) {
                    // Try to get validation errors from JSON response
                    let errors = xhr.responseJSON?.errors;

                    if (errors) {
                        // Get the first error message from the errors object
                        let firstError = Object.values(errors)[0][0];
                        alert(firstError);
                    } else if (xhr.responseJSON?.message) {
                        // If there's a general message from the backend (like a 500 error message)
                        alert(xhr.responseJSON.message);
                    } else {
                        alert('An error occurred, please try again.');
                    }
                }


            });
        }

        function confirmUploadBySubject() {
            submitFormAjax();
        }

        function confirmUploadByAcademic() {
            submitFormAjax();
        }
    </script>

</x-layout>

<!-- JS -->
