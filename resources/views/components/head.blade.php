<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{asset('favicon/site.webmanifest') }}">
    <link rel="mask-icon" href="{{asset('favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>CORIM</title>
</head>
<body x-data="{ isLoading: true }" x-init="setTimeout(() => isLoading = false, 500)">

        <!-- Spinner Loader -->
        <div id="loadingSpinner" class="loading-spinner" x-show="isLoading">
            <div class="spinner-border text-primary" role="status">
            </div>
        </div>

    <!-- Actual Content (Slot) -->
    <div x-show="!isLoading" style="display: none;">
        {{$slot}}
        <x-flash-message />
    </div>
</body>
</html>
