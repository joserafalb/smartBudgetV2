<title>@yield('title')</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="{{ asset('boomingup/code-helper/css/app.css') }}" rel="stylesheet">
<script src="{{ asset('boomingup/code-helper/js/app.js') }}"></script>
@stack('head-css')
@stack('head-scripts')
