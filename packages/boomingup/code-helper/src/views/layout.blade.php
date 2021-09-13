<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    @include('code-helper::layout.head')
</head>

<body>
    <header>
        @include('code-helper::layout.header')
    </header>
    <main>
        <div class="panel bg-gray-100">
            @yield('main')
        </div>
    </main>
    <footer>
        @include('code-helper::layout.footer')
    </footer>
</body>

</html>
