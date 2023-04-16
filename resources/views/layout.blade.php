<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{app()->getLocale()}}" xml:lang="{{config('app.locale')}}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <title>@yield('documentTitle')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    @switch(config('translation-manager.template'))
        @case('bootstrap3')
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
                  integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu"
                  crossorigin="anonymous">
        @break
        @case('bootstrap4')
            <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
                  integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN"
                  crossorigin="anonymous">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css"
                  integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn"
                  crossorigin="anonymous">
        @break
        @case('bootstrap5')
            <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
                  integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN"
                  crossorigin="anonymous">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
                  integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
                  crossorigin="anonymous">
        @break
        @case('tailwind3')
            <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
            <style>
                .form-floating {
                    position: relative;
                }
                .form-floating>label {
                    position: absolute;
                    top: 0;
                    left: 0;
                    height: 100%;
                    padding: 1rem 0.75rem;
                    pointer-events: none;
                    border: 1px solid transparent;
                    transform-origin: 0 0;
                    transition: opacity .1s ease-in-out,transform .1s ease-in-out;
                }
                .form-floating>.form-control:focus {
                    padding-top: 1.625rem;
                    padding-bottom: 0.625rem;
                }
                .form-floating>.form-control:focus~label {
                    opacity: .65;
                    transform: scale(.85) translateY(-0.5rem) translateX(0.15rem);
                }
            </style>
            <script>
                tailwind.config = {
                    theme: {
                        extend: {
                            colors: {
                                primary: {50:"#fafafa",100:"#f5f5f5",200:"#e5e5e5",300:"#d4d4d4",400:"#a3a3a3",500:"#737373",600:"#525252",700:"#404040",800:"#262626",900:"#171717"},
                            }
                        }
                    }
                }
            </script>
        @break
        @default
    @endswitch
    @stack('styles')
</head>
<body>
<div class="container body-wrapper mx-auto">
    <h1 class="my-3 h2 text-3xl font-medium">@yield('documentTitle')</h1>
    @stack('notifications')
    <main id="content-wrapper" class="mb-6">
        @yield('content')

        @if ($paginationEnabled) {!! $translations->links()->toHtml() !!} @endif
    </main>
</div>
<span class="javascripts">
        @switch(config('translation-manager.template'))
            @case('bootstrap3')
                <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
                <!-- Latest compiled and minified JavaScript -->
                <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"
                        integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd"
                        crossorigin="anonymous"></script>
            @break
            @case('bootstrap4')
                <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"
                        integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK"
                        crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"
                        integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF"
                        crossorigin="anonymous"></script>
            @break
            @case('bootstrap5')
                <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"
                        integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK"
                        crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
                        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
                        crossorigin="anonymous"></script>
            @break
            @case('tailwind3')
                <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"
                        integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK"
                        crossorigin="anonymous"></script>
            @break
            @default
        @endswitch
        @stack('scripts')
    </span>
</body>
</html>


