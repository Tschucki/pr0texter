<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import Editor from '@/components/Editor.vue';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import { z } from 'zod';
import { Loader2 } from 'lucide-vue-next';
import { FormControl, FormField, FormItem, FormMessage } from '@/components/ui/form';
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import _ from 'lodash';
import axios from 'axios';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import html2canvas from 'html2canvas-pro';

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


const editorContent = ref('<p>Der neue pr0texter für das pr0gramm</p>');
const loading = ref(false);
const previewImage = ref('');
const fileSize = ref('');
const autoGeneratePreview = ref(true);
const useLocalGeneration = ref(true);
const localPreviewRef = ref(null);


const formSchema = toTypedSchema(z.object({
    content: z.string().min(0).nullish().nullable().default('')
}));

const form = useForm({
    validationSchema: formSchema
});

const throttledGetPreview = _.debounce(() => {
    if (useLocalGeneration.value) {
        // we need to wait a bit until the dom is updated
        setTimeout(() => {
            getPreviewLocal();
        }, 500);
        return;
    }
    getPreview();
}, 500);

const abortController = ref<AbortController | null>(null);
const resize = ref<boolean>(true);

const abortPreviousRequest = () => {
    if (abortController.value) {
        abortController.value.abort();
    }
    abortController.value = new AbortController();
};

const getPreviewLocal = () => {
    loading.value = true;

    try {
        if (localPreviewRef.value) {
            const estimatedHeight = (localPreviewRef.value as HTMLElement).offsetHeight;
            html2canvas(localPreviewRef.value, {
                backgroundColor: '#161618',
                width: resize.value ? 1052 : undefined,
                height: estimatedHeight,
                useCORS: true,
                allowTaint: true,
                logging: false,
            }).then(canvas => {
                if (resize.value) {
                    const tmpCanvas = document.createElement('canvas');
                    tmpCanvas.width = 1052;
                    tmpCanvas.height = estimatedHeight;
                    const ctx = tmpCanvas.getContext('2d');

                    if (!ctx) {
                        throw new Error('Could not get canvas context');
                    }

                    ctx.drawImage(canvas, 0, 0, canvas.width, canvas.height, 0, 0, tmpCanvas.width, tmpCanvas.height);

                    canvas = tmpCanvas;
                }

                canvas.toBlob(async blob => {
                    if (blob) {
                        const size = blob.size;
                        if (size < 1024) {
                            fileSize.value = `${size} B`;
                        } else if (size < 1024 * 1024) {
                            fileSize.value = `${(size / 1024).toFixed(2)} KB`;
                        } else {
                            fileSize.value = `${(size / (1024 * 1024)).toFixed(2)} MB`;
                        }

                        URL.revokeObjectURL(previewImage.value);

                        previewImage.value = URL.createObjectURL(blob);
                    }
                    loading.value = false;
                }, 'image/png', 1);
            }).catch(error => {
                console.log('error while generating canvas', error);
                loading.value = false;
            });
        }
    } catch (error) {
        console.log(error);
        loading.value = false;

    }
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
                template: props.template.slug,
                resize: resize.value
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
    if (autoGeneratePreview.value === false) {
        return;
    }
    console.log('form changed, generating preview');
    throttledGetPreview();
});

watch(editorContent, (newContent) => {
    localStorage.setItem(storageKey.value, newContent);
    if (autoGeneratePreview.value === false) {
        return;
    }
    console.log('editor content changed, generating preview');
    throttledGetPreview();
});

watch(resize, () => {
    throttledGetPreview();
});

onMounted(() => {
    const savedContent = localStorage.getItem(storageKey.value);
    if (savedContent) {
        editorContent.value = savedContent;
    }
    if ('mounted getPreview');
    throttledGetPreview();
});

onBeforeUnmount(() => {
    if (previewImage.value) {
        URL.revokeObjectURL(previewImage.value);
    }
});

