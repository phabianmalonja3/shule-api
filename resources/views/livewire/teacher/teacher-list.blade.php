<div>
    <div class="row">
        <div class="col-12">
            <div class="p-3 card">
                <div class="card-header">
                    <h4>{{ \Str::title('List of Teachers') }}</h4>
                    <div class="card-header-form d-flex justify-content-between align-items-center">
                        <!-- Button to add a new teacher -->
                      @role(['header teacher','academic teacher'])
                       <a href="{{ route('teachers.create') }}" class="mr-2 btn btn-success">
                        <i class="fas fa-plus"></i> Add New Teacher
                    </a>
                       @endrole
                        <!-- Search form -->
                        <form class="py-4 ml-auto">

                            <div class="input-group">
                                <input type="text" wire:model.debounce.200ms.live="search" class="form-control" placeholder="Search teachers..." value="{{ request('search') }}">
                                <span wire:loading>
                                    <i class="fa fa-spinner fa-spin"></i>
            
                                </span>
                            </div>
                            
                        </form>
                    </div>
                </div>
    
                <div class="p-0 card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th> <!-- Serial Number -->
                                    <th>Full Name</th>
                                    <th>Phone Number</th>
                                    <th>Role</th>

                                    @if(auth()->user()->hasAnyRole(['header teacher','academic teacher' ,'assistant headteacher']))
                                    
                                    <th>Status</th>
                                    @endif
                                    <th >Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($teachers as $index => $teacher)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $teacher->name }}</td>
                                        <td>{{ $teacher->phone }}</td>
                                        @php
                                            $role = \Str::title($teacher->getRoleNames()->first());
                                            $role = $role == 'Header Teacher'? "Headteacher" : $role;
                                            if(Str::contains(strtolower(auth()->user()->school->name),'secondary') && Str::contains(strtolower($role), 'head')){
                                                if($role == "Header Teacher"){
                                                    $role = "Head of School";
                                                }elseif($role == "Assistant Headerteacher"){
                                                    $role = "Assistant Head of School";
                                                }
                                            }
                                            
                                            $created_by = \App\Models\User::select('name')->where('id',$teacher->created_by)->first();
                                        @endphp
                                        <td>{{  $role ?? 'N/A' }}</td>
    
                                        @if(auth()->user()->hasAnyRole(['header teacher','academic teacher']))
                                        
                                        <td>
                                            <span class="badge {{ $teacher->is_verified ? 'badge-success' : 'badge-danger' }}" style="min-width: 70px;">
                                                {{ $teacher->is_verified ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        @endif
    
                                        <td class="">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('teachers.show', $teacher->id) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-info-circle "></i> Details
                                                </a>

                                                @if(auth()->user()->hasAnyRole(['header teacher','assistant headteacher','academic teacher']))
                                                
                                                    <a href="{{ route('teachers.edit', $teacher->id) }}" class="mx-1 btn btn-warning btn-sm" 
                                                         @if (!auth()->user()->hasRole('academic teacher') && auth()->user()->id != $teacher->created_by) 
                                                         style="pointer-events: none; opacity: 0.65;" title="Can only be edited by {{ $created_by->name?? '' }}" @endif>
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    
                                                    <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                            @if($teacher->roles->first()->name =='header teacher') 
                                                                disabled style="opacity: 0.65;" title="Can only be deleted by an Administrator."
                                                            @elseif(auth()->user()->id != $teacher->created_by)
                                                                disabled style="opacity: 0.65;" title="Can only be deleted by {{ $created_by->name?? '' }}."
                                                            @endif 
                                                            onclick="return confirm('Are you sure you want to delete the teacher?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>

                                                    @if(auth()->user()->hasRole('academic teacher'))
                                                        @php
                                                            if($teacher->is_verified){
                                                                $message = "deactivate";
                                                            }else{
                                                                $message = "activate";
                                                            } 
                                                        @endphp
                                                        <button wire:click="toggleVerification({{ $teacher->id }}, {{ $teacher->is_verified?? '' }})"
                                                            class="mx-1 btn btn-primary btn-sm" style="min-width: 100px;"
                                                            @if ($teacher->roles->first()->name =='header teacher') 
                                                                disabled 
                                                                style="opacity: 0.65;" 
                                                                title="Can only be changed by an Administrator." 
                                                            @endif
                                                            onclick="return confirm('Are you sure you want to {{ $message }} the teacher?')">
                                                            @if($teacher->is_verified) 
                                                                <i class="fas fa-check-circle"></i> {{ ucfirst($message) }} 
                                                            @else 
                                                                <i class="fas fa-ban"></i> {{ ucfirst($message) }} 
                                                            @endif
                                                        </button> 
                                                    @endif                                                    
                                                @endif
                                               
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No teachers found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- <div class="px-2">
                            {{ $teachers->links() }}
                        </div> --}}
    
                        {{-- <div x-intersect.full="$wire.loadMore()" class="p-2 text-center">
                            <div wire:loading wire:target="loadMore" class="text-center">
                                <div class="lds">
                                    <div></div><div></div><div></div><div></div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
