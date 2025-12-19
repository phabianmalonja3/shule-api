<x-layout>
    <x-slot:title>
        Monthly Attendance Report
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
                                <h4>Generate Monthly Attendance Report</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('attendance.monthly.report') }}" method="GET">
                                    <div class="form-group">
                                        <label for="stream">Stream:</label>
                                        <select class="form-control" name="stream" id="stream" required>
                                            @foreach ($streams as $stream)
                                            <option value="{{ $stream->id }}" {{ request('stream', $streams->
                                                first()->id) == $stream->id ? 'selected' : '' }}>
                                               Class {{ $stream->schoolClass->name }}  {{ $stream->alias }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="month">Month:</label>
                                        <select class="form-control" name="month" id="month" required>
                                            <option value="">Select Month</option>
                                            @for ($i = 1; $i <= 12; $i++) <option value="{{ $i }}" {{
                                                request('month', '' )==$i ? 'selected' : '' }}>
                                                {{ date('F', strtotime('2024-' . $i . '-01')) }}
                                                </option>
                                                @endfor
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="year">Year:</label>
                                        <input type="number" class="form-control" name="year" id="year" min="2000"
                                            max="{{ date('Y') }}" value="{{ request('year', date('Y')) }}" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Generate Report</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</x-layout>