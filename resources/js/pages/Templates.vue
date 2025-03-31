<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

defineProps({
    templates: {
        type: Array as () => {
            title: string,
            description: string,
            slug: string,
        }[],
        required: true,
    },
})

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Neuen Post erstellen',
        href: '/',
    },
    {
        title: 'Template auswählen',
        href: '/',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <Link :href="route('post.create', template.slug)" v-for="(template, idx) in templates" :key="idx" class="relative p-4 aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <h1 class="font-semibold text-xl">{{template.title}}</h1>
                    <p class="font-medium text-lg">
                        {{template.description}}
                    </p>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
