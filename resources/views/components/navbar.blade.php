<div class="navbar-bg"></div>
  <nav class="sticky navbar navbar-expand-lg main-navbar">
    <div class="mr-auto form-inline">
      <ul class="mr-3 navbar-nav">
        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn"> <i data-feather="align-justify"></i></a></li>
      </ul>
    </div>
    <ul class="navbar-nav navbar-right">
      <li class="dropdown dropdown-list-toggle">
        <a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg">
          <i data-feather="bell" class="bell"></i>
        </a>
        @php
          $user = auth()->user();
          $notifications = $user->unreadNotifications;
        @endphp
        <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
          <div class="dropdown-header">
              Notifications
              <div class="float-right">
                  <a href="{{ route('mark.notifications') }}">Mark All As Read</a>
              </div>
          </div>
          <div class="dropdown-list-content dropdown-list-icons">
              @forelse($notifications as $notification)
                  <a href="{{route('application.show',['application'=>$notification->data['application_id']])}}" class="dropdown-item">
                      <span class="text-white dropdown-item-icon bg-info">
                          <i class="far fa-user"></i>
                      </span>
                      <span class="dropdown-item-desc">
                          <b>{{ $notification->data['application_id'] }}</b> 
                          {{ $notification->data['school_name'] }} 
                          <span class="time">{{ $notification->created_at->diffForHumans() }}</span>
                      </span>
                  </a>

                  @empty
                  <span class="px-4 dropdown-item-desc">
                    No Notifications
                </span>
              @endforelse
          </div>
          <div class="text-center dropdown-footer">
              <a href="#">View All <i class="fas fa-chevron-right"></i></a>
          </div>
        </div>
      </li>
        
      <li class="dropdown">
        <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user"> 
          <img alt="image" 
              src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('profile/png-clipart-profile-logo-computer-icons-user-user-blue-heroes-thumbnail.png') }}" 
              class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;"> 
          <span class="d-sm-none d-lg-inline-block"></span>
        </a>
    
        <div class="dropdown-menu dropdown-menu-right pullDown">

          @php
              $nameParts = explode(' ', auth()->user()->name);
              $firstPart = $nameParts[0] ?? ''; 
              $thirdPart = $nameParts[2] ?? ''; 
              $roleName = auth()->user()->getRoleNames()->first();
              $displayRoleName = str_replace('header teacher', 'head teacher', $roleName);

              if (auth()->user()->hasAnyRole(['header teacher', 'assistant headteacher'])){
                if(Str::contains(strtolower(auth()->user()->school->name),'secondary') && Str::contains(strtolower($displayRoleName), 'head')){
                    if($displayRoleName == "head teacher"){
                        $displayRoleName = "Head of School";
                    }elseif($displayRoleName == "assistant headteacher"){
                        $displayRoleName = "Assistant Head of School";
                    }
                } 
              }             

          @endphp

          <div class="dropdown-title">Welcome {{ $firstPart .' '.$thirdPart}} ({{ $displayRoleName }})</div>
          <a href="{{route('profile.show')}}" class="dropdown-item has-icon"> <i class=" fas fa-cogs"></i> Setup</a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
          <a href="#" class="dropdown-item has-icon text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <div class="preview">
              <i class="material-icons">power_settings_new</i> <span class="icon-name">Logout</span>
            </div>
          </a>
        </div>
      </li>
    </ul>
  </nav>
