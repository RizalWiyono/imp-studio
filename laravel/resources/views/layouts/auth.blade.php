<!DOCTYPE html>
<html lang="en" class="light-style" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('') }}"
    data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title', 'Dashboard')</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon/favicon.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('vendor/css/rtl/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/libs/typeahead-js/typeahead.css') }}" />
    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('vendor/css/pages/page-auth.css') }}" />
    <!-- Helpers -->
    <script src="{{ asset('vendor/js/helpers.js') }}"></script>

    <script src="{{ asset('vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('js/config.js') }}"></script>

    <!-- Custom styles for responsive design -->
    <style>
        /* Base styles */
        html,
        body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            overflow-x: hidden;
            /* Prevent horizontal scrolling */
            overflow-y: auto;
            /* Allow vertical scrolling when needed */
        }

        .authentication-wrapper {
            min-height: 100vh;
            width: 100%;
            display: flex;
        }

        .authentication-inner {
            width: 100%;
            display: flex;
            flex-direction: row;
            /* Ensure row layout */
            margin: 0 !important;
            /* Fix for row margin affecting alignment */
        }

        /* Image side styling */
        .img-side {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 66.66%;
            /* Fixed width matching the col-8 */
            z-index: 0;
            /* Changed from -1 to prevent interaction issues */
            overflow: hidden;
            /* background: linear-gradient(50deg, #7C56E5 0%, #36226B 100%); */
            background: url({{ asset('img/auth-bg.png') }}) no-repeat center center;
            background-size: cover;
            display: flex;
            flex-direction: column;
        }

        /* Logo positioning - top left */
        .img-side img#brand {
            position: absolute;
            top: 30px;
            left: 50px;
            width: 180px;
            height: auto;
            z-index: 3;
        }

        /* Pattern positioning - cover the background */
        .img-side img#pattern {
            position: absolute;
            width: 45%;
            height: auto;
            bottom: 0;
            left: 0;
            z-index: 1;
        }

        /* Main illustration positioning - bottom right */
        .img-side img#bringing-box {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 80%;
            height: auto;
            max-height: 95vh;
            object-fit: contain;
            object-position: bottom right;
            z-index: 2;
        }

        /* Login content side styling */
        .authentication-bg {
            width: 33.33%;
            /* col-4 width */
            min-height: 100vh;
            margin-left: 66.66%;
            /* Exactly matches the img-side width */
            background-color: #fff;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Content container */
        .w-px-400 {
            width: 400px;
            max-width: 100%;
            padding: 2rem;
        }

        /* Large screens: Show image on left */
        @media (min-width: 992px) {
            .authentication-bg {
                width: 33.33%;
                /* col-4 width */
                margin-left: 66.66%;
                /* Push content to right */
                background-color: rgba(255, 255, 255, 0.95);
            }
        }

        /* Mobile: Full-width content */
        @media (max-width: 991.98px) {
            .img-side {
                position: absolute;
                height: 30vh;
                max-width: 100%;
                width: 100%;
            }

            .img-side img#brand {
                top: 20px;
                left: 20px;
                width: 120px;
            }

            .img-side img#bringing-box {
                right: 0;
                width: 40%;
                max-width: 300px;
            }

            .authentication-bg {
                width: 100%;
                margin-left: 0;
                padding-top: 30vh !important;
                /* Space for image */
            }
        }

        /* Allow scrolling only when needed */
        @media (max-height: 700px),
        (max-width: 767.98px) {
            body {
                overflow-y: auto !important;
            }

            .authentication-bg {
                overflow-y: auto !important;
            }
        }
    </style>
</head>

<body>
    <!-- Content -->
    <div class="authentication-wrapper authentication-cover">
        <div class="authentication-inner row m-0">

            <div class="img-side">
            </div>

            <!-- Login Content -->
            <div class="d-flex col-12 align-items-center authentication-bg p-sm-5 p-4">
                <div class="w-px-400 mx-auto">
                    <!-- Logo -->
                    <div class="app-brand mb-4">
                        <a href="index.html" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <img src="{{ asset('img/logo.png') }}" alt="Logo" width="100">
                            </span>
                        </a>
                    </div>
                    <!-- /Logo -->

                    <!-- Content Yield - This will always be fully visible -->
                    @yield('content')
                </div>
            </div>
            <!-- /Login Content -->
        </div>
    </div>
    <!-- / Content -->

    <!-- Core JS -->
    <script src="{{ asset('vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('vendor/js/menu.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ asset('vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('js/pages-auth.js') }}"></script>

    <!-- Responsive handling script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if content height exceeds viewport
            function checkContentSize() {
                const content = document.querySelector('.w-px-400');
                const viewportHeight = window.innerHeight;

                if (content && content.offsetHeight > viewportHeight - 100) {
                    // Content is too tall, ensure scrolling is enabled
                    document.body.style.overflowY = 'auto';
                    document.querySelector('.authentication-bg').style.overflowY = 'auto';
                }
            }

            // Run on load and resize
            checkContentSize();
            window.addEventListener('resize', checkContentSize);
        });
    </script>
</body>

</html>
