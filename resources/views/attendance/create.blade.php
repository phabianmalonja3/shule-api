<x-layout>
    <x-slot:title>
        Take Attendance
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <h4>Take Attendance</h4>
                            <div class="mb-0 form-group">
                                <label for="stream" class="mr-2">Filter by Stream:</label>
                                <select class="w-auto form-control d-inline-block" id="stream" name="stream">
                                    <option value="" selected>All Streams</option>
                                    @foreach ($streams as $stream)
                                        <option value="{{ $stream->id }}" {{ request('stream') == $stream->id ? 'selected' : '' }}>
                                            {{ $stream->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                            <div class="card-body">
                              
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>