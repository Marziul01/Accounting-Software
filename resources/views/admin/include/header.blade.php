<nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme contact-card"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="bx bx-menu bx-md"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center">
                {{-- <i class="bx bx-search bx-md"></i>
                <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2" placeholder="Search..."
                    aria-label="Search..." /> --}}
            </div>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Place this tag where you want the button to render. -->
            <li class="nav-item me-2">
                <div class="clock"> <i class="fa-regular fa-clock"></i> <span id="clock">Loading...</span></div>
            </li>
            <li class="nav-item me-2">
                <div style="position: relative;">
                    <i id="notificationIcon" class="fa-solid fa-bell btn btn-sm btn-outline-secondary themecolor"></i>
                    <span id="notificationCount" class="notification-badge"></span>

                    <div id="notificationDropdown" class="notification-dropdown bg-menu-theme"></div>
                </div>
            </li>
            <li class="nav-item lh-1 me-2">
                <button id="themeToggle" class="btn btn-sm btn-outline-secondary themecolor">🌙<span>Dark Mode</span></button>
            </li>

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ asset($setting->site_logo) }}" alt
                            class="w-px-40 h-auto rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end bg-menu-theme">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset($setting->site_logo) }}" alt
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                    <small class="text-muted">{{ Auth::user()->id == 1 ? 'Admin' : 'SubAdmin' }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile') }}">
                            <i class="bx bx-user bx-md me-3"></i><span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('site.settings') }}"> <i
                                class="bx bx-cog bx-md me-3"></i><span>Settings</span> </a>
                    </li>
                    {{-- <li>
                        <button id="toggleLang" class="dropdown-item" type="button"><i class="fa-solid fa-globe me-3"></i><span>বাংলা</span></button>
                    </li> --}}
                    
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <a class="dropdown-item logout-confirm" href="{{ route('admin.logout') }}">
                            <i class="bx bx-power-off bx-md me-3"></i><span>Log Out</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>

<div id="notificationModal" style="display: none; position: fixed; top: 0px; left: 0px; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.35); justify-content: center; align-items: center; z-index: 99999;">
    <div class="bg-menu-theme" style="padding:20px; border-radius:8px; width:400px; position:relative;">
        <div id="notificationModalContent"></div>
        <button id="closeModalBtn" class="notification-modal-close" style="position:absolute; top:20px; right:10px;">X</button>
    </div>
</div>



