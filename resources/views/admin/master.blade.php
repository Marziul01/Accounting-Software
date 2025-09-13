<!doctype html>

<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('admin-assets') }}/assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title> {{ $setting->site_name }} | Admin Dashboard </title>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset($setting->site_favicon) }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('admin-assets') }}/assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('admin-assets') }}/assets/vendor/css/core.css"
        class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('admin-assets') }}/assets/vendor/css/theme-default.css"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('admin-assets') }}/assets/css/demo.css" />
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla&display=swap" rel="stylesheet">


    <!-- Vendors CSS -->
    <link rel="stylesheet"
        href="{{ asset('admin-assets') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ asset('admin-assets') }}/assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset('admin-assets') }}/assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('admin-assets') }}/assets/js/config.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


    <link rel="stylesheet" href="{{ asset('admin-assets') }}/css/style.css" />
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('admin.include.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                @include('admin.include.header')

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    @yield('content')
                    <!-- / Content -->

                    <!-- Footer -->

                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="{{ asset('admin-assets') }}/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="{{ asset('admin-assets') }}/assets/vendor/libs/popper/popper.js"></script>
    <script src="{{ asset('admin-assets') }}/assets/vendor/js/bootstrap.js"></script>
    <script src="{{ asset('admin-assets') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{{ asset('admin-assets') }}/assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('admin-assets') }}/assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('admin-assets') }}/assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="{{ asset('admin-assets') }}/assets/js/dashboards-analytics.js"></script>

    <!-- Place this tag before closing body tag for github widget button. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Buttons extension -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.querySelectorAll('.myDate').forEach(function(el) {
            flatpickr(el, {
                dateFormat: "Y-m-d",
                defaultDate: el.value || "today"
                disableMobile: true
            });
        });
    </script>

    <script>
        const toggleBtn = document.getElementById('themeToggle');
        const root = document.documentElement;

        // Load theme from localStorage
        if (localStorage.getItem('theme') === 'dark') {
            root.setAttribute('data-theme', 'dark');
            toggleBtn.innerHTML = '☀️<span>Light Mode</span>';
        }

        toggleBtn.addEventListener('click', () => {
            if (root.getAttribute('data-theme') === 'dark') {
                root.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                toggleBtn.innerHTML = '🌙<span>Dark Mode</span>';
            } else {
                root.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                toggleBtn.innerHTML = '☀️<span>Light Mode</span> ';
            }
        });
    </script>

    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if (session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif

        @if (session('info'))
            toastr.info("{{ session('info') }}");
        @endif
    </script>



    <script>
        $(document).on('click', '.logout-confirm', function(e) {
            e.preventDefault();

            const logoutUrl = $(this).attr('href');

            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out from your account.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, log me out'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = logoutUrl;
                }
            });
        });
    </script>
    <script>
        const timeApiUrl = "{{ route('get.time') }}"; // Laravel route URL

        async function fetchDhakaTime() {
            const response = await fetch(timeApiUrl);
            const data = await response.json();
            return new Date(data.time.replace(' ', 'T'));
        }

        function format12Hour(date) {
            let hours = date.getHours();
            const minutes = date.getMinutes();
            const seconds = date.getSeconds();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            return `${pad(hours)}:${pad(minutes)}:${pad(seconds)} ${ampm}`;
        }

        function pad(num) {
            return num < 10 ? '0' + num : num;
        }

        async function startClock() {
            let serverTime = await fetchDhakaTime();
            setInterval(() => {
                serverTime.setSeconds(serverTime.getSeconds() + 1);
                document.getElementById('clock').textContent = format12Hour(serverTime);
            }, 1000);
        }

        startClock();
    </script>

    <script>
    const notificationIcon = document.getElementById('notificationIcon');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationCount = document.getElementById('notificationCount');

    function timeAgo(dateString) {
        const now = new Date();
        const past = new Date(dateString.replace(' ', 'T')); // Fix for "Y-m-d H:i:s"
        const diffInSeconds = Math.floor((now - past) / 1000);

        if (diffInSeconds < 60) return `${diffInSeconds}s`;
        const diffInMinutes = Math.floor(diffInSeconds / 60);
        if (diffInMinutes < 60) return `${diffInMinutes}m`;
        const diffInHours = Math.floor(diffInMinutes / 60);
        if (diffInHours < 24) return `${diffInHours}h`;
        const diffInDays = Math.floor(diffInHours / 24);
        if (diffInDays < 30) return `${diffInDays}d`;
        const diffInMonths = Math.floor(diffInDays / 30);
        if (diffInMonths < 12) return `${diffInMonths}mo`;
        const diffInYears = Math.floor(diffInMonths / 12);
        return `${diffInYears}y`;
    }

    function truncateMessage(message, wordLimit = 15) {
        const words = message.split(' ');
        if (words.length > wordLimit) {
            return words.slice(0, wordLimit).join(' ') + '...';
        }
        return message;
    }

    async function loadNotifications() {
        const response = await fetch("{{ route('notifications.unread') }}");
        const data = await response.json();
        const notifications = data.notifications;
        const count = data.count;

        // Update count badge
        if (count > 0) {
            notificationCount.style.display = 'inline';
            notificationCount.textContent = count > 99 ? '99+' : count;
        } else {
            notificationCount.style.display = 'none';
        }

        // Render notifications
        if (notifications.length > 0) {
            // Map all notifications into HTML
            const notificationHTML = notifications.map(n => `
                <div class="notification-item" onclick="openNotification(${n.id})" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #eee; cursor: pointer;">
                    <div class="notification-icons" style="flex-shrink: 0; margin-right: 8px; position: relative;">
                        ${n.email_sent ? '<i class="fa-solid fa-envelope"></i>' : ''}
                        ${n.sms_sent ? '<i class="fa-solid fa-comment-sms"></i>' : ''}
                    </div>
                    <div class="notification-occasion" style="flex: 1; overflow: hidden;">
                        ${n.occasion_name ? `<p class="occasion" style="margin:0; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 10px; text-transform: capitalize;">${n.occasion_name}</p>` : ''}
                        <div class="message" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 12px; text-transform: capitalize;">
                            ${truncateMessage(n.message)}
                        </div>
                    </div>
                    <div class="notification-time" style="flex-shrink: 0; margin-left: 10px; font-size: 9px; color: #777;">
                        <span>${timeAgo(n.sent_date)}</span>
                    </div>
                </div>
            `).join('');

            // Append the link **once after all notifications**
            notificationDropdown.innerHTML = notificationHTML + `
                <div style="padding: 10px; text-align: center; border-top: 1px solid #eee;">
                    <a class="preborti" href="{{ route('notifications.all') }}">See Previous Notifications</a>
                </div>
            `;
        }else {
            notificationDropdown.innerHTML = `
                <div style="padding: 10px; text-align:center; color:#777;">
                    No notifications
                </div>
                <div>
                    <a class="preborti" href="{{ route('notifications.all') }}"> <i class="fa-solid fa-bell"></i> See Previous Notifications </a>
                </div>
            `;
        }
    }

    // Call immediately when page loads
    loadNotifications();

    notificationIcon.addEventListener('click', async (event) => {
        event.stopPropagation(); // Prevent closing immediately
        const isOpen = notificationDropdown.style.display === 'block';

        // Toggle visibility
        if (!isOpen) {
            await loadNotifications();
            notificationDropdown.style.display = 'block';

            // Mark all as read
            fetch("{{ route('notifications.markRead') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            notificationCount.style.display = 'none';
        } else {
            notificationDropdown.style.display = 'none';
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!notificationDropdown.contains(e.target) && e.target !== notificationIcon) {
            notificationDropdown.style.display = 'none';
        }
    });

    const notificationModal = document.getElementById('notificationModal');
    const closeModalBtn = document.getElementById('closeModalBtn');

    // Show modal when a notification is clicked
    async function openNotification(id) {
    const url = `{{ route('notifications.show', ':id') }}`.replace(':id', id);
    const response = await fetch(url);
    const data = await response.json();
    const n = data.notification;

    document.getElementById('notificationModalContent').innerHTML = `
    <div class="notification-detail">
        <h4 style="margin-bottom: 15px; font-size: 20px; text-align: center; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
            Notification Details
        </h4>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <small style="font-size: 14px; text-transform: capitalize;">
                ${n.occasion_name || ''}
            </small>
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="notimodals" style="display: flex; gap: 8px; font-size: 18px;">
                    ${n.email_sent ? '<i class="fa-solid fa-envelope"></i>' : ''}
                    ${n.sms_sent ? '<i class="fa-solid fa-comment-sms"></i>' : ''}
                </div>
                <div style="text-align: right; font-size: 13px;">
                    ${data.timeAgo}
                </div>
            </div>
        </div>

        ${data.contactName ? `
            <div style="font-size: 14px; margin-top: 15px;">
                <strong>Sent To:</strong> ${data.contactName}
            </div>
        ` : ''}

        <div style="border-radius: 8px; font-size: 15px; margin-top: 15px; margin-bottom: 5px;">
            <strong>Message:</strong> <br>
            ${n.message}
        </div>

    </div>
`;


    notificationModal.style.display = 'flex'; // Show modal
}


    // Close modal on button click
    closeModalBtn.addEventListener('click', () => {
        notificationModal.style.display = 'none';
    });

    // Close modal when clicking outside content
    notificationModal.addEventListener('click', (e) => {
        if (e.target === notificationModal) {
            notificationModal.style.display = 'none';
        }
    });

    // Refresh every 30 sec
    // setInterval(loadNotifications, 30000);
</script>



    @yield('scripts')
    <script src="{{ asset('admin-assets') }}/assets/js/tranalte.js"></script>
</body>

</html>
