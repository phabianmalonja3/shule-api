<div class="card shadow-sm border-0 rounded-lg">
    <div class="card-body">
        
<!-- Search and Filters Bar -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4" style="gap: 15px;">
    <!-- Search Input -->
    <div class="position-relative flex-grow-1" style="min-width: 280px; max-width: 500px;">
        <span class="position-absolute" style="top: 12px; left: 15px; color: #9ca3af;">
            <i class="fas fa-search"></i>
        </span>
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search" 
            placeholder="Search by applicant name or phone..." 
            class="form-control pl-5 pr-5 border-dark-subtle"
            style="height: 38px; border-radius: 8px;"
        >
        <span class="position-absolute badge bg-white border text-muted shadow-sm" style="top: 10px; right: 12px; font-size: 11px; padding: 4px 6px; border-radius: 6px;">
            ⌘ K
        </span>
    </div>

    <!-- Filter Dropdowns -->
    <div class="d-flex align-items-center" style="gap: 10px;">
@if(count($statuses) > 1)
    <div class="dropdown">
        <button class="btn btn-white border-dark-subtle px-3 py-2 bg-white shadow-sm font-14 d-flex align-items-center text-dark" type="button" data-toggle="dropdown" style="border-radius: 8px; gap: 8px; height: 38px;">
            <span class="text-dark font-weight-normal" style="opacity: 1 !important; visibility: visible !important; display: inline-block !important;">Status</span> 
            <span class="badge badge-primary rounded-circle px-2 py-1" style="opacity:4">
                {{ count($statuses) }}
            </span>
            <i class="fas fa-chevron-down text-muted font-11 ml-1"></i>
        </button>
        <div class="dropdown-menu shadow-sm border-0 mt-1">
            <!-- Reset filter option -->
            <a class="dropdown-item" href="#" wire:click.prevent="$set('status', '')">
                All Statuses
            </a>
            <div class="dropdown-divider"></div>
            
            <!-- Loop through array of status names -->
            @foreach($statuses as $statusName)
                <a class="dropdown-item" href="#" wire:click.prevent="$set('status', '{{ $statusName }}')">
                    {{ ucfirst($statusName) }}
                </a>
            @endforeach
        </div>
    </div>
@endif
    </div>
</div>

        <!-- Table View -->
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th class="border-0 py-3">Applicant</th>
                        <th class="border-0 py-3">Status</th>
                        <th class="border-0 py-3">Progress</th>
                        <th class="border-0 py-3 text-center">Confirmed</th>
                        <th class="border-0 py-3 text-center">Notified</th>
                        <th class="border-0 py-3">Updated</th>
                        <th class="border-0 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        <tr>
                            <!-- Applicant Name & Parent Phone -->
                            <td class="py-3">
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <div class="rounded-circle bg-light text-primary font-weight-bold d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px; font-size: 13px;">
                                        {{ strtoupper(substr($application->applicant->first_name ?? 'A', 0, 1)) }}{{ strtoupper(substr($application->applicant->last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold text-dark mb-0 font-14">
                                            {{ $application->applicant->first_name ?? 'Unknown' }} {{ $application->applicant->last_name ?? 'Applicant' }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-phone-alt font-10 mr-1"></i> {{ $application->applicant->parent_phone ?? $application->applicant->phone ?? 'N/A' }}
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <!-- Status Badge with Hover Last Step -->
                            <td class="py-3 align-middle">
                                @if($application->status === 'in progress')
                                    <span class="badge badge-pill px-3 py-2 font-12" style="background-color: #f3e8ff; color: #6b21a8;" title="Last step: {{ $application->last_step ?? 'Document Upload' }}" data-toggle="tooltip">
                                        <i class="fas fa-circle font-8 mr-1 text-purple"></i> In progress
                                    </span>
                                @elseif($application->status === 'completed')
                                    <span class="badge badge-pill px-3 py-2 font-12" style="background-color: #e0f2fe; color: #0369a1;">
                                        <i class="fas fa-circle font-8 mr-1 text-info"></i> Completed
                                    </span>
                                @elseif($application->status === 'submitted')
                                    <span class="badge badge-pill px-3 py-2 font-12" style="background-color: #d1fae5; color: #065f46;">
                                        <i class="fas fa-circle font-8 mr-1 text-success"></i> Submitted
                                    </span>
                                @elseif($application->status === 'selected')
                                    <span class="badge badge-pill px-3 py-2 font-12" style="background-color: #dcfce7; color: #166534;">
                                        <i class="fas fa-check-circle font-10 mr-1 text-success"></i> Selected
                                    </span>
                                @elseif($application->status === 'not selected')
                                    <span class="badge badge-pill px-3 py-2 font-12" style="background-color: #fee2e2; color: #991b1b;">
                                        <i class="fas fa-times-circle font-10 mr-1 text-danger"></i> Not selected
                                    </span>
                                @else
                                    <span class="badge badge-pill px-3 py-2 font-12 bg-light text-muted">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Progress Bar -->
                            <td class="py-3 align-middle" style="width: 200px;">
                                <div class="d-flex justify-content-between font-12 text-muted mb-1">
                                    <span>Tasks complete</span>
                                    <span class="font-weight-bold text-dark"> 92% </span>
                                </div>
                                <div class="progress" style="height: 6px; background-color: #f3f4f6;">
                                    <div class="progress-bar rounded-pill bg-primary" role="progressbar" style="width: {{ $application->progress_percentage ?? '82%' }};"></div>
                                </div>
                            </td>

                            <!-- Selection Confirmed -->
                            <td class="py-3 align-middle text-center">
                                @if($application->is_confirmed)
                                    <span class="badge badge-success px-2 py-1 font-11">Yes</span>
                                @else
                                    <span class="badge badge-light text-muted border px-2 py-1 font-11">No</span>
                                @endif
                            </td>

                            <!-- Notified Status -->
                            <td class="py-3 align-middle text-center">
                                @if($application->is_notified)
                                    <span class="text-success font-13" title="Notified"><i class="fas fa-paper-plane"></i></span>
                                @else
                                    <span class="text-muted font-13" title="Not Notified"><i class="far fa-paper-plane"></i></span>
                                @endif
                            </td>


                            <!-- Actions -->
                            <td class="py-3 align-middle text-right">
                                <button class="btn btn-sm btn-light text-muted border-0 bg-transparent">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No applications found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $applications->links() }}
        </div>

    </div>
</div>