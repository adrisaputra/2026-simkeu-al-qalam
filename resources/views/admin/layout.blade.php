@php
$setting = \App\Helpers\Helpers::setting();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $setting->application_name }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/upload/setting/'.$setting->small_icon) }}" />
    <link href="{{ asset('backend/assets/css/loader.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('backend/assets/js/loader.js') }}"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap" rel="stylesheet">
    <link href="{{ asset('backend/bootstrap/css/bootstrap.css') }}" rel="stylesheet" type="text/css" />

    <!-- Load Bootstrap CSS files -->
    <link id="bootstrap-css" href="{{ session('monochrome') ? asset('backend/assets/css/plugins2.css') : asset('backend/assets/css/plugins.css') }}" rel="stylesheet">

    <!-- <link href="{{ asset('backend/assets/css/plugins2.css') }}" rel="stylesheet" type="text/css" /> -->
    <link href="{{ asset('backend/plugins/flatpickr/flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/plugins/noUiSlider/nouislider.min.css') }}" rel="stylesheet" type="text/css">
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link href="{{ asset('backend/plugins/apex/apexcharts.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/dashboard/dash_1.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->


    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{ asset('backend/assets/css/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/plugins/animate/animate.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('backend/plugins/sweetalerts/promise-polyfill.js') }}"></script>
    <link href="{{ asset('backend/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/elements/alert.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/plugins/select2/select2.min.css') }}">
    <link href="{{ asset('backend/plugins/file-upload/file-upload-with-preview.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/plugins/flatpickr/custom-flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/plugins/noUiSlider/custom-nouiSlider.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/plugins/bootstrap-range-Slider/bootstrap-slider.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/plugins/notification/snackbar/snackbar.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/forms/switches.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/fullcalendar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/forms/theme-checkbox-radio.css') }}">
    <link href="{{ asset('backend/assets/css/components/tabs-accordian/custom-tabs.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

    <!-- CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> -->

    <!-- Script -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>

<body class="sidebar-noneoverflow">
    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>
    <!--  END LOADER -->

    <!--  BEGIN NAVBAR  -->
    <div class="header-container fixed-top">
        <header class="header navbar navbar-expand-sm">

            <ul class="navbar-nav theme-brand flex-row ">
                <li class="nav-item theme-logo">
                    <a href="{{ url('/') }}" target="_blank">
                        <img src="{{ asset('storage/upload/setting/'.$setting->large_icon) }}" style="width:100%">
                        <!-- <p style="font-size:18px;font-weight:bold;color:white;margin-top: 10px;">{{ $setting->short_application_name }}</p> -->
                    </a>
                </li>
                <li class="nav-item toggle-sidebar">
                    <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list">
                            <line x1="8" y1="6" x2="21" y2="6"></line>
                            <line x1="8" y1="12" x2="21" y2="12"></line>
                            <line x1="8" y1="18" x2="21" y2="18"></line>
                            <line x1="3" y1="6" x2="3" y2="6"></line>
                            <line x1="3" y1="12" x2="3" y2="12"></line>
                            <line x1="3" y1="18" x2="3" y2="18"></line>
                        </svg></a>
                </li>
            </ul>

            <ul class="navbar-item flex-row search-ul" style="padding-top:10px">
            </ul>

            <ul class="navbar-item flex-row navbar-dropdown">

                <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                    </a>
                    <div class="dropdown-menu position-absolute animated fadeInUp" aria-labelledby="userProfileDropdown">
                        <div class="user-profile-section">
                            <div class="media mx-auto">
                                @if (Auth::user()->photo)
                                <img src="{{ asset('storage/upload/photo/' . Auth::user()->photo) }}" class="img-fluid mr-2" alt="avatar">
                                @else
                                <img src="{{ asset('storage/profile-1-20210205190338.jpg') }}" class="img-fluid mr-2" alt="avatar">
                                @endif

                                <div class="media-body">
                                    <h5>{{ Auth::user()->name }}</h5>
                                    <p>{{ Auth::user()->group->name }}</p>
                                </div>
                            </div>
                        </div>
                        @if(Auth::user()->group->name == 'Admin KPI')
                        <div class="dropdown-item">
                            <a href="{{ url('setting') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg> <span>Pengaturan</span>
                            </a>
                        </div>
                        @endif
                        <div class="dropdown-item">
                            <a href="{{ url('edit_profil/'.Crypt::encrypt(Auth::user()->id)) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg> <span>Profil Saya</span>
                            </a>
                        </div>
                        <div class="dropdown-item">
                            <a href="{{ url('logout') }}" onclick="event.preventDefault();
									document.getElementById('logout-form').submit();">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg> <span>Log Out</span>
                            </a>
                            <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN SIDEBAR  -->
        <div class="sidebar-wrapper sidebar-theme">

            <nav id="sidebar">
                <div class="profile-info">
                    <figure class="user-cover-image"></figure>
                    <div class="user-info">
                        @if (Auth::user()->photo)
                        <img src="{{ asset('storage/upload/photo/' . Auth::user()->photo) }}" alt="avatar">
                        @else
                        <img src="{{ asset('storage/profile-1-20210205190338.jpg') }}" alt="avatar">
                        @endif
                        <h6 class="">{{ Auth::user()->name }}</h6>
                        <p class="">{{ Auth::user()->group->name }}</p>
                    </div>
                </div>
                <div class="shadow-bottom"></div>
                <ul class="list-unstyled menu-categories" id="accordionExample">

                    <li class="menu @if(Request::segment(1)==" dashboard") active @endif">
                        <a href="{{ url('dashboard') }}" @if(Request::segment(1)=="dashboard" ) aria-expanded="true" @endif class="dropdown-toggle">
                            <div class="">
                                <img src="{{ asset('storage/menu/icons8-dashboard-layout-100.png') }}" width="30" height="30" style="margin-right: 18px">
                                <span>Dashboard</span>
                            </div>
                        </a>
                    </li>

                    @if(in_array(Auth::user()->group->name, ['Admin Finance']))
                        
                        <li class="menu @if(Request::segment(1)==" employee_kpi") active @endif">
                            <a href="{{ url('employee_kpi') }}" @if(in_array(Request::segment(1), ['employee_kpi','employee_kpi_detail','employee_kpi_period','employee_kpi_indicator_item'])) aria-expanded="true" @endif class="dropdown-toggle">
                                <div class="">
                                    <img src="{{ asset('storage/menu/icons8-profile-100.png') }}" width="30" height="30" style="margin-right: 18px">
                                    <span>KPI</span>
                                </div>
                            </a>
                        </li>

                        <li class="menu @if(Request::segment(1)==" employee_report") active @endif">
                            <a href="{{ url('employee_report') }}" @if(in_array(Request::segment(1), ['employee_report','employee_report_category','employee_report_period','employee_report_value'])) aria-expanded="true" @endif class="dropdown-toggle">
                                <div class="">
                                    <img src="{{ asset('storage/menu/icons8-profile-100.png') }}" width="30" height="30" style="margin-right: 18px">
                                    <span>Rapor</span>
                                </div>
                            </a>
                        </li>

                        @if(Auth::user()->group->name == 'Admin Finance')
                        
                        <li class="menu menu-heading">
                            <div class="heading"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-minus">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg><span>PENGATURAN</span></div>
                        </li>

                        <li class="menu @if(Request::segment(1)==" kpi_category") active @endif">
                            <a href="{{ url('kpi_category') }}" @if(in_array(Request::segment(1), ['kpi_category','kpi','kpi_indicator','kpi_indicator_item','kpi_bonus'])) aria-expanded="true" @endif class="dropdown-toggle">
                                <div class="">
                                    <img src="{{ asset('storage/menu/icons8-menu-100.png') }}" width="30" height="30" style="margin-right: 18px">
                                    <span>Jenjang</span>
                                </div>
                            </a>
                        </li>

                        <li class="menu @if(Request::segment(1)==" report_category") active @endif">
                            <a href="{{ url('report_category') }}" @if(in_array(Request::segment(1), ['report_category','report'])) aria-expanded="true" @endif class="dropdown-toggle">
                                <div class="">
                                    <img src="{{ asset('storage/menu/icons8-menu-100.png') }}" width="30" height="30" style="margin-right: 18px">
                                    <span>Kategori Uraian</span>
                                </div>
                            </a>
                        </li>

                        <li class="menu @if(Request::segment(1)==" report_category") active @endif">
                            <a href="{{ url('report_category') }}" @if(in_array(Request::segment(1), ['report_category','report'])) aria-expanded="true" @endif class="dropdown-toggle">
                                <div class="">
                                    <img src="{{ asset('storage/menu/icons8-menu-100.png') }}" width="30" height="30" style="margin-right: 18px">
                                    <span>Kode Transaksi</span>
                                </div>
                            </a>
                        </li>

                        <li class="menu @if(Request::segment(1)==" log") active @endif">
                            <a href="{{ url('log') }}" @if(Request::segment(1)=="log" ) aria-expanded="true" @endif class="dropdown-toggle">
                                <div class="">
                                    <img src="{{ asset('storage/menu/icons8-timer-100.png') }}" width="30" height="30" style="margin-right: 18px">
                                    <span>Log Aktifitas</span>
                                </div>
                            </a>
                        </li>

                        <li class="menu @if(Request::segment(1)==" user") active @endif">
                            <a href="{{ url('user') }}" @if(Request::segment(1)=="user" ) aria-expanded="true" @endif class="dropdown-toggle">
                                <div class="">
                                    <img src="{{ asset('storage/menu/icons8-customer-100.png') }}" width="30" height="30" style="margin-right: 18px">
                                    <span>User</span>
                                </div>
                            </a>
                        </li>
                        @endif

                    @endif
                </ul>
            </nav>
        </div>
        <!--  END SIDEBAR  -->

        @yield('content')

    </div>
    <!-- END MAIN CONTAINER -->

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <!-- <script src="{{ asset('backend/assets/js/libs/jquery-3.1.1.min.js') }}"></script> -->
    <script src="{{ asset('backend/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('backend/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/app.js') }}"></script>
    <script>
        $(document).ready(function() {
            App.init();
        });
    </script>
    {{--<script src="{{ asset('backend/js/custom.js') }}"></script>--}}

    <!-- BEGIN THEME GLOBAL STYLE -->
    <script src="{{ asset('backend/assets/js/scrollspyNav.js') }}"></script>
    <script src="{{ asset('backend/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/sweetalerts/custom-sweetalert.js') }}"></script>
    <script src="{{ asset('backend/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/select2/custom-select2.js') }}"></script>
    <script src="{{ asset('backend/plugins/file-upload/file-upload-with-preview.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('backend/plugins/noUiSlider/nouislider.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/flatpickr/custom-flatpickr.js') }}"></script>
    <script src="{{ asset('backend/plugins/noUiSlider/custom-nouiSlider.js') }}"></script>
    <script src="{{ asset('backend/plugins/bootstrap-range-Slider/bootstrap-rangeSlider.js') }}"></script>
    <script src="{{ asset('backend/plugins/notification/snackbar/snackbar.min.js') }}"></script>

    <!-- END PAGE LEVEL PLUGINS -->

    <!--  BEGIN CUSTOM SCRIPTS FILE  -->
    <!-- END THEME GLOBAL STYLE -->
    <script>
        //First upload
        var firstUpload = new FileUploadWithPreview('myFirstImage')
        //Second upload
        var secondUpload = new FileUploadWithPreview('mySecondImage')

        function formatRupiah(objek, separator) {
            a = objek.value;
            b = a.replace(/[^\d]/g, "");
            c = "";
            panjang = b.length;
            j = 0;
            for (i = panjang; i > 0; i--) {
                j = j + 1;
                if (((j % 3) == 1) && (j != 1)) {
                    c = b.substr(i - 1, 1) + separator + c;
                } else {
                    c = b.substr(i - 1, 1) + c;
                }
            }
            objek.value = c;
        }

        // Get the Toast button
        var toastButton = document.getElementById("toast-btn");
        // Get the Toast element
        var toastElement = document.getElementsByClassName("toast")[0];

        toastButton.onclick = function() {
            $('.toast').toast('show');
        }
    </script>
    <!-- END PAGE LEVEL PLUGINS -->
</body>

</html>