<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Voting Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100">
@include('layouts.header')

<main class="container-fluid">
    <div>
        @if(session()->has('success'))
            <div class="alert alert-success w-50 mx-auto mt-3">
                {{ session('success') }}
            </div>
        @endif
    </div>

    <div>
        @if(session()->has('error'))
            <div class="alert alert-danger w-50 mx-auto mt-3">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <div id="status-notification"
         class="alert alert-danger w-50 ms-auto top-10 end-0 opacity-75 sticky position-absolute mt-3 z-3 d-none">
        hello
    </div>

    @yield('content')
</main>

@include('layouts.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>
<script>
    @auth
    const currentUserId = {{ auth()->user()->id }};
    @endauth
</script>
</body>

</html>
