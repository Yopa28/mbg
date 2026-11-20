
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'MBG') }}</title>
    <link rel="icon" type="image/png" sizes="256x256" href="/images/bgn-seal-favicon.png">
    <link rel="apple-touch-icon" href="/images/bgn-seal-favicon.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
