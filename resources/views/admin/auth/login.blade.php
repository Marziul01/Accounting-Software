<!doctype html>

<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset( 'admin-assets' )  }}/assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ $setting->site_name }} | Admin Login</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset($setting->site_favicon) }}" />

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
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Helpers -->
    <script src="{{ asset( 'admin-assets' )  }}/assets/vendor/js/helpers.js"></script>
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
                {{-- <button id="themeToggle" class="btn btn-sm btn-outline-secondary login-darkMOde">🌙 Dark Mode</button> --}}
                <div class="theme-switch-wrapper" style="position: fixed; top: 20px; right: 20px;">
                    <input type="checkbox" class="checkbox" id="themeToggle">
                    <label for="themeToggle" class="checkbox-label">
                    <i class="fas fa-moon"></i>
                    <i class="fas fa-sun"></i>
                    <span class="ball"></span>
                    </label>
                </div>
                <!-- Register -->
                <div class="card px-sm-6 px-0">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center" style="overflow: visible">
                            <a href="index.html" class="login app-brand-link gap-2">
                                <img src="{{ asset($setting->site_logo) }}" alt="Logo" class="logo" width="75%" />
                            </a>
                        </div>

                        <form id="formAuthentication" class="mb-6" action="index.html">
                            @csrf
                            <div class="mb-6 position-relative">
                                <label for="email" class="form-label">Email </label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" autofocus />
                            </div>
                            <div class="mb-6 form-password-toggle">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <button class="btn btn-primary d-grid w-100 mt-3" type="submit">Login</button>
                            </div>
                            <div class="w-100 text-center">
                                <a class="text-center text-danger" href="{{ route('forgotPass') }}">Forgot your password?</a>
                            </div>
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
  
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body d-flex justify-content-center flex-column align-items-center auth-error-modal">
                  <img src="{{ asset('admin-assets/img/fail.gif') }}" width="25%" alt="">
                  <h5 class="modal-title text-center text-danger" id="errorModalLabel">Access Denied</h5>
                  <p id="errorMessage" class="text-center text-danger">You are not authorized to access the Admin Panel.</p>
              </div>
          </div>
      </div>
  </div>

  <div id="fullscreenLoader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999999999;">
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
        $(document).ready(function () {
            $('#formAuthentication').on('submit', function (e) {
                e.preventDefault();
    
                let form = $(this);
                let url = "{{ route('admin.authenticate') }}";
                let data = form.serialize();
                $('#fullscreenLoader').fadeIn();
    
                // Clear previous error messages
                form.find('.text-danger').remove();
    
                // Show loader (optional)
                let submitButton = form.find('button[type="submit"]');
                submitButton.prop('disabled', true).text('Logging in...');
    
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        $('#fullscreenLoader').fadeOut();
                        // Display success message in the modal
                        $('#successMessage').text(response.message);
                        $('#successModal').modal('show');
    
                        // Redirect when "Go to Dashboard" button is clicked
                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 2000); // Delay of 2000 milliseconds (2 seconds)
                    },
                    error: function (xhr) {
                        $('#fullscreenLoader').fadeOut();
                      if (xhr.status === 422) {
                          let errors = xhr.responseJSON.errors;
  
                          if (errors.authorization) {
                              // Show "Not Authorized" error in the modal
                              $('#errorMessage').text(errors.authorization);
                              $('#errorModal').modal('show');
                          } else {
                              // Display other validation errors under the corresponding inputs
                              $.each(errors, function (field, message) {
                                  form.find(`[name="${field}"]`).after(
                                      `<small class="text-danger pass-error">${message}</small>`
                                  );
                              });
                          }
                      } else {
                          alert('An unexpected error occurred.');
                      }
                  },
                    complete: function () {
                        $('#fullscreenLoader').fadeOut();
                        submitButton.prop('disabled', false).text('Login');
                    },
                });
            });
        });
    </script>
    <script>
  const toggleCheckbox = document.getElementById('themeToggle');
  const root = document.documentElement;

  // Load theme from localStorage
  if (localStorage.getItem('theme') === 'dark') {
      root.setAttribute('data-theme', 'dark');
      toggleCheckbox.checked = true; // show ball on right side
  } else {
      toggleCheckbox.checked = false;
  }

  // Toggle theme on change
  toggleCheckbox.addEventListener('change', () => {
      if (toggleCheckbox.checked) {
          root.setAttribute('data-theme', 'dark');
          localStorage.setItem('theme', 'dark');
      } else {
          root.removeAttribute('data-theme');
          localStorage.setItem('theme', 'light');
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
