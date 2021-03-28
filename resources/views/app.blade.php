<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EVE Tools</title>
    <script>
        const BASE_URL = '/';

        window.__characters = {!! json_encode($characters) !!}
        window.__locations = {!! json_encode($locations) !!};

        if (!localStorage.getItem('current_location_id')) {
            localStorage.setItem('current_location_id', window.__locations.find(l => !l.is_trading_hub).id);
        }
    </script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">
</head>
<body id="kt_body" class="header-fixed header-mobile-fixed aside-enabled aside-fixed">
    <div id="app"></div>

    <script src="{{ mix('/js/main.js') }}"></script>
</body>
</html>
