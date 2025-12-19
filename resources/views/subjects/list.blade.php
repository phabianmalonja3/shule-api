<x-layout>
    <x-slot:title>
        Combination & Subjects
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
								<h4> Combinations & Subjects</h4>
								@role('academic teacher')
								<div class="card-header-form d-flex justify-content-between align-items-center">
								
									<a href="{{ route('subjects.create') }}" class="mt-3 mr-3 btn btn-success">
										<i class="fas fa-plus"></i> Add Subject
									</a>

									<button type="button" class="mt-3 mr-3 btn btn-success" data-toggle="modal" data-target="#addCombinationModal">
										<i class="fas fa-plus"></i> Add Combination
									</button>

									@if(count($school->combinations) > 0)
										<button type="button" class="mt-3 mr-3 btn btn-info" data-toggle="modal" data-target="#editCombinationModal">
											<i class="fas fa-edit"></i> Edit Combination
										</button>
								
										<button type="button" class="mt-3 mr-3 btn btn-danger" data-toggle="modal" data-target="#deleteCombinationModal">
											<i class="fas fa-trash-alt mr-1"></i> Delete Combination
										</button>	
									@endif
									
								</div>
								@endrole
							</div>

							<div class="card-body p-3">
								@forelse($school->combinations as $combination)
									<fieldset class="mb-4 p-3" style="border: 1px solid #e4e6fc; border-radius: 8px;">
										<legend class="w-auto px-2 ml-3">
											<h6 class="font-weight-bold">
												{{ ucfirst($combination->name) }}
											</h6>
										</legend>

										<div class="row">
											@forelse($combination->subjects as $subject)
												<div class="col-md-3 col-sm-6 mb-2 d-flex align-items-center">
													<i class="fas fa-circle mr-2" style="font-size: 8px; color: #6777ef;"></i>
													<span>{{ $subject->name }}</span>
												</div>
											@empty
												<div class="col-12 text-muted italic">
													No subjects assigned to this combination.
												</div>
											@endforelse
										</div>
									</fieldset>
								@empty
									<div class="text-center py-4">
										<p>No combinations found for this school.</p>
									</div>
								@endforelse
							</div>
						</div>
					</div>

                </div>
            </div>
        </section>
		<div class="modal fade" id="addCombinationModal" tabindex="-1" role="dialog" aria-labelledby="addCombinationModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="addCombinationModalLabel">Add Combination</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<form action="{{ route('combination.add') }}" method="POST">
						@csrf
						<div class="modal-body">
							<div class="form-group">
								<label for="combination_id">Select Combination</label>
								<select class="form-control select2" id="combination_id" name="combination_id" required style="width: 100%;">
									<option value="" selected disabled>-- Choose Combination --</option>
									@foreach($combinations as $combination)
										<option value="{{ $combination->id }}">{{ $combination->name }}</option>
									@endforeach
								</select>						
							</div>
							
							<div class="form-group">
								<label for="subjects_list">Select Subjects</label>
								<select class="form-control ss-select-tags" id="subjects_list" name="subjects[]" multiple="multiple" style="width: 100%; height: 50%" required>
									@isset($subjects)
										@foreach($subjects as $subject)
											<option value="{{ $subject->id }}">{{ $subject->name }}</option>
										@endforeach
									@else
										<option disabled>Subjects not available.</option>
									@endisset
								</select>
								<small class="form-text text-muted">Hold down Ctrl (Windows) or Cmd (Mac) to select multiple subjects.</small>
							</div>
							
						</div>
						
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary">Add</button>
						</div>
					</form>
					
				</div>
			</div>
		</div>
		
		<div class="modal fade" id="editCombinationModal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Edit Combination</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<form action="{{ route('combination.update') }}" method="POST">
						@csrf
						@method('PUT')
						<div class="modal-body">
							<div class="form-group">
								<label>Select Combination</label>
								<select class="form-control select2" id="edit_combination_id" name="combination_id" required style="width: 100%;">
									<option value="" selected disabled>-- Choose Combination --</option>
									@foreach($school->combinations as $comb)
										<option value="{{ $comb->id }}">{{ $comb->name }}</option>
									@endforeach
								</select>
							</div>

							<div class="form-group">
								<label class="d-block">Assigned Subjects</label>
								<div id="assigned-subjects-container" class="border p-3 rounded" style="max-height: 200px; overflow-y: auto; background: #f9f9f9;">
									<p class="text-muted">Select a combination above to view subjects.</p>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-danger">Update Combination</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<div class="modal fade" id="deleteCombinationModal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title text-danger">Delete Combination</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<form action="{{ route('combination.delete') }}" method="POST">
						@csrf
						@method('DELETE')
						<div class="modal-body">
							<div class="alert alert-warning">
								<i class="fas fa-exclamation-triangle"></i> 
								<span class="text-small"><strong>Warning:</strong> This will remove the combination from the school. Assigned subjects will be detached.</span>
							</div>
							
							<div class="form-group">
								<label>Select Combination</label>
								<select class="form-control select2" id="delete_combination_id" name="combination_id" required style="width: 100%;">
									<option value="" selected disabled>-- Choose Combination --</option>
									@foreach($school->combinations as $comb)
										<option value="{{ $comb->id }}">{{ $comb->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this combination?')">Delete</button>
						</div>
					</form>
				</div>
			</div>
		</div>	
    </div>
</x-layout>

<script>
$(document).ready(function() {
    $('#edit_combination_id').on('change', function() {
        let combinationId = $(this).val();
        let container = $('#assigned-subjects-container');

        // Show loading state
        container.html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');

        // Fetch subjects for this combination
        $.ajax({
            url: `/combinations/${combinationId}/subjects`, // You will need to create this route
            method: 'GET',
            success: function(response) {
                container.empty();
                
                // Response should contain ALL subjects and an array of ASSIGNED subject IDs
                response.allSubjects.forEach(subject => {
                    let isChecked = response.assignedIds.includes(subject.id) ? 'checked' : '';
                    
                    container.append(`
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" name="subjects[]" value="${subject.id}" 
                                   class="custom-control-input" id="sub_${subject.id}" ${isChecked}>
                            <label class="custom-control-label" for="sub_${subject.id}">
                                ${subject.name}
                            </label>
                        </div>
                    `);
                });
            },
            error: function() {
                container.html('<span class="text-danger">Error loading subjects.</span>');
            }
        });
    });
});
</script>