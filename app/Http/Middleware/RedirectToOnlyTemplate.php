<?php

namespace App\Http\Middleware;

use App\Models\Template;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToOnlyTemplate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Template::count() === 1 && $request->routeIs('dashboard')) {
            $template = Template::first();
            return redirect()->route('post.create', ['template' => $template->slug]);
        }

        return $next($request);
    }
}
