<!doctype html>

<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset( 'admin-assets' )  }}/assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Forget Password!</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset( 'admin-assets' )  }}/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset( 'admin-assets' )  }}/assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset( 'admin-assets' )  }}/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset( 'admin-assets' )  }}/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset( 'admin-assets' )  }}/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset( 'admin-assets' )  }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset( 'admin-assets' )  }}/assets/vendor/css/pages/page-auth.css" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Helpers -->
    <script src="{{ asset( 'admin-assets' )  }}/assets/vendor/js/helpers.js"></script>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset( 'admin-assets' )  }}/assets/js/config.js"></script>
    <link rel="stylesheet" href="{{ asset( 'admin-assets' )  }}/css/style.css" />
</head>

<body>
    <!-- Content -->

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <button id="themeToggle" class="btn btn-sm btn-outline-secondary login-darkMOde">🌙 Dark Mode</button>
                <!-- Register -->
                <div class="card px-sm-6 px-0">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center" style="overflow: visible">
                            <a href="index.html" class="login app-brand-link gap-2">
                                <img src="{{ asset( 'admin-assets' )  }}/img/logo.png" alt="Logo" class="logo" width="75%" />
                            </a>
                        </div>

                        <form id="forgotForm" class="mb-6">
                            @csrf
                            <div id="input-email" class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Enter your email" />
                            </div>

                            <div id="input-phone" class="mb-3" style="display: none;">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="number" class="form-control" name="mobile" placeholder="Enter your phone number" />
                            </div>

                            <div class="mb-3">
                                <button type="button" id="toggleInput" class="btn btn-sm btn-outline-primary">Use Phone Number</button>
                            </div>

                            <div>
                                <button class="btn btn-primary w-100" type="submit">Send Code</button>
                            </div>

                            <div class="text-center mt-3">
                                <a class="text-primary" href="{{ route('login') }}">Back to Login!</a>
                            </div>
                        </form>

                        {{-- Code Verification --}}
                        <form id="verifyCodeForm" style="display: none;">
                            @csrf
                            <div class="mb-3">
                                <label for="code" class="form-label">Enter Verification Code</label>
                                <input type="text" class="form-control" name="code" placeholder="6-digit code" />
                            </div>
                            <button type="submit" class="btn btn-success w-100">Verify Code</button>
                        </form>

                        {{-- New Password --}}
                        <form id="newPasswordForm" style="display: none;">
                            @csrf
                            <div class="mb-3">
                                <label>New Password</label>
                                <input type="password" name="password" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" />
                            </div>
                            <button type="submit" class="btn btn-success w-100">Update Password</button>
                        </form>


                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex justify-content-center flex-column align-items-center auth-success-modal" >
                    <img src="{{ asset('admin-assets/img/double-check.gif') }}" width="25%" alt="">
                    <h5 class="modal-title text-center" id="successModalLabel">Success</h5>
                    <p id="successMessage" class="text-center">Login successful!</p>
                </div>
            </div>
        </div>
    </div>

    <div id="fullscreenLoader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
        <div style="display:flex; justify-content:center; align-items:center; width:100%; height:100%;">
            <div class="loader-custom"></div>
        </div>
    </div>


    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="{{ asset( 'admin-assets' )  }}/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="{{ asset( 'admin-assets' )  }}/assets/vendor/libs/popper/popper.js"></script>
    <script src="{{ asset( 'admin-assets' )  }}/assets/vendor/js/bootstrap.js"></script>
    <script src="{{ asset( 'admin-assets' )  }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{{ asset( 'admin-assets' )  }}/assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="{{ asset( 'admin-assets' )  }}/assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag before closing body tag for github widget button. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>


    <script>
    let usingEmail = true;

    $('#toggleInput').on('click', function () {
        usingEmail = !usingEmail;

        if (usingEmail) {
            $('#input-email').show();
            $('#input-phone').hide();
            $('input[name=phone]').val('');
            $(this).text('Use Phone Number');
        } else {
            $('#input-email').hide();
            $('#input-phone').show();
            $('input[name=email]').val('');
            $(this).text('Use Email');
        }
    });

    // Send Code
    $('#forgotForm').on('submit', function (e) {
    e.preventDefault();
    $('#fullscreenLoader').fadeIn();

    // Manually clear the hidden field
    if ($('#input-email').is(':hidden')) {
        $('input[name=email]').val('');
    } else {
        $('input[name=mobile]').val('');
    }

    const formData = new FormData(this);

    $.ajax({
            url: "{{ route('password.send.code') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success(response) {
                $('#fullscreenLoader').fadeOut();
                toastr.success(response.message);
                $('#forgotForm').hide();
                $('#verifyCodeForm').show();
            },
            error(xhr) {
                $('#fullscreenLoader').fadeOut();
                if (xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        toastr.error(errors[field][0]);
                    }
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
                }
            }
        });
    });


    // Verify Code
    $('#verifyCodeForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('password.verify.code') }}",
            method: "POST",
            data: $(this).serialize(),
            success(response) {
                toastr.success(response.message);
                $('#verifyCodeForm').hide();
                $('#newPasswordForm').show();
            },
            error(xhr) {
                toastr.error(xhr.responseJSON.message || "Invalid code.");
            }
        });
    });

    // Update Password
    $('#newPasswordForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('password.reset.update') }}",
            method: "POST",
            data: $(this).serialize(),
            success(response) {
                toastr.success(response.message);
                window.location.href = "{{ route('login') }}";
            },
            error(xhr) {
                const errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    toastr.error(errors[field][0]);
                }
            }
        });
    });
</script>


    <script>
        const toggleBtn = document.getElementById('themeToggle');
        const root = document.documentElement;
      
        // Load theme from localStorage
        if (localStorage.getItem('theme') === 'dark') {
          root.setAttribute('data-theme', 'dark');
          toggleBtn.textContent = '☀️ Light Mode';
        }
      
        toggleBtn.addEventListener('click', () => {
          if (root.getAttribute('data-theme') === 'dark') {
            root.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
            toggleBtn.textContent = '🌙 Dark Mode';
          } else {
            root.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
            toggleBtn.textContent = '☀️ Light Mode';
          }
        });
    </script>
    <script>
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if(session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif

        @if(session('info'))
            toastr.info("{{ session('info') }}");
        @endif
      </script>
</body>

</html>
