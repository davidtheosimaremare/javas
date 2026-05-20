<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite(['resources/css/app.scss', 'resources/js/app.js']) 
    @php
        $favicon = isset($page['props']['globalData']['profile']['favicon']) && $page['props']['globalData']['profile']['favicon']
            ? asset('storage/' . $page['props']['globalData']['profile']['favicon'])
            : asset('images/logo.png');
    @endphp
    <link rel="icon" href="{{ $favicon }}" type="image/png" />
    @inertiaHead
  </head>
  <body>
    @inertia
  </body>
</html>