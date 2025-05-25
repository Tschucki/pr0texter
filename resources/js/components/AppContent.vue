<script setup lang="ts">
import { SidebarInset } from '@/components/ui/sidebar';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';

interface Props {
    variant?: 'header' | 'sidebar';
    class?: string;
}

const props = defineProps<Props>();
const className = computed(() => props.class);
</script>

<template>
    <SidebarInset v-if="props.variant === 'sidebar'" :class="className">
        <slot />
        <div class="group fixed bottom-5 right-5 z-20">
            <h4
                class="mb-2 cursor-default text-center text-4xl font-extrabold tracking-wide text-gray-200 transition-colors duration-200 group-hover:text-primary">
                BETA
            </h4>
            <a
                target="_blank"
                href="https://pr0gramm.com/inbox/messages/PimmelmannJones"
                title="Feedback senden">
                <Button icon="heroicon-o-bug-ant">Feedback senden</Button>
            </a>
        </div>
        <footer>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="py-8 text-center">
                    <nav aria-label="quick links" class="mt-10 text-sm">
                        <div class="-my-1 flex flex-wrap justify-center gap-2 lg:gap-6">
                            <a
                                target="_blank"
                                class="inline-block rounded-lg px-2 py-1 text-sm hover:text-primary"
                                href="https://github.com/Tschucki/pr0texter"
                            >GitHub
                            </a>
                            <Link
                                class="inline-block rounded-lg px-2 py-1 text-sm hover:text-primary"
                                :href="route('legal-notice')"
                            >Impressum
                            </Link>
                            <Link
                                class="inline-block rounded-lg px-2 py-1 text-sm hover:text-primary"
                                :href="route('privacy-policy')"
                            >Datenschutz
                            </Link>
                        </div>
                    </nav>
                </div>
                <div
                    class="flex flex-col items-center border-t border-muted py-10 sm:flex-row-reverse sm:justify-between">
                    <a :href="route('dashboard')" target="_blank" class="text-sm sm:mt-0"
                    >{{ new Date().getFullYear() }} - pr0texter</a
                    >
                </div>
            </div>
        </footer>
    </SidebarInset>
    <main v-else class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl" :class="className">
        <slot />
    </main>
</template>