const downloadImage = () => {
    if (previewImage.value) {
        const link = document.createElement('a');
        link.href = previewImage.value;
        link.download = `preview_${props.template.slug}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
};

</script>

<template>
    <Head>
        <title>Post erstellen - Template {{props.template.title}}</title>
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
                    <div class="w-full items-center flex justify-between gap-4 max-w-[1052px]">
                        <div class="flex items-center gap-2">
                            <Label for="local-generation">Lokal generieren</Label>
                            <Switch id="local-generation" v-model="useLocalGeneration" @change="throttledGetPreview" />
                        </div>
                        <div class="flex items-center gap-2">
                            <Label for="resize">Vorschau automatisch generieren</Label>
                            <Switch id="resize" v-model="autoGeneratePreview" />
                        </div>
                        <div class="flex items-center gap-2">
                            <Label for="resize">Maximale Breite 1052px</Label>
                            <Switch id="resize" v-model="resize" />
                        </div>
                    </div>

                    <Button @click.prevent="throttledGetPreview" type="button"
                            variant="default"
                            class="my-4 w-full max-w-[1052px]">
                        <Loader2 v-if="loading" class="animate-spin text-4xl aspect-square"></Loader2>
                        <span v-else>Aktuelle Vorschau generieren</span>
                    </Button>

                    <Button v-if="!loading" @click.prevent="downloadImage" :disabled="loading" type="button"
                            variant="default"
                            class="my-4 w-full max-w-[1052px]">
                        <Loader2 v-if="loading" class="animate-spin text-4xl aspect-square"></Loader2>
                        <span v-else>Bild herunterladen</span>
                    </Button>

                    <div v-if="useLocalGeneration" ref="localPreviewRef"
                         class="!absolute -left-[99999px] antialiased bg-[#161618] prose dark:prose-invert py-4 px-2
            text-[#f2f5f4] ProseMirror !prose-2xl !leading-tight prose-invert-[hsl(--border)]
            prose-p:text-2xl prose-p:leading-tight prose-h1:text-6xl prose-h2:text-5xl prose-h3:text-4xl
            prose-headings:font-bold prose-headings:text-[hsl(--border)] prose-p:text-[hsl(--border)]
            prose-a:text-[hsl(--border)] prose-a:opacity-90 prose-a:hover:opacity-100
            prose-a:underline-offset-4 prose-strong:text-[hsl(--border)]
            prose-em:text-[hsl(--border)]/90 prose-code:text-[hsl(--border)]
            prose-code:bg-white/10 prose-code:rounded-md prose-code:px-1.5 prose-code:py-0.5
            prose-blockquote:text-[hsl(--border)]/80 prose-blockquote:border-l-[hsl(--border)]/30
            prose-hr:border-[hsl(--border)]/20 prose-img:rounded-2xl max-w-[1052px] w-full"
                         v-html="editorContent">
                    </div>
                    <div v-if="loading" class="h-[30dvh] flex items-center justify-center gap-4 flex-col">
                        <Loader2 class="animate-spin text-4xl aspect-square"></Loader2>
                        <p>
                            Vorschau wird geladen...
                        </p>
                    </div>
                    <div v-else>
                        <div v-if="fileSize" class="text-sm text-gray-500 mb-2 flex justify-between flex-wrap">
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
                <div>
                    Mit diesem Tool kannst du einfach und schnell Posts für pr0gramm erstellen.
                    Ich habe geplant, mehrere Templates zu erstellen, aus denen du wählen kannst. Bspw. soll es ein
                    zukünftig ein Template geben, bei dem du ein Hintergrundbild auswählen kannst, das dann hinter dem
                    Post dargestellt wird.<br>
                    Vorerst gibt es allerdings nur das Standard-Template, das du hier siehst.
                    <br />
                    <br />
                    Du kannst den Inhalt direkt im Editor bearbeiten und die Vorschau wird automatisch aktualisiert.
                    <br />
                    <br />
                    Folgende Features sind verfügbar:
                    <ul class="list-disc pl-5">
                        <li>Textformatierung (Fett, Kursiv, Unterstrichen)</li>
                        <li>Listen erstellen (nummeriert und unnummeriert)</li>
                        <li>Schriftarten wechseln</li>
                        <li>Codeblöcke einfügen</li>
                        <li>Hochladen von Bildern</li>
                        <li>Automatische Vorschau des Posts</li>
                        <li>Download der Vorschau als Bild</li>
                        <li>Überschriften und Absätze formatieren</li>
                        <li>pr0gramm-Farbpalette für den Editor</li>
                        <li>Automatisches Speichern des Inhalts im Browser</li>
                        <li>Tabellen erstellen</li>
                        <li>Zitate</li>
                        <li>Horizontal-Linien</li>
                        <li>Undo/Redo-Funktion</li>
                    </ul>
                    <br>
                    Deine hochgeladenen Bilder werden automatisch nach 2 Stunden vom Server gelöscht.<br />
                    Das Bild wird serverseitig generiert (also nicht clientseitig im Browser).<br />
                </div>
                <h2 class="scroll-m-20 text-2xl font-semibold tracking-tight">
                    Wie funktioniert das Tool?
                </h2>
                <div>
                    Das Projekt ist Open Source und du kannst dir den Code auf <a
                    href="https://github.com/Tschucki/pr0texter" target="_blank">GitHub</a> ansehen.<br><br>
                    Den Editor, den du hier siehst, ist ein <a href="https://tiptap.dev/"
                                                               target="_blank">TipTap-Editor</a>, mit einigen
                    pr0gramm-spezifischen Extensions.
                    Der Editor generiert den Post als HTML, das dann an den Server gesendet wird.<br>
                    Auf dem Server wird dann das HTML in das vorgesehene Template geladen und <a
                    href="https://pptr.dev/" target="_blank">Puppeteer</a> macht einen Screenshot und der Server sendet
                    das Bild zurück.
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
