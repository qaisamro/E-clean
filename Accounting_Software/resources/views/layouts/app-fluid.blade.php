<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
<html
    lang="{{ config('app.locale') }}"
    class="light-style layout-menu-fixed"
    dir="{{ config('app.dir') }}"
    data-theme="theme-default"
    data-assets-path="../assets/"
    data-template="vertical-menu-template-free"
>
<head>
    <meta charset="utf-8"/>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>{{ $title ?? config('app.name') }}</title>

    <meta name="description" content=""/>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}?v=ec10"/>

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') . '?v=fix2' }}"
          class="template-customizer-theme-css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/ec-theme.css') }}?v=ec10"/>

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>

    <!-- Page CSS -->
    @stack('css')

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

</head>

<body>
<a class="ec-skip-link" href="#main-content">الانتقال إلى المحتوى</a>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->

        @include('layouts.partials.sidebar')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">

            <!-- Content wrapper -->
            <div class="content-wrapper" id="main-content">
                <!-- Content -->

                <div class="container-fluid flex-grow-1 container-p-y">
                    {{ $slot }}
                </div>
                <!-- / Content -->

                {{-- Footer removed for Elite Cleaning --}}

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

            {{-- Floating menu toggle — mobile/tablet only (direct body child, bulletproof) --}}
        <a href="javascript:void(0);" class="layout-menu-toggle ec-mobile-menu-fab" aria-label="فتح القائمة">
            <i class="bx bx-menu"></i>
        </a>

<!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
<!-- endbuild -->

<!-- Main JS -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- Vendors JS -->
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<!-- Page JS -->
<script src="{{ asset('assets/js/forms-selects.js') }}"></script>

@include('sweetalert::alert')

@stack('js')
</body>
</html>
