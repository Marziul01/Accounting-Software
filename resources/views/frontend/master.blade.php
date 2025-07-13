<!DOCTYPE html>
<html class="no-js ss-preload" lang="en">

<head>

    <!--- basic page needs
    ================================================== -->
    <meta charset="utf-8">
    <title>{{ $setting->site_name }}</title>
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- mobile specific metas
    ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS
    ================================================== -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('frontend-assets') }}/css/vendor.css">
    <link rel="stylesheet" href="{{ asset('frontend-assets') }}/css/styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend-assets') }}/css/style.css">
    <!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- favicons
    ================================================== -->
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset($setting->site_favicon) }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset($setting->site_favicon) }}">
    <link rel="manifest" href="site.webmanifest">

</head>

<body id="top">


    <!-- # preloader
    ================================================== -->
    <div id="preloader">
        <div id="loader">
        </div>
    </div>


    <!-- # page wrap
    ================================================== -->
    <div class="s-pagewrap">

        <div class="circles">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>


        <!-- ## site header
        ================================================== -->
        @include('frontend.include.header')
        <!-- end s-header -->


        <!-- ## main content
        ==================================================- -->
        @yield('content')
        <!-- end s-content -->


        <!-- ## footer
        ================================================== -->
        @include('frontend.include.footer')
        <!-- end s-footer -->

    </div> <!-- end -s-pagewrap -->


    <!-- Java Script
    ================================================== -->
    <script src="{{ asset('frontend-assets') }}/js/plugins.js"></script>
    <script src="{{ asset('frontend-assets') }}/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    @yield('scripts')
</body>

</html>
