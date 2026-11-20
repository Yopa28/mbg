
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'MBG') }}</title>
    <link rel="icon" type="image/png" sizes="16x16" href="/images/bgn-seal-16.png?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/bgn-seal-32.png?v=2">
    <link rel="icon" type="image/png" sizes="64x64" href="/images/bgn-seal-64.png?v=2">
    <link rel="shortcut icon" type="image/png" href="/images/bgn-seal-favicon.png?v=2">
    <link rel="apple-touch-icon" sizes="256x256" href="/images/bgn-seal-favicon.png?v=2">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
