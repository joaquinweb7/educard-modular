<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>CERTIFICADO</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('main-1/logo.png') }}" />
    <!-- ENABLE LOADERS -->
    
    
    <script src="{{ asset('main-1/loader.js') }}"></script>
    <!-- /ENABLE LOADERS -->
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="{{ asset('plugins/src/bootstrap/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    
    
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    
    
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->
</head>

<body class="layout-boxed" page="starter-pack">

    <!-- BEGIN LOADER -->
    @include('main-1.layouts.loader')
    <!-- END LOADER -->

    <!-- BEGIN MAIN CONTAINER -->
    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!-- BEGIN CONTENT AREA -->
        <div id="content" class="main-content ms-0 mt-0">
            <div class="layout-px-spacing">

                <div class="middle-content">
                    <!-- Result Message -->
                    <div class="row">
                        @yield('content')
                    </div>
                </div>

            </div>
        </div>
        <!-- END CONTENT AREA -->
    </div>
    <!-- END MAIN CONTAINER -->

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('plugins/src/global/vendors.min.js') }}"></script>
    <script src="{{ asset('plugins/src/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/src/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
</body>

</html>
