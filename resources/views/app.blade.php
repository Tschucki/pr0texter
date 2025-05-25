<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="preconnect" href="https://plausible.marcelwagner.dev">
        <link href="https://fonts.bunny.net/css?family=Inter:400,500,600" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=Montserrat:400,500,600" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=Futura:400,500,600" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=Courier New:400,500,600" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=Georgia:400,500,600" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=Times New Roman:400,500,600" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=Verdana:400,500,600" rel="stylesheet" />
        <script defer data-domain="pr0texter.pimmelmannjones.com" src="https://plausible.marcelwagner.dev/js/script.js"></script>
        @routes
        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
