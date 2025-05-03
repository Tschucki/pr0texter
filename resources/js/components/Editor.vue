<script setup lang="ts">
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import TextAlign from '@tiptap/extension-text-align';
import ResizeImage from 'tiptap-extension-resize-image';
import { FontFamily } from '@tiptap/extension-font-family';
import TextStyle from '@tiptap/extension-text-style';
import Color from '@tiptap/extension-color';
import CodeBlock from '@tiptap/extension-code-block';
import { BubbleMenu, Editor as TiptapEditor, EditorContent } from '@tiptap/vue-3';
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import axios from 'axios';
import {
    AlignCenter,
    AlignJustify,
    AlignLeft,
    AlignRight,
    Bold,
    Code,
    Heading1,
    Heading2,
    Heading3,
    ImageIcon,
    Italic,
    List,
    ListOrdered,
    Minus,
    Palette,
    PilcrowSquare,
    Quote,
    Redo,
    Strikethrough,
    Undo,
    X
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import { HoverCard, HoverCardContent, HoverCardTrigger } from '@/components/ui/hover-card';
import { FontSize } from '@/extensions/font-size';

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['update:modelValue']);

const editor = ref<TiptapEditor | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

const colorOptions = [
    { name: 'Standard-Schriftfarbe', value: '#f2f5f4' },
    { name: 'Gebannt', value: '#444444' },
    { name: 'Fliesentisch', value: '#6c432b' },
    { name: 'Neuschwuchtel', value: '#e108e9' },
    { name: 'Schwuchtel', value: '#ffffff' },
    { name: 'Edler Spender', value: '#1cb992' },
    { name: 'Altschwuchtel', value: '#5bb91c' },
    { name: 'Mittelaltschwuchtel', value: '#addc8d' },
    { name: 'Wichtel', value: '#c52b2f' },
    { name: 'Moderator', value: '#008fff' },
    { name: 'Alt-Moderator', value: '#7fc7ff' },
    { name: 'Administrator', value: '#ff9900' },
    { name: 'System-Bot', value: '#ffc166' },
    { name: 'Nutzer-Bot', value: '#10366f' },
    { name: 'Lebende Legende', value: '#1cb992' },
    { name: 'Community-Helfer', value: '#c52b2f' },
    { name: 'Alt-Helfer', value: '#ea9fa1' },
    { name: 'Richtiges Grau', value: '#161618' },
    { name: 'pr0gramm Orange', value: '#ee4d2e' },
    { name: 'pr0gramm-Logo-Orange', value: '#d23c22' },
    { name: 'Standard-Schriftfarbe (ausgegraut)', value: '#888888' },
    { name: 'Linkfarbe', value: '#75c0c7' },
    { name: '"IRGENDWAS DOOFES IST PASSIERT"-Farbe', value: '#fc8833' },
    { name: 'Alternative Hintergrundfarbe (cha0s-Grau)', value: '#212121' },
    { name: 'Gelb vom pr0mium-Post', value: '#f7c516' },
    { name: 'SFW-Grün', value: '#5cb85c' },
    { name: 'NSFW-Orange', value: '#f0ad4e' },
    { name: 'NSFL-Rot', value: '#d9534f' },
    { name: 'pr0moim-Auswahlteile', value: '#222222' },
    { name: 'Ehemalige Hintergrundfarbe der Kommentare', value: '#2a2e31' },
    { name: 'Altes pr0gramm', value: '#ff0082' },
    { name: 'bewährtes Orange', value: '#ee4d2e' },
    { name: 'angenehmes Grün', value: '#1db992' },
    { name: 'Olivgrün des Friedens', value: '#bfbc06' },
    { name: 'mega episches Blau', value: '#008fff' }
];

const fontSizeOptions = [
    { name: 'Klein', value: '1rem' },
    { name: 'Normal', value: '1.5rem' },
    { name: 'Groß', value: '3rem' },
    { name: 'Sehr groß', value: '5rem' },
    { name: 'Riesig', value: '6.5rem' }
];

const fontFamilyOptions = [
    { name: 'Sans Serif', value: 'sans-serif' },
    { name: 'Inter', value: 'Inter, sans-serif' },
    { name: 'Montserrat', value: 'Montserrat, sans-serif' },
    { name: 'Futura', value: 'Futura, sans-serif' },
    { name: 'Courier New', value: '"Courier New", Courier, monospace' },
    { name: 'Georgia', value: 'Georgia, serif' },
    { name: 'Times New Roman', value: '"Times New Roman", Times, serif' },
    { name: 'Verdana', value: 'Verdana, sans-serif' }
];

