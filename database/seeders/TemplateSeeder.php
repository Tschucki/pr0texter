<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'title' => 'Modern',
                'description' => 'A modern template',
                'icon' => 'modern-icon',
                'view' => 'modern',
            ],
        ];

        foreach ($templates as $template) {
            Template::updateOrCreate([
                'title' => $template['title'],
            ], $template);
        }
    }
}
