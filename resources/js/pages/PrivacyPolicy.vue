<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

defineProps({
    content: String
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: route('dashboard')
    },
    {
        title: 'Datenschutzerklärung',
        href: (usePage().props as any).ziggy.location.location
    }
];
</script>

<template>
    <Head>
        <title>Datenschutzerklärung - pr0texter</title>
        <meta content="Datenschutzerklärung für pr0texter" name="description" />
    </Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="markdown prose mx-auto max-w-5xl flex-grow py-6"
            v-html="content"></div>
    </AppLayout>
</template>

<style scoped>
.markdown.prose * {
    color: white !important;
}

.markdown.prose
:where(ul > li):not(
    :where([class~='not-prose'], [class~='not-prose'] *)
  )::marker {
    color: white !important;
}
</style>
