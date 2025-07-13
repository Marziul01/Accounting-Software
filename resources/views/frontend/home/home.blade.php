@extends('frontend.master')

@section('content')
    <main class="s-content min-h-screen">


        <!-- ### intro
        ================================================== -->
        <section id="intro" class="s-intro target-section">

            <div class="row intro-content wide">

                {{-- <div class="col-md-8">
                <div class="intro-content__pic">
                    <img src="{{ asset('frontend-assets') }}/images/avatars/user-04.jpg" class="" >
                </div>
                <div class="text-pretitle with-line">
                    <h1 class="text-huge-title">
                        {{ $home->name ?? 'Rashel Mia.' }}
                    </h1>
                </div>
                <div>
                    <p class="intro-content__desc">
                        {{ $home->description ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.' }}
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                
                <div class="card p-4">
                    <div class="mb-3 h1 d-flex align-items-center mt-0">
                        <i class="fas fa-phone-alt me-2"></i>
                        <span>{{ $home->phone ?? '+123 456 7890' }}</span>
                    </div>
                    <div class="mb-3 h1 d-flex align-items-center">
                        <i class="fas fa-envelope me-2"></i>
                        <span>{{ $home->email ?? 'example@email.com' }}</span>
                    </div>
                    <div class="mb-3 h1 d-flex align-items-center">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <span>{{ $home->address ?? '123 Main St, City, Country' }}</span>
                    </div>
                </div>
                
            </div> --}}

                <div class=" flex items-center justify-center p-4 sm:p-6 md:p-8">
                    <div class="profile-container w-full max-w-5xl bg-white rounded-2xl overflow-hidden">
                        <div class="flex flex-container">

                            <div class="w-full md:w-3/5 p-8 md:p-10 flex flex-col justify-center">
                                <div class="flex flex-col md:flex-row items-center md:items-start mb-6">
                                    <div
                                        class="rounded-full overflow-hidden mb-4 md:mb-0 md:mr-6 flex-shrink-0 border-4 border-indigo-100">
                                        <img src="{{ asset($home->image) }}" class="" >
                                    </div>
                                    
                                </div>
                                <div>
                                        <h1 class="text-huge-title">
                                            {{ $home->name ?? 'Rashel Mia.' }}
                                        </h1>
                                </div>
                                <div class="mobile-hidden">
                                    <a href="{{ $home->whatsapp }}" title="Whatsapp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <a href="{{ $home->facebook }}" title="Facebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="{{ $home->telegram }}" title="Telegram">
                                        <i class="fab fa-telegram-plane"></i>
                                    </a>
                                    <a href="{{ $home->insta }}" title="Instagram">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                </div>
                                <div class="space-y-4 mt-3">
                                    <p class="text-white leading-relaxed">
                                        {{ $home->desc ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.' }}
                                    </p>

                                    {{-- <div class="flex flex-wrap gap-2 mt-2">
                                        <span
                                            class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded-full text-sm font-medium">UI
                                            Design</span>
                                        <span
                                            class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded-full text-sm font-medium">UX
                                            Research</span>
                                        <span
                                            class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded-full text-sm font-medium">Frontend</span>
                                        <span
                                            class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded-full text-sm font-medium">Prototyping</span>
                                    </div>

                                    <div class="pt-4">
                                        <button
                                            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-300 flex items-center">
                                            <span>View Portfolio</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div> --}}
                                </div>
                            </div>
                            <!-- Left Side - Contact Details -->
                            <div class="w-full md:w-2/5 gradient-bg text-white p-8 md:p-10 flex flex-col justify-center">
                                <h2 class="text-2xl md:text-3xl font-bold mb-8">Get in Touch</h2>

                                <div class="space-y-6">
                                    <div
                                        class="contact-item flex items-center space-x-4 p-4 bg-white bg-opacity-10 rounded-lg">
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
                            </div>

                            <!-- Right Side - Profile Info -->
                            
                        </div>
                    </div>
                </div>

                <ul class="intro-social align-items-center justify-content-center">
                    <li>
                        <a href="{{ $home->whatsapp }}" title="Whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $home->facebook }}" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $home->telegram }}" title="Telegram">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $home->insta }}" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </li>
                </ul>



            </div> <!-- end intro content -->
            {{-- <div class="column ss-copyright">
                <span>© Copyright Luther 2021</span>
                <span>Design by <a href="https://www.styleshout.com/">StyleShout</a> Distribution By <a
                        href="https://themewagon.com">Themewagon</a></span>
            </div> --}}
            {{-- <a href="#about" class="intro-scrolldown smoothscroll">
            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M11 21.883l-6.235-7.527-.765.644 7.521 9 7.479-9-.764-.645-6.236 7.529v-21.884h-1v21.883z"/></svg>
        </a> --}}

        </section> <!-- end s-intro -->


        <!-- ### about
        ================================================== -->
        {{-- <section id="about" class="s-about target-section">


        <div class="row about-info wide" data-animate-block>

            <div class="column lg-6 md-12 about-info__pic-block">
                <img src="{{ asset('frontend-assets') }}/images/about-photo.jpg" 
                     srcset="{{ asset('frontend-assets') }}/images/about-photo.jpg 1x, images/about-photo@2x.jpg 2x" alt="" class="about-info__pic" data-animate-el>
            </div>

            <div class="column lg-6 md-12">
                <div class="about-info__text" >

                    <h2 class="text-pretitle with-line" data-animate-el>
                        About
                    </h2>
                    <p class="attention-getter" data-animate-el>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod 
                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim 
                        veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex 
                        ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate 
                        velit esse cillum dolore eu fugiat nulla pariatur.
                    </p>
                    <a href="#0" class="btn btn--medium u-fullwidth" data-animate-el>Download CV</a>

                </div>
            </div>
        </div> <!-- about-info -->


        <div class="row about-expertise" data-animate-block>
            <div class="column lg-12">

                <h2 class="text-pretitle" data-animate-el>Expertise</h2>

                <ul class="skills-list h1" data-animate-el>
                    <li>Visual Design</li>
                    <li>Branding Identity</li>
                    <li>UI Design</li>
                    <li>Product Design</li>
                    <li>Prototyping</li>
                    <li>Illustration</li>
                </ul>

            </div>
        </div> <!-- end about-expertise -->


        <div class="row about-timelines" data-animate-block>

            <div class="column lg-6 tab-12">

                <h2 class="text-pretitle" data-animate-el>
                    Experience
                </h2>

                <div class="timeline" data-animate-el>

                    <div class="timeline__block">
                        <div class="timeline__bullet"></div>
                        <div class="timeline__header">
                            <h4 class="timeline__title">Dropbox</h3>
                            <h5 class="timeline__meta">Product Designer</h5>
                            <p class="timeline__timeframe">August 2019 - Present</p>
                        </div>
                        <div class="timeline__desc">
                            <p>Lorem ipsum Occaecat do esse ex et dolor culpa nisi ex in magna consectetur nisi cupidatat laboris esse eiusmod deserunt aute do quis velit esse sed Ut proident cupidatat nulla esse cillum laborum occaecat nostrud sit dolor incididunt amet est occaecat nisi.</p>
                        </div>
                    </div>

                    <div class="timeline__block">
                        <div class="timeline__bullet"></div>
                        <div class="timeline__header">
                            <h4 class="timeline__title">Microsoft</h4>
                            <h5 class="timeline__meta">Frontend Developer</h5>
                            <p class="timeline__timeframe">August 2016 - July 2019</p>
                        </div>
                        <div class="timeline__desc">
                            <p>Lorem ipsum Occaecat do esse ex et dolor culpa nisi ex in magna consectetur nisi cupidatat laboris esse eiusmod deserunt aute do quis velit esse sed Ut proident cupidatat nulla esse cillum laborum occaecat nostrud sit dolor incididunt amet est occaecat nisi.</p>
                        </div>
                    </div>

                </div> <!-- end timeline -->

            </div> <!-- end column -->

            <div class="column lg-6 tab-12">

                <h2 class="text-pretitle" data-animate-el>
                    Education
                </h2>

                <div class="timeline" data-animate-el>

                    <div class="timeline__block">
                        <div class="timeline__bullet"></div>
                        <div class="timeline__header">
                            <h4 class="timeline__title">University of Life</h3>
                            <h5 class="timeline__meta">Master in Graphic Design</h5>
                            <p class="timeline__timeframe">April 2015</p>
                        </div>
                        <div class="timeline__desc">
                            <p>Lorem ipsum Occaecat do esse ex et dolor culpa nisi ex in magna consectetur nisi cupidatat laboris esse eiusmod deserunt aute do quis velit esse sed Ut proident cupidatat nulla esse cillum laborum occaecat nostrud sit dolor incididunt amet est occaecat nisi.</p>
                        </div>
                    </div>

                    <div class="timeline__block">
                        <div class="timeline__bullet"></div>
                        <div class="timeline__header">
                            <h4 class="timeline__title">School of Cool Designers</h4>
                            <h5 class="timeline__meta">B.A. Degree in Graphic Design</h5>
                            <p class="timeline__timeframe">August 2012</p>
                        </div>
                        <div class="timeline__desc">
                            <p>Lorem ipsum Occaecat do esse ex et dolor culpa nisi ex in magna consectetur nisi cupidatat laboris esse eiusmod deserunt aute do quis velit esse sed Ut proident cupidatat nulla esse cillum laborum occaecat nostrud sit dolor incididunt amet est occaecat nisi.</p>
                        </div>
                    </div>

                </div> <!-- end timeline -->
                
            </div> <!-- end column -->


        </div> <!-- end about-timelines -->

    </section>  --}}

        <!-- end s-about -->


        <!-- ### works
        ================================================== -->
        {{-- <section id="works" class="s-works target-section">


        <div class="row works-portfolio">

            <div class="column lg-12" data-animate-block>

                <h2 class="text-pretitle" data-animate-el>
                    Recent Works
                </h2>
                <p class="h1" data-animate-el>
                    Here are some of my favorite projects I have done lately. Feel free to check them out.
                </p>

                <ul class="folio-list row block-lg-one-half block-stack-on-1000">

                    <li class="folio-list__item column" data-animate-el>
                        <a class="folio-list__item-link" href="#modal-01">
                            <div class="folio-list__item-pic">
                                <img src="{{ asset('frontend-assets') }}/images/portfolio/fuji.jpg"
                                     srcset="{{ asset('frontend-assets') }}/images/portfolio/fuji.jpg 1x, images/portfolio/fuji@2x.jpg 2x"  alt="">
                            </div>
                            
                            <div class="folio-list__item-text">
                                <div class="folio-list__item-cat">
                                    Website
                                </div>
                                <div class="folio-list__item-title">
                                    Retro Camera.
                                </div>
                            </div>
                        </a>
                        <a class="folio-list__proj-link" href="#" title="project link">
                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        </a>
                    </li> <!--end folio-list__item -->

                    <li class="folio-list__item column" data-animate-el>
                        <a class="folio-list__item-link" href="#modal-02">
                            <div class="folio-list__item-pic">
                                <img src="{{ asset('frontend-assets') }}/images/portfolio/lamp.jpg" 
                                     srcset="{{ asset('frontend-assets') }}/images/portfolio/lamp.jpg 1x, images/portfolio/lamp@2x.jpg 2x" alt="">
                            </div>
                            
                            <div class="folio-list__item-text">
                                <div class="folio-list__item-cat">
                                    Product Design
                                </div>
                                <div class="folio-list__item-title">
                                    The White Lamp.
                                </div>
                            </div>
                        </a>
                        <a class="folio-list__proj-link" href="#" title="project link">
                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        </a>
                    </li> <!--end folio-list__item -->

                    <li class="folio-list__item column" data-animate-el>
                        <a class="folio-list__item-link" href="#modal-03">
                            <div class="folio-list__item-pic">
                                <img src="{{ asset('frontend-assets') }}/images/portfolio/rucksack.jpg" 
                                     srcset="{{ asset('frontend-assets') }}/images/portfolio/rucksack.jpg 1x, images/portfolio/rucksack@2x.jpg 2x" alt="">
                            </div>
                            
                            <div class="folio-list__item-text">
                                <div class="folio-list__item-cat">
                                    Branding
                                </div>
                                <div class="folio-list__item-title">
                                    Rucksuck.
                                </div>
                            </div>
                        </a>
                        <a class="folio-list__proj-link" href="#" title="project link">
                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        </a>
                    </li> <!--end folio-list__item -->

                    <li class="folio-list__item column" data-animate-el>
                        <a class="folio-list__item-link" href="#modal-04">
                            <div class="folio-list__item-pic">
                                <img src="{{ asset('frontend-assets') }}/images/portfolio/skaterboy.jpg"
                                     srcset="{{ asset('frontend-assets') }}/images/portfolio/skaterboy.jpg 1x, images/portfolio/skaterboy@2x.jpg 2x" alt="">
                            </div>
                            
                            <div class="folio-list__item-text">
                                <div class="folio-list__item-cat">
                                    Website
                                </div>
                                <div class="folio-list__item-title">
                                    Since Day One.
                                </div>
                            </div>
                        </a>
                        <a class="folio-list__proj-link" href="#" title="project link">
                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        </a>
                    </li> <!--end folio-list__item -->

                    <li class="folio-list__item column" data-animate-el>
                        <a class="folio-list__item-link" href="#modal-05">
                            <div class="folio-list__item-pic">
                                <img src="{{ asset('frontend-assets') }}/images/portfolio/sanddunes.jpg" 
                                     srcset="{{ asset('frontend-assets') }}/images/portfolio/sanddunes.jpg 1x, images/portfolio/sanddunes@2x.jpg 2x" alt="">
                            </div>
                            
                            <div class="folio-list__item-text">
                                <div class="folio-list__item-cat">
                                    Illustration
                                </div>
                                <div class="folio-list__item-title">
                                    Sand Dunes.
                                </div>
                            </div>
                        </a>
                        <a class="folio-list__proj-link" href="#" title="project link">
                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        </a>
                    </li> <!--end folio-list__item -->

                    <li class="folio-list__item column" data-animate-el>
                        <a class="folio-list__item-link" href="#modal-06">
                            <div class="folio-list__item-pic">
                                <img src="{{ asset('frontend-assets') }}/images/portfolio/minimalismo.jpg" 
                                     srcset="{{ asset('frontend-assets') }}/images/portfolio/minimalismo.jpg 1x, images/portfolio/minimalismo@2x.jpg 2x" alt="">
                            </div>
                            
                            <div class="folio-list__item-text">
                                <div class="folio-list__item-cat">
                                    Branding
                                </div>
                                <div class="folio-list__item-title">
                                    Minimalismo.
                                </div>
                            </div>
                        </a>
                        <a class="folio-list__proj-link" href="#" title="project link">
                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.14645 3.14645C8.34171 2.95118 8.65829 2.95118 8.85355 3.14645L12.8536 7.14645C13.0488 7.34171 13.0488 7.65829 12.8536 7.85355L8.85355 11.8536C8.65829 12.0488 8.34171 12.0488 8.14645 11.8536C7.95118 11.6583 7.95118 11.3417 8.14645 11.1464L11.2929 8H2.5C2.22386 8 2 7.77614 2 7.5C2 7.22386 2.22386 7 2.5 7H11.2929L8.14645 3.85355C7.95118 3.65829 7.95118 3.34171 8.14645 3.14645Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        </a>
                    </li> <!--end folio-list__item -->

                </ul> <!-- end folio-list -->

            </div> <!-- end column -->


            <!-- Modal Templates Popup
            -------------------------------------------- -->
            <div id="modal-01" hidden>
                <div class="modal-popup">
                    <img src="{{ asset('frontend-assets') }}/images/portfolio/gallery/g-fuji.jpg" alt="">
        
                    <div class="modal-popup__desc">
                        <h5>Retro Camera</h5>
                        <p>Odio soluta enim quos sit asperiores rerum rerum repudiandae cum. Vel voluptatem alias qui assumenda iure et expedita voluptatem. Ratione officiis quae.</p>
                        <ul class="modal-popup__cat">
                            <li>Branding</li>
                            <li>Product Design</li>
                        </ul>
                    </div>
        
                    <a href="https://www.behance.net/" class="modal-popup__details">Project link</a>
                </div>
            </div> <!-- end modal -->

            <div id="modal-02" hidden>
                <div class="modal-popup">
                    <img src="{{ asset('frontend-assets') }}/images/portfolio/gallery/g-lamp.jpg" alt="">
        
                    <div class="modal-popup__desc">
                        <h5>The White Lamp</h5>
                        <p>Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit.</p>
                        <ul class="modal-popup__cat">
                            <li>Branding</li>
                        </ul>
                    </div>
        
                    <a href="https://www.behance.net/" class="modal-popup__details">Project link</a>
                </div>
            </div> <!-- end modal -->

            <div id="modal-03" hidden>
                <div class="modal-popup">
                    <img src="{{ asset('frontend-assets') }}/images/portfolio/gallery/g-rucksack.jpg" alt="">
        
                    <div class="modal-popup__desc">
                        <h5>Rucksuck</h5>
                        <p>Exercitationem reprehenderit quod explicabo consequatur aliquid ut magni ut. Deleniti quo corrupti illum velit eveniet ratione necessitatibus ipsam mollitia.</p>
                        <ul class="modal-popup__cat">
                            <li>Product Design</li>
                        </ul>
                    </div>
        
                    <a href="https://www.behance.net/" class="modal-popup__details">Project link</a>
                </div>
            </div> <!-- end modal -->

            <div id="modal-04" hidden>
                <div class="modal-popup">
                    <img src="{{ asset('frontend-assets') }}/images/portfolio/gallery/g-skaterboy.jpg" alt="">
        
                    <div class="modal-popup__desc">
                        <h5>Since Day One</h5>
                        <p>Dolores velit qui quos nobis. Aliquam delectus voluptas quos possimus non voluptatem voluptas voluptas. Est doloribus eligendi porro doloribus voluptatum.</p>
                        <ul class="modal-popup__cat">
                            <li>Website</li>
                        </ul>
                    </div>
        
                    <a href="https://www.behance.net/" class="modal-popup__details">Project link</a>
                </div>
            </div> <!-- end modal -->

            <div id="modal-05" hidden>
                <div class="modal-popup">

                    <img src="{{ asset('frontend-assets') }}/images/portfolio/gallery/g-sanddunes.jpg" alt="">
        
                    <div class="modal-popup__desc">
                        <h5>Sand Dunes</h5>
                        <p>Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit.</p>
                        <ul class="modal-popup__cat">
                            <li>Illustration</li>
                        </ul>
                    </div>
        
                    <a href="https://www.behance.net/" class="modal-popup__details">Project link</a>
                </div>
            </div> <!-- end modal -->

            <div id="modal-06" hidden>
                <div class="modal-popup">
                    <img src="{{ asset('frontend-assets') }}/images/portfolio/gallery/g-minimalismo.jpg" alt="">
        
                    <div class="modal-popup__desc">
                        <h5>Minimalismo</h5>
                        <p>Quisquam vel libero consequuntur autem voluptas. Qui aut vero. Omnis fugit mollitia cupiditate voluptas. Aenean sollicitudin, lorem quis bibendum auctor.</p>
                        <ul class="modal-popup__cat">
                            <li>Branding</li>
                            <li>Product Design</li>
                        </ul>
                    </div>
        
                    <a href="https://www.behance.net/" class="modal-popup__details">Project link</a>
                </div>
            </div> <!-- end modal -->

        </div> <!-- end works-portfolio -->


        <div class="row testimonials">
            <div class="column lg-12" data-animate-block>

                <div class="swiper-container testimonial-slider" data-animate-el>

                    <div class="swiper-wrapper">

                        <div class="testimonial-slider__slide swiper-slide">
                            <div class="testimonial-slider__author">
                                <img src="{{ asset('frontend-assets') }}/images/avatars/user-02.jpg" alt="Author image" class="testimonial-slider__avatar">
                                <cite class="testimonial-slider__cite">
                                    <strong>Tim Cook</strong>
                                    <span>CEO, Apple</span>
                                </cite>
                            </div>
                            <p>
                            Molestiae incidunt consequatur quis ipsa autem nam sit enim magni. Voluptas tempore rem. 
                            Explicabo a quaerat sint autem dolore ducimus ut consequatur neque.  Nisi dolores quaerat fuga rem nihil nostrum.
                            Laudantium quia consequatur molestias delectus culpa.
                            </p>
                        </div> <!-- end testimonial-slider__slide -->
        
                        <div class="testimonial-slider__slide swiper-slide">
                            <div class="testimonial-slider__author">
                                <img src="{{ asset('frontend-assets') }}/images/avatars/user-03.jpg" alt="Author image" class="testimonial-slider__avatar">
                                <cite class="testimonial-slider__cite">
                                    <strong>Sundar Pichai</strong>
                                    <span>CEO, Google</span>
                                </cite>
                            </div>
                            <p>
                            Excepturi nam cupiditate culpa doloremque deleniti repellat. Veniam quos repellat voluptas animi adipisci.
                            Nisi eaque consequatur. Voluptatem dignissimos ut ducimus accusantium perspiciatis.
                            Quasi voluptas eius distinctio. Atque eos maxime.
                            </p>
                        </div> <!-- end testimonial-slider__slide -->
        
                        <div class="testimonial-slider__slide swiper-slide">
                            <div class="testimonial-slider__author">
                                <img src="{{ asset('frontend-assets') }}/images/avatars/user-01.jpg" alt="Author image" class="testimonial-slider__avatar">
                                <cite class="testimonial-slider__cite">
                                    <strong>Satya Nadella</strong>
                                    <span>CEO, Microsoft</span>
                                </cite>
                            </div>
                            <p>
                            Repellat dignissimos libero. Qui sed at corrupti expedita voluptas odit. Nihil ea quia nesciunt. Ducimus aut sed ipsam.  
                            Autem eaque officia cum exercitationem sunt voluptatum accusamus. Quasi voluptas eius distinctio.
                            Voluptatem dignissimos ut.
                            </p>
                        </div> <!-- end testimonial-slider__slide -->

                        <div class="testimonial-slider__slide swiper-slide">
                            <div class="testimonial-slider__author">
                                <img src="{{ asset('frontend-assets') }}/images/avatars/user-06.jpg" alt="Author image" class="testimonial-slider__avatar">
                                <cite class="testimonial-slider__cite">
                                    <strong>Jeff Bezos</strong>
                                    <span>CEO, Amazon</span>
                                </cite>
                            </div>
                            <p>
                            Nunc interdum lacus sit amet orci. Vestibulum dapibus nunc ac augue. Fusce vel dui. In ac felis 
                            quis tortor malesuada pretium. Curabitur vestibulum aliquam leo. Qui sed at corrupti expedita voluptas odit. 
                            Nihil ea quia nesciunt. Ducimus aut sed ipsam.
                            </p>
                        </div> <!-- end testimonial-slider__slide -->
    
                    </div> <!-- end swiper-wrapper -->

                    <div class="swiper-pagination"></div>

                </div> <!-- end swiper-container -->

            </div> <!-- end column -->
        </div> <!-- end row testimonials -->

    </section> 
    
    <!-- end s-works --> --}}


        <!-- ### contact
        ================================================== -->
        {{-- <section id="contact" class="s-contact target-section">

        <div class="row contact-top">
            <div class="column lg-12">
                <h2 class="text-pretitle">
                    Get In Touch
                </h2>

                <p class="h1">
                    I love to hear from you.
                    Whether you have a question or just 
                    want to chat about design, tech & art — shoot me a message.
                </p>
            </div>
        </div> <!-- end contact-top -->

        <div class="row contact-bottom">
            <div class="column lg-3 md-5 tab-6 stack-on-550 contact-block">
                <h3 class="text-pretitle">Reach me at</h3>
                <p class="contact-links">
                    <a href="mailto:sayhello@luther.com" class="mailtoui">sayhello@luther.com</a> <br>
                    <a href="tel:+1975432345">+197 543 2345</a>
                </p>
            </div>
            <div class="column lg-4 md-5 tab-6 stack-on-550 contact-block">
                <h3 class="text-pretitle">Social</h3>
                <ul class="contact-social">
                    <li><a href="#0">Behance</a></li>
                    <li><a href="#0">Dribble</a></li>
                    <li><a href="#0">Twitter</a></li>
                    <li><a href="#0">Instagram</a></li>
                    <li><a href="#0">Github</a></li>
                </ul>
            </div>
            <div class="column lg-4 md-12 contact-block">
                <a href="mailto:sayhello@luther.com" class="mailtoui btn btn--medium u-fullwidth contact-btn">Say Hello.</a>
            </div>
        </div> <!-- end contact-bottom -->

    </section> 
    
    <!-- end contact --> --}}

    </main>
@endsection

@section('scripts')
    <script>
        // Custom JavaScript can go here
        console.log("Home page loaded");
    </script>
@endsection
