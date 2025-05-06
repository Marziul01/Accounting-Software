<!DOCTYPE html>
<html class="no-js ss-preload" lang="en">

<head>

    <!--- basic page needs
    ================================================== -->
    <meta charset="utf-8">
    <title>Luther</title>
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- mobile specific metas
    ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS
    ================================================== -->
    
    <link rel="stylesheet" href="{{ asset('frontend-assets') }}/css/vendor.css">
    <link rel="stylesheet" href="{{ asset('frontend-assets') }}/css/styles.css">

    <!-- favicons
    ================================================== -->
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('frontend-assets') }}/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('frontend-assets') }}/images/favicon-16x16.png">
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

    @yield('scripts')
</body>

</html>
