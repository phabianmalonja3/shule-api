<x-layout>
    <x-slot:title>
        Assignment Details For {{ $assignment->subject->name }}
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-header">
                <h1></h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <!-- First Column: Teacher Details -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Assignment Details For {{ $assignment->subject->name }}</h4>
                            </div>
                            <div class="card-body">
                                <!-- Teacher Details -->
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th>Subject Name</th>
                                            <td>{{ $assignment->subject->name }}</td>
                                        </tr>
                                        
                                        <tr>
                                            <th>streams</th>
                                            

                                            
                                            <td>  

                                                @foreach($assignment->streams as $stream)
                                                <span>{{ $stream->name }}{{ !$loop->last ? ', ' : '' }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Assigment File</th>
                                            
                                            <td><a href="{{asset('storage/'.$assignment->file_path) }}" target="_blank"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-file-pdf"></i> View
                                            </a></td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            
                                            <td>{{ $assignment->created_at->diffForHumans() }}</td>
                                        </tr>
                                        <tr>
                                            <th>DeadLine</th>
                                            
                                            <td>

                                                {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y,
                                                    H:i') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Second Column: User Profile -->
                
                </div>

                <a href="{{ url()->previous() }}" class="btn btn-primary">Go Back</a>
            </div>
        </section>
    </div>
</x-layout>
