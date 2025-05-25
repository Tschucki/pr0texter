<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import Editor from '@/components/Editor.vue';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import {Head} from '@inertiajs/vue3';
import { z } from 'zod';
import { Loader2 } from 'lucide-vue-next';
import { FormControl, FormField, FormItem, FormMessage } from '@/components/ui/form';
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import _ from 'lodash';
import axios from 'axios';

const props = defineProps({
    template: {
        type: Object as () => {
            title: string,
            slug: string,
            description: string,
            to: string,
        },
        required: true
    }
});

const breadcrumbs: BreadcrumbItem[] = [
 /*   {
        title: 'Neuen Post erstellen',
        href: '/'
    },
    {
        title: 'Template auswählen',
        href: '/'
    },*/
    {
        title: 'Template ' + props.template.title,
        href: (usePage().props as any).ziggy.location.location
    }
];


const editorContent = ref('<h1>Der neue pr0texter für das pr0gramm</h1>');
const loading = ref(false);
const previewImage = ref('');
const fileSize = ref('');


const formSchema = toTypedSchema(z.object({
    content: z.string().min(0).nullish().nullable().default('')
}));

const form = useForm({
    validationSchema: formSchema
});

const throttledGetPreview = _.throttle(() => {
    getPreview();
}, 500);

const abortController = ref<AbortController | null>(null);

const abortPreviousRequest = () => {
    if (abortController.value) {
        abortController.value.abort();
    }
    abortController.value = new AbortController();
};

const getPreview = () => {
    loading.value = true;

    if (previewImage.value) {
        URL.revokeObjectURL(previewImage.value);
    }

    abortPreviousRequest();

    try {
        if (abortController.value) {
            axios.post(route('preview', {
                template: props.template.slug
            }), {
                tipTapContent: editorContent.value
            }, {
                responseType: 'blob',
                signal: abortController.value.signal
            }).then((response) => {
                // Calculate file size
                const size = response.data.size;
                if (size < 1024) {
                    fileSize.value = `${size} B`;
                } else if (size < 1024 * 1024) {
                    fileSize.value = `${(size / 1024).toFixed(2)} KB`;
                } else {
                    fileSize.value = `${(size / (1024 * 1024)).toFixed(2)} MB`;
                }

                previewImage.value = URL.createObjectURL(response.data);
                loading.value = false;
            }).catch((error) => {
                if (!axios.isCancel(error)) {
                    console.log(error);
                }
            });
        }
    } catch (error) {
        console.log(error);
    }
};

const storageKey = ref(`editor_content_${props.template.slug}`);

watch(form.values, () => {
    throttledGetPreview();
});

watch(editorContent, (newContent) => {
    localStorage.setItem(storageKey.value, newContent);
    throttledGetPreview();
});

onMounted(() => {
    throttledGetPreview();
    const savedContent = localStorage.getItem(storageKey.value);
    if (savedContent) {
        editorContent.value = savedContent;
    }
    throttledGetPreview();
});

onBeforeUnmount(() => {
    if (previewImage.value) {
        URL.revokeObjectURL(previewImage.value);
    }
});

</script>

<template>
    <Head>
        <title>Post erstellen - Template {{usePage().props.template.title}}</title>
    </Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <form
                class="relative p-4 overflow-clip rounded-xl border border-sidebar-border/70 dark:border-sidebar-border space-y-4">
                <h1 class="scroll-m-20 text-2xl font-semibold tracking-tight">
                    Post erstellen
                </h1>
                <FormField name="content" class="">
                    <FormItem class="flex justify-center">
                        <FormControl class="max-w-[1052px] w-full h-full">
                            <Editor v-model="editorContent" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </form>
            <div
                class="relative p-4 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border space-y-4">
                <h1 class="scroll-m-20 text-2xl font-semibold tracking-tight">
                    Vorschau
                </h1>
                <div class="justify-center flex flex-col items-center">
                    <div v-if="loading" class="h-[30dvh] flex items-center justify-center gap-4 flex-col">
                        <Loader2 class="animate-spin text-4xl aspect-square"></Loader2>
                        <p>
                            Vorschau wird geladen...
                        </p>
                    </div>
                    <div v-else>
                        <div v-if="fileSize" class="text-sm text-gray-500 mb-2">
                            Bildgröße: {{ fileSize }} (Max: 20MB für pr0gramm)
                        </div>
                        <img v-if="previewImage" :src="previewImage" alt="Preview"
                             class="max-w-[1052px] w-full h-full object-cover" />
                    </div>
                </div>
            </div>
            <div
                class="relative p-4 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border space-y-4">
                <h1 class="scroll-m-20 text-2xl font-semibold tracking-tight">
                    Ein neuer pr0texter für das pr0gramm
                </h1>
                <div class="flex flex-col">
                    Mit diesem Tool kannst du einfach und schnell Posts für pr0gramm erstellen.
                    <br />
                    Du kannst den Inhalt direkt im Editor bearbeiten und die Vorschau wird automatisch aktualisiert.
                    <br />
                    <br />
                    Deine hochgeladenen Bilder werden automatisch nach 2 Stunden vom Server gelöscht.
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.skeleton-loader {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
}

@keyframes skeleton-loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}
</style>
