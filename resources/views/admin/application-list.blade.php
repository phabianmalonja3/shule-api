<x-layout>
  <x-slot:title>
    Application List Page
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
                <h4>School Application Requests</h4>
                <div class="card-header-form">
                  <form>
                    <div class="input-group">
                      <form action="" method="">

                        <input type="text" class="form-control" placeholder="Search by name or city" name="search">
                        <div class="input-group-btn">
                          <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                      </form>
                    </div>
                  </form>
                </div>
              </div>
              <div class="p-0 card-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <tbody>
                      <tr>
                        <th class="text-center">
                          
                        </th>
                        <th>School Name</th>
                        <th>Headmaster/Headmistres</th>
                        <th>Phone Number</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                      <tr>


                        @forelse ($applications as $application)
                        <td class="p-0 text-center">
                          
                        </td>
                        <td>{{$application->school_name}}</td>
                        <td>{{$application->fullname}}</td>
                        <td>{{$application->phone}}</td>
                        <td>
                          @if(isset($application->region, $application->district, $application->ward))
                          {{ $application->ward }},
                          {{ $application->district }},
                          {{ $application->region }},
                          @else
                          <em>No location data</em>
                          @endif
                        </td>


                        <td>
                          @if($application->status === 'complete')
                          <!-- Status is Complete -->
                          <span class="">
                            <i class="fas fa-check-circle text-success"></i> Complete
                          </span>
                          @elseif($application->status === 'progress')
                          <!-- Status is In Progress -->
                          <span class="">
                            <i class="fas fa-circle-notch fa-spin "></i> In Progress
                          </span>

                          @elseif($application->status === 'scheduled')
                          <!-- Status is In Progress -->
                          <span class="">
                            <i class="fas fa-calendar-check fa-spin"></i> Scheduled
                          </span>
                          @else
                          <!-- Status is Pending -->
                          <span class=" text-dark">
                            <i class="fas fa-times-circle text-warning"></i> Pending
                          </span>
                          @endif
                        </td>




                        <td><a href="{{route('application.show',['application'=>$application->id])}}"
                            class="btn btn-primary">Detail</a></td>
                      </tr>


                      @empty

                      @endforelse
                    </tbody>


                  </table>
                  <div class="px-2">
                    {{ $applications->links() }}
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

  </div>
</x-layout>