watch(() => props.modelValue, (value) => {
    if (editor.value && editor.value.getHTML() !== value) {
        editor.value.commands.setContent(value, false);
    }
});

function handleImageUpload(event: Event) {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (!file || !editor.value) return;

    const formData = new FormData();
    formData.append('image', file);

    axios.post(route('image.upload'), formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
        .then(response => {
            editor.value?.chain().focus().setImage({ src: response.data.url, alt: file.name }).run();
        })
        .catch(error => {
            console.error('Upload failed:', error);
        });

    if (fileInput.value) {
        fileInput.value.value = '';
    }
}

onMounted(() => {
    editor.value = new TiptapEditor({
        extensions: [
            StarterKit,
            TextAlign.configure({
                types: ['heading', 'paragraph', 'image']
            }),
            Image.configure({
                HTMLAttributes: {
                    class: 'rounded-lg'
                },
                allowBase64: true
            }),
            ResizeImage,
            TextStyle.configure({ mergeNestedSpanStyles: true }),
            FontFamily,
            FontSize,
            CodeBlock,
            Color
        ],
        content: props.modelValue,
        editorProps: {
            attributes: {
                class: 'prose !prose-invert text-[#f2f5f4] max-w-none focus:outline-none px-4 py-3 !prose-2xl min-h-[200px] prose-h1:text-6xl prose-h2:text-5xl prose-h3:text-4xl !leading-tight prose-p:leading-tight',
                spellcheck: 'true'
            }
        },
        onUpdate: ({ editor }) => {
            emit('update:modelValue', editor.getHTML());
        },
        autofocus: 'start',
        onContentError: (error) => {
            console.error('Content error:', error);
        }
    });
});

onBeforeUnmount(() => {
    if (editor.value) {
        editor.value.destroy();
    }
});

function addImage() {
    fileInput.value?.click();
}

function setColor(color: string) {
    if (editor.value) {
        editor.value.chain().focus().setColor(color).run();
    }
}

function setFontSize(size: string) {
    if (editor.value) {
        editor.value.chain().focus().setFontSize(`${size}`).run();
    }
}

function setFontFamily(family: string) {
    if (editor.value) {
        editor.value.chain().focus().setFontFamily(family).run();
    }
}
</script>

<template>
    <div class="border rounded-lg overflow-clip bg-background">
        <div v-if="editor" class="border-b px-1 py-4 flex flex-wrap gap-1 sticky top-0 bg-background z-10">
            <div class="flex-shrink-0 flex gap-1">
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive('bold') }"
                    @click="editor.chain().focus().toggleBold().run()"
                >
                    <Bold class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive('italic') }"
                    @click="editor.chain().focus().toggleItalic().run()"
                >
                    <Italic class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive('strike') }"
                    @click="editor.chain().focus().toggleStrike().run()"
                >
                    <Strikethrough class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive('codeBlock') }"
                    @click="editor.chain().focus().toggleCodeBlock().run()"
                >
                    <Code class="size-4" />
                </Button>
            </div>

            <Separator orientation="vertical" class="h-8 mx-1" />

            <div class="flex-shrink-0 flex gap-1">
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive('paragraph') }"
                    @click="editor.chain().focus().setParagraph().run()"
                >
                    <PilcrowSquare class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive('heading', { level: 1 }) }"
                    @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
                >
                    <Heading1 class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive('heading', { level: 2 }) }"
                    @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                >
                    <Heading2 class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive('heading', { level: 3 }) }"
                    @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                >
                    <Heading3 class="size-4" />
                </Button>
            </div>

            <Separator orientation="vertical" class="h-8 mx-1" />

            <div class="flex-shrink-0 flex gap-1">
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="ghost" size="sm">
                            <Palette class="size-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <div class="grid grid-cols-6 gap-2 p-2 w-auto max-h-[40dvh] overflow-y-auto">
                            <DropdownMenuItem
                                v-for="color in colorOptions"
                                :key="color.value"
                                @click="setColor(color.value)"
                                class="flex flex-col items-center p-1 cursor-pointer"
                            >
                                <HoverCard :open-delay="200">
                                    <HoverCardTrigger>
                                        <div class="w-8 h-8 rounded-full border"
                                             :style="{ backgroundColor: color.value }"></div>
                                    </HoverCardTrigger>
                                    <HoverCardContent>
                                        <span>
                                            {{ color.name }}
                                        </span>
                                    </HoverCardContent>
                                </HoverCard>
                            </DropdownMenuItem>
                        </div>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <div class="flex-shrink-0 flex gap-1">
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="ghost" size="sm">
                            <span class="size-4">A</span>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <div class="grid grid-cols-1 gap-2 p-2 w-auto">
                            <DropdownMenuItem
                                v-for="size in fontSizeOptions"
                                :key="size.value"
                                @click="setFontSize(size.value)"
                                class="flex flex-col items-center p-1 cursor-pointer"
                            >
                                <span>{{ size.name }}</span>
                            </DropdownMenuItem>
                        </div>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <div class="flex-shrink-0 flex gap-1">
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="ghost" size="sm">
                            <span class="size-4">F</span>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <div class="grid grid-cols-1 gap-2 p-2 w-auto">
                            <DropdownMenuItem
                                v-for="family in fontFamilyOptions"
                                :key="family.value"
                                @click="setFontFamily(family.value)"
                                class="flex flex-col items-center p-1 cursor-pointer"
                            >
                                <span :style="{ fontFamily: family.value }">{{ family.name }}</span>
                            </DropdownMenuItem>
                        </div>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <div class="flex-shrink-0 flex gap-1">
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive({ textAlign: 'left' }) }"
                    @click="editor.chain().focus().setTextAlign('left').run()"
                >
                    <AlignLeft class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive({ textAlign: 'center' }) }"
                    @click="editor.chain().focus().setTextAlign('center').run()"
                >
                    <AlignCenter class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive({ textAlign: 'right' }) }"
                    @click="editor.chain().focus().setTextAlign('right').run()"
                >
                    <AlignRight class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive({ textAlign: 'justify' }) }"
                    @click="editor.chain().focus().setTextAlign('justify').run()"
                >
                    <AlignJustify class="size-4" />
                </Button>
            </div>

            <Separator orientation="vertical" class="h-8 mx-1" />

            <div class="flex-shrink-0 flex gap-1">
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive('bulletList') }"
                    @click="editor.chain().focus().toggleBulletList().run()"
                >
                    <List class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive('orderedList') }"
                    @click="editor.chain().focus().toggleOrderedList().run()"
                >
                    <ListOrdered class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive('blockquote') }"
                    @click="editor.chain().focus().toggleBlockquote().run()"
                >
                    <Quote class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    @click="editor.chain().focus().setHorizontalRule().run()"
                >
                    <Minus class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    @click="addImage"
                >
                    <ImageIcon class="size-4" />
                </Button>
                <input
                    type="file"
                    ref="fileInput"
                    @change="handleImageUpload"
                    accept="image/*"
                    class="hidden"
                />
            </div>

            <div class="flex-grow"></div>

            <div class="flex gap-1">
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    @click="editor.chain().focus().undo().run()"
                >
                    <Undo class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    @click="editor.chain().focus().redo().run()"
                >
                    <Redo class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    @click="editor.chain().focus().clearNodes().run(); editor.chain().focus().unsetAllMarks().run()"
                >
                    <X class="size-4" />
                </Button>
            </div>
        </div>
        <BubbleMenu v-if="editor" :editor="editor" :tippyOptions="{ duration: 500, placement: 'bottom' }">
            <div class="flex items-center gap-1 p-1 rounded-lg bg-background border shadow-lg">
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive('bold') }"
                    @click="editor.chain().focus().toggleBold().run()"
                >
                    <Bold class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive('paragraph') }"
                    @click="editor.chain().focus().setParagraph().run()"
                >
                    <PilcrowSquare class="size-4" />
                </Button>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    :class="{ 'bg-accent': editor.isActive('heading', { level: 1 }) }"
                    @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
                >
                    <Heading1 class="size-4" />
                </Button>
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="ghost" size="sm">
                            <Palette class="size-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <div class="grid grid-cols-6 gap-2 p-2 w-auto max-h-[40dvh] overflow-y-auto">
                            <DropdownMenuItem
                                v-for="color in colorOptions"
                                :key="color.value"
                                @click="setColor(color.value)"
                                class="flex flex-col items-center p-1 cursor-pointer"
                            >
                                <HoverCard :open-delay="200">
                                    <HoverCardTrigger>
                                        <div class="w-8 h-8 rounded-full border"
                                             :style="{ backgroundColor: color.value }"></div>
                                    </HoverCardTrigger>
                                    <HoverCardContent>
                                        <span>
                                            {{ color.name }}
                                        </span>
                                    </HoverCardContent>
                                </HoverCard>
                            </DropdownMenuItem>
                        </div>
                    </DropdownMenuContent>
                </DropdownMenu>
                <Button
                    variant="ghost"
                    type="button"
                    size="sm"
                    @click="addImage"
                >
                    <ImageIcon class="size-4" />
                </Button>
            </div>
        </BubbleMenu>

        <EditorContent :editor="editor" />
    </div>
</template>

<style>
</style>
