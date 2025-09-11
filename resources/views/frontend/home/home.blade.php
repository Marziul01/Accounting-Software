@extends('frontend.master')

@section('content')
    <main class="s-content min-h-screen">
        <section id="intro" class="s-intro target-section">
            <div class="row intro-content wide">
                <div class=" flex items-center justify-center p-4 sm:p-6 md:p-8">
                    <div class="profile-container w-full max-w-5xl bg-white rounded-2xl overflow-hidden">
                        <div class="flex flex-container">

                            <div class="w-full md:w-3/5 p-8 md:p-10 flex flex-col justify-center">
                                <div class="flex flex-col md:flex-row items-center md:items-start mb-6">
                                    
                                    <div class="swiper mySwiper w-full">
                                        <div class="swiper-wrapper">
                                            @foreach (['image', 'image2', 'image3', 'image4', 'image5'] as $key)
                                                @if (!empty($home->$key))
                                                    <div class="swiper-slide">
                                                        <div class=" mb-6">
                                                            <div class="w-full md:w-1/2">
                                                                <img src="{{ asset($home->$key) }}"
                                                                    class="w-100 h-100 border-4 rounded-full border-indigo-100"
                                                                    alt="Slider Image">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        <!-- Pagination Dots -->
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                                <div>
                                    <h1 class="text-huge-title">
                                        {{ $home->name ?? 'Rashel Mia.' }}
                                    </h1>
                                </div>
                                <div class="mobile-hidden">
                                    <div class="iconsss">
                                        <div class="bg">
                                            <a href="{{ $home->whatsapp }}" title="Whatsapp" style="color: #25D366;">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                            <a href="{{ $home->facebook }}" title="Facebook" style="color: #1877F2;">
                                                <i class="fab fa-facebook-f"></i>
                                            </a>
                                            <a href="{{ $home->telegram }}" title="Telegram" style="color: #0088cc;">
                                                <i class="fab fa-telegram-plane"></i>
                                            </a>
                                            <a href="{{ $home->insta }}" title="Instagram" style="color: #E1306C;">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                            <a href="mailto:{{ $home->email }}" title="Email" style="color: #EA4335;">
                                                <i class="fa-solid fa-envelope"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-4 mt-3">
                                    <p class="text-white leading-relaxed justified-text">
                                        {{ $home->desc ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.' }}
                                    </p>

                                </div>
                            </div>
                            <!-- Left Side - Contact Details -->
                            <div class="w-full md:w-2/5 gradient-bg text-white p-8 md:p-10 flex flex-col justify-center">
                                <h2 class="text-2xl md:text-3xl font-bold mb-8">Get in Touch</h2>

                                <div class="space-y-6">
                                    <div
                                        class="contact-item flex items-center space-x-4 p-4  bg-white bg-opacity-10 rounded-lg">
                                        <div class="flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm opacity-80">Email</p>
                                            <p class="font-medium">{{ $home->email }}</p>
                                        </div>
                                    </div>

                                    <div
                                        class="contact-item flex items-center space-x-4 p-4 bg-white bg-opacity-10 rounded-lg">
                                        <div class="flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm opacity-80">Phone</p>
                                            <p class="font-medium">{{ $home->phone }}</p>
                                        </div>
                                    </div>

                                    <div
                                        class="contact-item flex items-center space-x-4 p-4 bg-white bg-opacity-10 rounded-lg">
                                        <div class="flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm opacity-80">Address</p>
                                            <p class="font-medium">{{ $home->address }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 card">
                                    <a href="{{ route('login') }}" class="rotating-border-btn">
                                        Login to Your Software !</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="intro-social align-items-center justify-content-center">
                    <li>
                        <a href="{{ $home->whatsapp }}" title="Whatsapp" style="color: #25D366;">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $home->facebook }}" title="Facebook" style="color: #1877F2;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $home->telegram }}" title="Telegram" style="color: #0088cc;">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $home->insta }}" title="Instagram" style="color: #E1306C;">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </li>
                    <li>
                        <a href="mailto:{{ $home->email }}" title="Email" style="color: #EA4335;">
                                                <i class="fa-solid fa-envelope"></i>
                                            </a>
                    </li>
                </ul>
            </div>
        </section>
    </main>

    

@endsection

@section('scripts')
    <script>
        // Custom JavaScript can go here
        console.log("Home page loaded");
    </script>
    <script>
        new Swiper('.mySwiper', {
            loop: true,
            // autoplay: {
            //     delay: 5000,
            //     disableOnInteraction: false,
            // },
            autoplay: false,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            slidesPerView: 1,
            spaceBetween: 20,
            allowTouchMove: true,
            speed: 1000, // Slower = smoother (default is 300ms)
            effect: 'slide', // Keep it simple
        });
    </script>
@endsection
