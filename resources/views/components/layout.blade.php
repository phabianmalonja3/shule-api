<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Welcome To ShuleMIS' }} </title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/bundles/prism/prism.css') }}">
  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/bundles/fullcalendar/fullcalendar.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/bundles/izitoast/css/iziToast.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/bundles/pretty-checkbox/pretty-checkbox.min.css') }}">

      
 
 
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" /> 
 <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>  --}}



  <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}"> 
  
 <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">



 
<style>
  .animated-bg {
    /* Set your background image, adjust the URL as needed */
    background: url("{{ asset('background/OQ3OWI0.jpg') }}") 
    no-repeat center center fixed;
    background-size: cover;
    
    /* Full viewport height */
    min-height: 100vh;
    
    /* Optional: add a slight overlay to help the content stand out */
    position: relative;
    z-index: 1;
}

/* Optional overlay effect */
.animated-bg::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
   
    /* Apply blur to the background behind the overlay */
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: -1;

    /* Animate the overlay's opacity if desired */
    animation: overlayFade 10s infinite alternate;
}

@keyframes overlayFade {
    0% { opacity: 0.3; }
    100% { opacity: 0.6; }
}

/* Example background position animation */
@keyframes bgMove {
    0% {
        background-position: center top;
    }
    100% {
        background-position: center bottom;
    }
}

.animated-bg {
    animation: bgMove 20s ease-in-out infinite alternate;

    
}
@media (max-width: 768px) {
    .animated-bg {
      /* On smaller screens, using scroll attachment improves performance */
      background-attachment: scroll;
    }
  }
</style>

  <link rel='shortcut icon' type='image/x-icon' href='{{ asset('favicon.png') }}' />
  @livewireStyles

</head>

<body>
  <div id="preloader">
    <div class="lds-ellipsis">
      <div></div>
      <div></div>
      <div></div>
      <div></div>
    </div>
  </div>
  
  <div id="app" class="" >

        {{$slot}}
    </div>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
  <!-- JS Libraies -->
  <script src="{{ asset('assets/bundles/prism/prism.js') }}"></script>
  <!-- Page Specific JS File -->
  <!-- Template JS File -->
  <script src="{{ asset('assets/js/scripts.js') }}"></script>

  <script src="{{ asset('assets/bundles/apexcharts/apexcharts.min.js') }}"></script>
  <!-- Page Specific JS File -->
  {{-- <script src="{{ asset('assets/js/page/chart-apexcharts.js') }}"></script> --}}
  
  <script src="{{ asset('assets/js/custom.js') }}"></script>
  <script src="{{ asset('assets/bundles/sweetalert/sweetalert.min.js') }}"></script>
  <!-- Page Specific JS File -->
  <script src="{{ asset('assets/js/page/sweetalert.js') }}"></script>
  <script src="{{ asset('assets/bundles/datatables/datatables.min.js') }}"></script>
  <script src="{{ asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
  {{-- <script src="{{ asset('assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script> --}}
  <!-- Page Specific JS File -->

  <script src="{{ asset('assets/bundles/fullcalendar/fullcalendar.min.js') }}"></script>
  <!-- Page Specific JS File -->
  <script src="{{ asset('assets/js/page/calendar.js') }}"></script>
 
  {{-- <script src="{{ asset('assets/js/page/chart-amchart.js') }}"></script> --}}
  <!-- Template JS File -->
  <script>
    const preloader = document.querySelector('#preloader');
    if (preloader) {
        window.addEventListener('load', () => {
            $("#preloader").fadeOut("slow")
        });
    }
</script>

@livewireScripts
</body>

</html>
   