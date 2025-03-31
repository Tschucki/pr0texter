<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TemplateController extends Controller
{
    public function __invoke(Request $request)
    {
        return Inertia::render('Templates', [
            'templates' => Template::all(),
        ]);
    }

    public function create(Template $template): Response
    {
        return Inertia::render('Create', [
            'template' => $template,
        ]);
    }
}
