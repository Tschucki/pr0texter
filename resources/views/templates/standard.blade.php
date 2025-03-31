<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=Montserrat:400,500,600" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=Futura:400,500,600" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=Courier New:400,500,600" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=Georgia:400,500,600" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=Times New Roman:400,500,600" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=Verdana:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased bg-[#161618] prose dark:prose-invert py-4 px-2
  prose-headings:font-bold
  prose-headings:text-[#f2f5f4]
  prose-p:text-[#f2f5f4]
  prose-p:text-lg
  prose-a:text-[#f2f5f4]
  prose-a:opacity-90
  prose-a:hover:opacity-100
  prose-a:underline-offset-4
  prose-strong:text-[#f2f5f4]
  prose-em:text-[#f2f5f4]/90
  prose-code:text-[#f2f5f4]
  prose-code:bg-white/10
  prose-code:rounded-md
  prose-code:px-1.5
  prose-code:py-0.5
  prose-blockquote:text-[#f2f5f4]/80
  prose-blockquote:border-l-[#f2f5f4]/30
  prose-hr:border-[#f2f5f4]/20
  prose-img:rounded-2xl
  max-w-full w-full">
{!! $content !!}
</body>
</html>
