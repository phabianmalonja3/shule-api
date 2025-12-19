<x-layout>
    <x-slot:title>
        Admin Panel Page
    </x-slot:title>
    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content">
        <section class="section">
            <div class="row ">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6 ">
                                        <div class="card-content">
                                            <h5 class="font-15">Applications</h5>
                                            <h2 class="mb-3 font-18">{{$applicationCount}}</h2>
                                        </div>
                                    </div>
                                    <div class="pl-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="banner-img">
                                            <img src="assets/img/banner/1.png" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 " >
                    <div class="card" style="padding-top: 50px;">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="card-content">
                                            <h5 class="font-15">Schools</h5>
                                            <h2 class="mb-3 font-18">{{$schoolCount}}</h2>
                                        </div>
                                    </div>
                                    <div class="pl-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="banner-img">
                                            <img src="{{ asset('assets/img/banner/school-sT23_cxW.png') }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 ">
                    <div class="card" style="padding-top: 35px;">
                        <div class="card-statistic-4" >
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="card-content">
                                            <h5 class="font-15">Users</h5>
                                            <h2 class="mb-3 font-18">{{$userCount}}</h2>
                                        </div>
                                    </div>
                                    <div class="pl-0 col-lg-6 col-md-6 col-sm-6 col-xs-6 ">
                                        <div class="banner-img">
                                            <img src="{{ asset('assets/img/banner/bg-gr-DIUE5V0z.png') }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="pt-3 pr-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="card-content">
                                            <h5 class="font-15">Announcement</h5>
                                            <h2 class="mb-3 font-18">0</h2>
                                        </div>
                                    </div>
                                    <div class="pl-0 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="banner-img">
                                            <img src="assets/img/banner/4.png" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
            <div class="clearfix row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-6">
                  <div class="card">
                    <div class="card-header">
                      <h4>Bar chart of Applications , Schools & Parents</h4>
                    </div>
                    <div class="card-body">
                      <div class="recent-report__chart">
                        <div id="chart1"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-6">
                  <div class="card">
                    <div class="card-header">
                      <h4>Subscriptions</h4>
                    </div>
                    <div class="card-body">
                      <div class="recent-report__chart">
                        <div id="chart2"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
        </section>

    </div>

   
</x-layout>
<script>
   $(document).ready(function () {
//   function chart1() {
    var options = {
        chart: {
            height: 350,
            type: 'bar',
        },
        plotOptions: {
            bar: {
                horizontal: false,
                endingShape: 'rounded',
                columnWidth: '55%',
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        series: [{
            name: 'Applications',
            data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
        }, {
            name: 'Schools',
            data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
        }, {
            name: 'Users',
            data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
        }],
        xaxis: {
            categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
            labels: {
                style: {
                    colors: '#9aa0ac',
                }
            }
        },
        yaxis: {
            title: {
                text: 'Numbers'
            },
            labels: {
                style: {
                    color: '#9aa0ac',
                }
            }
        },
        fill: {
            opacity: 1

        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return  val 
                }
            }
        }
    }

    var chart = new ApexCharts(
        document.querySelector("#chart1"),
        options
    );

    chart.render();



var options = {
    chart: {
        height: 350,
        type: 'bar',
    },
    plotOptions: {
        bar: {
            dataLabels: {
                position: 'top', // top, center, bottom
            },
        }
    },
    dataLabels: {
        enabled: true,
        formatter: function (val) {
            return val + "%";
        },
        offsetY: -20,
        style: {
            fontSize: '12px',
            colors: ["#9aa0ac"]
        }
    },
    series: [{
        name: 'Inflation',
        data: [2.3, 3.1, 4.0, 10.1, 4.0, 3.6, 3.2, 2.3, 1.4, 0.8, 0.5, 0.2]
    }],
    xaxis: {
        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        position: 'top',
        labels: {
            offsetY: -18,
            style: {
                colors: '#9aa0ac',
            }
        },
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: false
        },
        crosshairs: {
            fill: {
                type: 'gradient',
                gradient: {
                    colorFrom: '#D8E3F0',
                    colorTo: '#BED1E6',
                    stops: [0, 100],
                    opacityFrom: 0.4,
                    opacityTo: 0.5,
                }
            }
        },
        tooltip: {
            enabled: true,
            offsetY: -35,

        }
    },
    fill: {
        gradient: {
            shade: 'light',
            type: "horizontal",
            shadeIntensity: 0.25,
            gradientToColors: undefined,
            inverseColors: true,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [50, 0, 100, 100]
        },
    },
    yaxis: {
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: false,
        },
        labels: {
            show: false,
            formatter: function (val) {
                return val + "%";
            }
        }

    },
    title: {
        text: 'Monthly Inflation in Argentina, 2002',
        floating: true,
        offsetY: 320,
        align: 'center',
        style: {
            color: '#9aa0ac'
        }
    },
}

var chart = new ApexCharts(
    document.querySelector("#chart2"),
    options
);

chart.render();


   });
</script>