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
                'title' => 'Standard',
                'description' => 'Standard template',
                'view' => 'standard',
            ],
        ];

        foreach ($templates as $template) {
            Template::updateOrCreate([
                'title' => $template['title'],
            ], $template);
        }
    }
}
