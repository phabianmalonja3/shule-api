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
                        <button class="btn btn-white border px-3 py-2 bg-white shadow-sm font-14 d-flex align-items-center text-dark" type="button" data-toggle="dropdown" style="border-radius: 8px; gap: 8px;">
                            <span class="text-dark font-weight-normal" style="opacity: 1 !important; visibility: visible !important; display: inline-block !important;">Status</span> 
                            
                            <!-- Badge with matching text color and lower-opacity background enclosure -->
                            <span class="badge px-2 py-1 font-weight-bold" style="background-color: rgba(67, 94, 190, 0.12); color: #435ebe; border-radius: 6px;">
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
@if($this->hasSelectedOrNotSelected)
    <th class="border-0 py-3 text-center">Notified</th>
@endif

{{-- Confirmed Column: Appears only if at least one application is notified --}}
@if($this->hasNotified)
    <th class="border-0 py-3 text-center">Confirmed</th>
@endif                        
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
                                    @php
                                        $parentPhones = $application->applicant->parents()->pluck('phone')->filter()->implode(', ');

                                        $firstName = trim($application->applicant->first_name ?? 'A');
                                        $initials = collect(explode(' ', $firstName))
                                            ->filter()
                                            ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                                            ->take(2) // Limits to max 2 characters for the avatar circle
                                            ->implode('');

                                        if (empty($initials)) {
                                            $initials = 'A';
                                        }

                                        $hue = abs(crc32($initials)) % 360;

                                        $bgColor = "hsl({$hue}, 75%, 93%)";
                                        $textColor = "hsl({$hue}, 65%, 30%)";
                                    @endphp

                                    <div class="rounded-circle font-weight-bold d-flex align-items-center justify-content-center border-0 shadow-sm" 
                                        style="width: 40px; height: 40px; font-size: 13px; background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                        {{ $initials }}
                                    </div>

                                    <div>
                                        <h6 class="font-weight-bold text-dark mb-0 font-14">
                                            {{ $application->applicant->first_name ?? 'Unknown' }} {{ $application->applicant->surname ?? 'Applicant' }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-phone-alt font-10 mr-1"></i> {{ $parentPhones ?? 'N/A' }}
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
                            <td class="py-3 align-middle" style="width: 162px;">
                                <div class="d-flex justify-content-between font-12 text-muted mb-1">
                                    <span>Steps completed by</span>
                                    <span class="font-weight-bold text-dark"> @if($application->status == "in progress") 92% @else 100% @endif</span>
                                </div>
                                <div class="progress" style="height: 6px; background-color: #f3f4f6;">
                                    <div class="progress-bar rounded-pill bg-primary" role="progressbar" style="width: {{ $application->status == 'in progress'? $application->progress_percentage : '100%' }};"></div>
                                </div>
                            </td>

                            @if($this->hasNotified)
                                <td class="py-3 align-middle text-center">
                                    @if($application->is_notified == 1)
<span class="badge rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" 
                      style="width: 30px; height: 30px; background-color: #d1fae5; color: #065f46;"
                      title="Already being notified" data-toggle="tooltip">
                    <i class="fas fa-check font-12"></i>
                </span>
                                    @else
 <span class="badge rounded-circle d-inline-flex align-items-center justify-content-center" 
                      style="width: 30px; height: 30px; background-color: #fee2e2; color: #991b1b;"
                      title="Awaiting notification" data-toggle="tooltip">
                    <i class="fas fa-times font-12"></i>
                                </td>
                            @endif
@endif
                            
                            @if($this->hasSelectedOrNotSelected)
                                <td class="py-3 align-middle text-center">
                                    @if($application->is_confirmed == 1)
<span class="badge rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" 
                      style="width: 30px; height: 30px; background-color: #d1fae5; color: #065f46;"
                      title="Confirmed" data-toggle="tooltip">
                    <i class="fas fa-check font-12"></i>
                </span>
                                    @else
 <span class="badge rounded-circle d-inline-flex align-items-center justify-content-center" 
                      style="width: 30px; height: 30px; background-color: #fee2e2; color: #991b1b;"
                      title="Awaiting confirmation" data-toggle="tooltip">
                    <i class="fas fa-times font-12"></i>
                </span>                                    @endif
                                </td>
                            @endif
                            <td> </td>
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