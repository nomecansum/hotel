
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>Login Page | Spotdesking</title>

    <!-- STYLESHEETS -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~--- -->

    <!-- Fonts [ OPTIONAL ] -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS [ REQUIRED ] -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">

    <!-- Nifty CSS [ REQUIRED ] -->
    <link rel="stylesheet" href="/assets/css/nifty.min.css">

    <!-- Premium line icons [ OPTIONAL ] -->
    <link rel="stylesheet" href="/assets/premium/icon-sets/icons/line-icons/premium-line-icons.min.css">

    <!-- Premium solid icons [ OPTIONAL ] -->
    <link rel="stylesheet" href="/assets/premium/icon-sets/icons/solid-icons/premium-solid-icons.min.css">

    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~---

    [ REQUIRED ]
    You must include this category in your project.


    [ OPTIONAL ]
    This is an optional plugin. You may choose to include it in your project.


    [ DEMO ]
    Used for demonstration purposes only. This category should NOT be included in your project.


    [ SAMPLE ]
    Here's a sample script that explains how to initialize plugins and/or components: This category should NOT be included in your project.


    Detailed information and more samples can be found in the documentation.

    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~--- -->
    @laravelPWA
</head>

<body class="">

    <!-- PAGE CONTAINER -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <div id="root" class="root front-container">

        <!-- CONTENTS -->
        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        @yield('content')

        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        <!-- END - CONTENTS -->
    </div>
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <!-- END - PAGE CONTAINER -->

</body>
@yield('scripts')
</html>