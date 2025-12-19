<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>List of Registered Schools</h4>
                    <div class="row">

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="mx-2 col-md-6">
                                <div class="input-group">

                                    <label class="px-3 mt-2">Sort Option</label>
                                    <select id="sortOrder" wire:model.live="sortOrder" class="form-control">
                                        {{-- <option value="desc" selected>Sort Option</option> --}}
                                        <option value="asc">Join At (Older)</option>
                                        <option value="desc">Join At (Newest)</option>
                                    </select>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <form>
                                    <div class=" input-group">
                                        <label for="searchInput" class="mx-2">Search by name</label>
                                        <input type="text" id="searchInput" class="form-control"
                                            placeholder="Search by name" wire:model.debounce.200ms.live="search">
                                        <span wire:loading>
                                            <i class="fa fa-spinner fa-spin"></i>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-0 card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th class="text-center">

                                    </th>

                                    <th>#</th>
                                    <th>School Name</th>
                                    <th>Level</th>
                                    <th>government/private</th>
                                    <th>Headteacher Name</th>
                                    <th>Headteacher Phone</th>

                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                <tr>


                                    @forelse ($schools as $index=>$school)
                                        <td class="p-0 text-center">

                                        </td>
                                        <td class="p-0 text-center">
{{ $loop->iteration }}
                                        </td>

                                        <td>{{ $school->name }}</td>
                                        <td>
                                            @if ($school->school_type && is_array($types = json_decode($school->school_type, true)))
                                                {{ implode(', ', $types) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $school->sponsorship_type }}</td>
                                        <td>{{ $school->headerTeacher->name ?? 'Not Available' }}</td>
                                        <td>{{ $school->headerTeacher->phone ?? 'Not Available' }}</td>





                                        <td>
                                            @if ($school->is_active)
                                                <span class="">
                                                    <i class="fas fa-check-circle text-success"></i> Active
                                                </span>
                                            @else
                                                <span class="">
                                                    <i class="fas fa-times-circle text-danger"></i>  inactive
                                                </span>
                                            @endif
                                        </td>


                                        <td><a href="{{ route('school.view', ['school' => $school->id]) }}"
                                                class="btn btn-primary">Detail</a></td>
                                </tr>


                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No School found.</td>
                                </tr>
                                @endforelse

                            </tbody>


                        </table>
                        <div class="px-2">
                            {{ $schools->links() }}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
