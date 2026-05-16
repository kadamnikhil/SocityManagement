<!DOCTYPE html>
<html lang="en">
    <head>
        @php
            $host = request()->getHost();
        @endphp

        @if(Str::contains($host, 'technicul.com'))
            <meta name="robots" content="noindex, nofollow">
            <meta name="Googlebot" content="noindex, nofollow">
        @endif
        <!-- TITLE -->
        <title>@yield('title')</title>

        <!-- META -->
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="handheldfriendly" content="true" />
        <meta name="MobileOptimized" content="width" />
        <meta name="description" content="Mordenize" />
        <meta name="author" content="" />
        <meta name="keywords" content="Mordenize" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- FAVICON -->
        <!-- <link rel="shortcut icon" type="image/png" href="/favicon.png" /> -->

        <!-- BASIC CSS -->
        <link  id="themeColors"  rel="stylesheet" href="/backend/dist/css/style.min.css" />

        <!-- TECHNICUL CSS -->
        <link  id="themeColors"  rel="stylesheet" href="/backend/dist/css/techincul.css" />

        <!-- CAROUSEL -->
        <link rel="stylesheet" href="/backend/dist/libs/owl.carousel/dist/assets/owl.carousel.min.css">

        <!-- MAIN JQUERY FILE -->
        <script src="/backend/dist/libs/jquery/dist/jquery.min.js"></script>

        <!-- DATATABLE -->
        <link rel="stylesheet" href="/backend/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
        <script src="/backend/dist/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
        <script src="/backend/dist/js/datatable/datatable-advanced.init.js"></script>

        <!-- SELECT2 -->
        <link rel="stylesheet" href="/backend/dist/libs/select2/dist/css/select2.min.css">

        <!-- SWITCH -->
        <link rel="stylesheet" href="/backend/dist/libs/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css">
    </head>
    <body>
        <div class="page-wrapper" id="main-wrapper" data-theme="blue_theme"  data-layout="vertical" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
            <aside class="left-sidebar">
                <div>
                    <div class="brand-logo d-flex align-items-center justify-content-between text-center">
                        <a href="/" class="text-nowrap logo-img ">
							<img src="/logo.png" width="250" style="margin-bottom: -80px;" alt="" style=""/>
                        </a>
                        <div class="close-btn d-lg-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                            <i class="ti ti-x fs-8 text-muted"></i>
                        </div>
                    </div>
                    @include('layouts.admin.navbar')
                    @auth
                    @php
                        $__u = Auth::user();
                        $__roles = $__u->getRoleNames()->take(2)->implode(', ');
                        if ($__u->getRoleNames()->count() > 2) {
                            $__roles .= '…';
                        }
                    @endphp
                    <div class="fixed-profile p-3 bg-light-secondary rounded sidebar-ad mt-3">
                        <a href="{{ route('admin.profile.show') }}" class="text-decoration-none text-body hstack gap-3">
                            <div class="john-img flex-shrink-0">
                                <img src="/backend/dist/images/profile/user-1.jpg" class="rounded-circle" width="40" height="40" alt="">
                            </div>
                            <div class="john-title text-start min-w-0">
                                <h6 class="mb-0 fs-4 fw-semibold text-truncate">{{ $__u->first_name }} {{ $__u->last_name }}</h6>
                                <span class="fs-2 text-muted text-truncate d-block">{{ $__roles ?: 'Admin' }}</span>
                            </div>
                        </a>
                        <div class="d-flex justify-content-end mt-2">
                            <button class="border-0 bg-transparent text-primary" type="button" aria-label="Log out" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Log out" onclick="document.getElementById('sidebar-logout-form').submit();">
                                <i class="ti ti-power fs-6"></i>
                            </button>
                            <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>
            </aside>
            <div class="body-wrapper">
                <header class="app-header">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        {{-- <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link sidebartoggler nav-icon-hover ms-n3" id="headerCollapse" href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                                </a>
                            </li>
                            <li class="nav-item d-none d-lg-block">
                                <a class="nav-link nav-icon-hover" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                <i class="ti ti-search"></i>
                                </a>
                            </li>
                        </ul> --}}
                        
                        <div class="d-block d-lg-none">
							<img src="/logo.png" width="180" alt="" style=""/>
                        </div>
                        <button class="navbar-toggler p-0 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
							<span class="p-2">
								<i class="ti ti-dots fs-7"></i>
							</span>
                        </button>
                        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                            <div class="d-flex align-items-center justify-content-between">
                                <a href="javascript:void(0)" class="nav-link d-flex d-lg-none align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar" aria-controls="offcanvasWithBothOptions">
                                	<i class="ti ti-align-justified fs-7"></i>
                                </a>
                                <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                                    <li class="nav-item dropdown">
                                        <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
                                            <div class="d-flex align-items-center">
                                                <div class="user-profile-img">
                                                    <img src="/backend/dist/images/profile/user-1.jpg" class="rounded-circle" width="35" height="35" alt="" />
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop1">
                                            <div class="profile-dropdown position-relative" data-simplebar>
                                                <div class="py-3 px-7 pb-0">
                                                    <h5 class="mb-0 fs-5 fw-semibold">User Profile</h5>
                                                </div>
                                                <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                                                    <img src="/backend/dist/images/profile/user-1.jpg" class="rounded-circle" width="80" height="80" alt="" />
                                                    <div class="ms-3">
                                                        <h5 class="mb-1 fs-3">
                                                            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                                                        </h5>
                                                        <span class="mb-1 d-block text-dark">
                                                            {{ Auth::user()->getRoleNames()->implode(', ') ?: '—' }}
                                                        </span>
                                                        <p class="mb-0 d-flex text-dark align-items-center gap-2">
                                                            <i class="ti ti-mail fs-4"></i> {{ Auth::user()->email }}
                                                        </p>
                                                        <p class="mb-0 d-flex text-dark align-items-center gap-2">
                                                            <i class="ti ti-phone fs-4"></i> {{ Auth::user()->mobile }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="d-grid gap-2 py-4 px-7 pt-8">
                                                    <a href="{{ route('admin.profile.show') }}" class="btn btn-primary">My profile</a>
                                                    <a href="#" class="btn btn-outline-primary" onclick="$('#logout-form').submit();">Log Out</a>
                                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                        @csrf
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </header>
                <div class="container-fluid">
                    @yield('content')
                </div>
                <div id="chart" style="display:none;"></div>
                <div id="breakup" style="display:none;"></div>
                <div id="earning" style="display:none;"></div>
                <div id="salary" style="display:none;"></div>
                <div id="customers" style="display:none;"></div>
                <div id="projects" style="display:none;"></div>
                <div id="stats" style="display:none;"></div>
            </div>
        </div>

        @include('layouts.admin.modals')

        <!-- BOOTSTRAP -->
        <script src="/backend/dist/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

        <!-- APP -->
        <script src="/backend/dist/js/app.min.js"></script>
        <script src="/backend/dist/js/app.init.js"></script>
        <script src="/backend/dist/js/app-style-switcher.js"></script>

        <!-- CUSTOM -->
        <script src="/backend/dist/js/custom.js"></script>

        <!-- SIMPLEBAR -->
        <script src="/backend/dist/libs/simplebar/dist/simplebar.min.js"></script>
        
        <!-- SIDEBAR -->
        <script src="/backend/dist/js/sidebarmenu.js"></script>

        <!-- CAROUSEL -->
        <script src="/backend/dist/libs/owl.carousel/dist/owl.carousel.min.js"></script>

        <!-- CHART -->
        <script src="/backend/dist/libs/apexcharts/dist/apexcharts.min.js"></script>

        <!-- DASHBOARD -->
        <script src="/backend/dist/js/dashboard.js"></script>

        <!-- TOASTR -->
        <script src="/backend/dist/js/plugins/toastr-init.js"></script>

        <!-- SELECT2 -->
        <script src="/backend/dist/libs/select2/dist/js/select2.full.min.js"></script>
        <script src="/backend/dist/libs/select2/dist/js/select2.min.js"></script>
        <script src="/backend/dist/js/forms/select2.init.js"></script>

        <!-- SWITCH -->
        <script src="/backend/dist/libs/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>
        <script src="/backend/dist/js/forms/bootstrap-switch.js"></script>
    </body>
</html>