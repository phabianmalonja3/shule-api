<div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="text-center card-header font-weight-bold">
                    {{ __('Examination Results for ') }} {{ $student->user->name }}
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="student">Student Name:</label>
                            <select class="form-control" wire:model="student_id" wire:change="fetchResults">
                                <option value="">-- Select Student --</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->user->name }} ({{ $student->reg_number }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="examType">Filter by Exam Type:</label>
                            <select wire:model="examType" class="form-control" wire:change="fetchResults">
                                @foreach($examTypes as $id => $name)
                                    <option value="{{ $id }}">{{ ucfirst($name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if($results->isEmpty())
                        <div class="mt-4 text-center">
                            {{ __('No examination results available.') }}
                        </div>
                    @else

                    <div class="pt-2">
                        
                        {{-- @if($examTypeName =='Monthly')
                        @foreach ($months as $key => $month)
                            <div id="accordion{{ $key }}">
                                <div class="accordion">
                                    <div class="accordion-header collapsed" role="button" data-toggle="collapse"
                                         data-target="#panel-body-inside-{{ $key }}" aria-expanded="false">
                                        <h4>{{ $month }}</h4>
                                    </div>
                                    <div class="accordion-body collapse" id="panel-body-inside-{{ $key }}" data-parent="#accordion{{ $key }}" style="">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Student Name</th>
                                                        <th>Registration Number</th>
                                                 
                                                        <th>Total Marks</th>
                                                        <th>Average Marks</th>
                                                        <th>Position</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $i = 1;
                                                    @endphp
                                                    @foreach ($results->sortBy('position') as $result)
                                                        @if ($result->month == $key)
                                                            <tr>
                                                                <td>{{ $i++ }}</td>
                                                                <td>{{ $result->student->user->name }}</td>
                                                                <td>{{ $result->student->reg_number }}</td>
                                                              
                                                                <td>{{ $result->total_marks }}</td>
                                                                <td>{{ number_format($result->average_marks, 2) }}</td>
                                                                <td>{{ $result->position ?? 'N/A' }}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @else
                        @endif --}}

                        @if($examTypeName =='Monthly')
    @foreach ($months as $key => $month)
        @php
            // Check if there are any results for this month
            $hasResults = $results->where('month', $key)->isNotEmpty();
        @endphp

        @if($hasResults)
            <div id="accordion{{ $key }}">
                <div class="accordion">
                    <div class="accordion-header collapsed" role="button" data-toggle="collapse"
                         data-target="#panel-body-inside-{{ $key }}" aria-expanded="false">
                        <h4>{{ $month }}</h4>
                    </div>
                    <div class="accordion-body collapse" id="panel-body-inside-{{ $key }}" data-parent="#accordion{{ $key }}">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student Name</th>
                                        <th>Registration Number</th>
                                        <th>Total Marks</th>
                                        <th>Average Marks</th>
                                        <th>Position</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    {{ $results }}
                                    @foreach ($results->sortBy('position') as $result)
                                        @if ($result->month == $key)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $result->student->user->name }}</td>
                                                <td>{{ $result->student->reg_number }}</td>
                                                <td>{{ $result->total_marks }}</td>
                                                <td>{{ number_format($result->average_marks, 2) }}</td>
                                                <td>{{ $result->position ?? 'N/A' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif

                    </div>
                    @endif
                </div> 
            </div>
        </div>
    </div>
</div>
