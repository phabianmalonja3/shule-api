<div>
    <div class="row">
        <div class="col-12">
            <h4>{{ \Str::title('List of Announcements') }}</h4>

            <!-- Announcements -->
            <div class="row">
                @foreach ($announcements as $announcement)
                    <div class="mb-3 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $announcement->title }}</h5>
                                @if (isset($expandedAnnouncement[$announcement->id]))
                                <p class="card-text">{{ $announcement->content }}</p><br/>
                                <small>
                                    <i class="fas fa-calendar-alt"></i> Start Date:  {{  \Carbon\Carbon::parse($announcement->start_date)->format('d M Y') }}
                                </small><br />
                                <small>{{ $announcement->created_at->diffForHumans() }}</small><br />
                                <small>
                                    <i class="fas fa-user"></i> By: {{ $announcement->user->name }} | 
                                    <i class="fas fa-user-shield"></i> Role: 
                                    
                                    {{ $announcement->user->getRoleNames()->first() }}
                                </small><br />
                                
                                <button wire:click="toggleDetails({{ $announcement->id }})" class="mt-3 btn btn-primary">Read Less</button>
                            @else
                                <p class="card-text">{{ \Str::limit($announcement->content, 100) }}</p>
                                <small>
                                    <i class="fas fa-calendar-alt"></i> Start Date: {{  \Carbon\Carbon::parse($announcement->start_date)->format('d M Y') }}
                                </small><br />
                                <small>{{ $announcement->created_at->diffForHumans() }}</small><br/>
                                <small>
                                    <i class="fas fa-user"></i> By: {{ $announcement->user->name }} | 
                                    <i class="fas fa-user-shield"></i> Role: 
                                  
                                        {{ $announcement->user->getRoleNames()->first() }}
                                   
                                </small><br />
                                
                                <button  wire:click="toggleDetails({{ $announcement->id }})" class="mt-3 btn btn-primary">Read More</button>
                            @endif

                                
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Infinite Scroll Loader -->
            <div x-intersect.full="$wire.loadMore()" class="p-2 text-center">
                <div wire:loading wire:target="loadMore" class="text-center">
                    <div class="lds">
                        <div></div><div></div><div></div><div></div>
                    </div>
                </div>
            </div>

            
        </div>
    </div>
</div>
