<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Futura:400,500,600" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=Courier New:400,500,600" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=Georgia:400,500,600" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=Times New Roman:400,500,600" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=Verdana:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased bg-[#161618] prose dark:prose-invert py-4 px-2
text-[hsl(--border)]
ProseMirror
!prose-2xl
!leading-tight
prose-invert-[hsl(--border)]
prose-p:text-2xl
prose-p:leading-tight
prose-h1:text-6xl
prose-h2:text-5xl
prose-h3:text-4xl
  prose-headings:font-bold
  prose-headings:text-[hsl(--border)]
  prose-p:text-[hsl(--border)]
  prose-a:text-[hsl(--border)]
  prose-a:opacity-90
  prose-a:hover:opacity-100
  prose-a:underline-offset-4
  prose-strong:text-[hsl(--border)]
  prose-em:text-[hsl(--border)]/90
  prose-code:text-[hsl(--border)]
  prose-code:bg-white/10
  prose-code:rounded-md
  prose-code:px-1.5
  prose-code:py-0.5
  prose-blockquote:text-[hsl(--border)]/80
  prose-blockquote:border-l-[hsl(--border)]/30
  prose-hr:border-[hsl(--border)]/20
  prose-img:rounded-2xl
  max-w-full w-full">
{!! $content !!}
</body>
</html>
