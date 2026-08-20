<script setup>
import { useToast } from '../stores/toast';

const { state, dismiss } = useToast();
</script>

<template>
    <div class="pointer-events-none fixed bottom-6 right-6 z-[100] flex flex-col gap-3">
        <TransitionGroup name="toast">
            <div
                v-for="toast in state.toasts"
                :key="toast.id"
                class="pointer-events-auto flex w-[26rem] items-start gap-3.5 rounded-lg border border-border bg-card p-5 shadow-xl"
            >
                <svg
                    v-if="toast.type === 'error'"
                    viewBox="0 0 20 20"
                    fill="none"
                    class="mt-0.5 h-6 w-6 shrink-0 text-red"
                >
                    <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.6" />
                    <path d="M10 6v5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                    <circle cx="10" cy="13.5" r="0.9" fill="currentColor" />
                </svg>
                <svg
                    v-else
                    viewBox="0 0 20 20"
                    fill="none"
                    class="mt-0.5 h-6 w-6 shrink-0 text-green"
                >
                    <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.6" />
                    <path d="M6.5 10.2l2.3 2.3 4.7-4.9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                <span class="flex-1 pt-0.5 text-sm leading-snug text-ink">{{ toast.message }}</span>

                <button type="button" class="text-muted hover:text-ink" @click="dismiss(toast.id)">
                    <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4">
                        <path d="M5 5l10 10M15 5L5 15" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                    </svg>
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateX(16px);
}
</style>
