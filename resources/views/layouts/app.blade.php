<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
@includeIf('layouts.modules._navbar')

@if(session('flash_message') && isset(session('flash_message')['type']) && isset(session('flash_message')['text']))
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert alert-{{ session('flash_message')['type'] }} text-center">
                    {!!  session('flash_message')['text']!!}
                </div>
            </div>
        </div>
    </div>
@endif
<main class="main-padding">
    @yield('content')
</main>

{{--@includeIf('layouts.modules._footer')--}}
<!-- Scripts -->
<script src="{{ asset('js/all.js') }}" defer></script>
<script src="{{ asset('js/app.js') }}" defer></script>
<script type="text/javascript">

</script>

</body>
</html>
