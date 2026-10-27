<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield("title")</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .object-cover {
            object-fit: cover;
        }

        .fw-black {
            font-weight: 900;
        }

        .text-muted-light {
            color: #a0aec0;
        }

        .bg-secondary-dark {
            background-color: #1a202c;
        }

        .italic {
            font-style: italic;
        }

        main {
            flex: 1 0 auto;
        }
    </style>
    @yield("css")
</head>

<body>
    @include("layouts.header")
    @guest
        @include('layouts.modal')
    @endguest
    @yield('content')
    @include("layouts.footer")
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery-3.3.3.js"></script>
    <script src="js/jquery.maskedinput.min.js"></script>
    <script src="js/modal.js"></script>
    <script>
        $(document).ready(function () {
            $("#phone").mask("+7 (999) 999-99-99");
        });
    </script>
</body>

</html>