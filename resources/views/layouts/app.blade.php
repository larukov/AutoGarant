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
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        <div id="successToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true"
            data-bs-delay="3000">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">✅ Успешно</strong>
                <small>только что</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Операция выполнена успешно.
            </div>
        </div>

        <div id="errorToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true"
            data-bs-delay="5000">
            <div class="toast-header bg-danger text-white">
                <strong class="me-auto">❌ Ошибка</strong>
                <small>только что</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastErrorMessage">
                Произошла ошибка.
            </div>
        </div>
    </div>
    @yield('script')
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery-3.3.3.js') }}"></script>
    <script src="{{ asset('js/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ asset('js/modal.js') }}"></script>

    @guest
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if ($errors->hasBag('login'))
                    const loginModal = new bootstrap.Modal(document.getElementById('AuthorizationModal'));
                    loginModal.show();
                @elseif ($errors->any())
                    const regModal = new bootstrap.Modal(document.getElementById('RegModal'));
                    regModal.show();
                @endif
                    });
        </script>
    @endguest

    <script>
        $(document).ready(function () {
            $("#phone").mask("+7 (999) 999-99-99");
        });
    </script>
</body>

</html>