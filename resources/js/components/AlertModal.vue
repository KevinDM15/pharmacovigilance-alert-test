<script setup>
import { ref } from 'vue';
import api from '../services/api';

const props = defineProps({
    lot: { type: String, required: true },
    orders: { type: Array, required: true },
});

const emit = defineEmits(['close', 'sent']);

const isSending = ref(false);
const error = ref('');

async function confirmSend() {
    isSending.value = true;
    error.value = '';

    try {
        const response = await api.post('/alerts/send', {
            lot: props.lot,
            order_ids: props.orders.map((order) => order.id),
        });

        emit('sent', response.data.message);
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Unable to send alert. Please try again.';
    } finally {
        isSending.value = false;
    }
}
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4">
        <div class="w-full max-w-md rounded-md border border-border bg-card shadow-xl">
            <div class="flex items-start justify-between border-b border-border px-6 py-4">
                <h2 class="text-base font-semibold text-ink">
                    Send recall alert to {{ orders.length }} customer{{ orders.length === 1 ? '' : 's' }}
                </h2>
                <button type="button" class="text-muted hover:text-ink" @click="emit('close')">
                    ✕
                </button>
            </div>

            <div class="space-y-3 px-6 py-5">
                <p class="rounded border border-red-border bg-red-bg px-3 py-2.5 text-sm text-red">
                    This will email each customer a warning about medication lot
                    <span class="font-mono font-semibold">{{ lot }}</span>, including the recommended action.
                </p>

                <ul class="max-h-40 space-y-1 overflow-y-auto text-sm text-sub">
                    <li v-for="order in orders" :key="order.id" class="flex justify-between">
                        <span>{{ order.customer.name }}</span>
                        <span class="font-mono text-muted">#{{ order.id }}</span>
                    </li>
                </ul>

                <p v-if="error" class="text-sm text-red">{{ error }}</p>
            </div>

            <div class="flex justify-end gap-2 border-t border-border px-6 py-4">
                <button
                    type="button"
                    class="rounded border border-ghost px-4 py-2 text-sm text-sub hover:text-ink"
                    @click="emit('close')"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    :disabled="isSending"
                    class="rounded bg-[#e5484d] px-4 py-2 text-sm font-semibold text-white hover:bg-[#f2555a] disabled:cursor-not-allowed disabled:opacity-60"
                    @click="confirmSend"
                >
                    {{ isSending ? 'Sending…' : 'Send email' }}
                </button>
            </div>
        </div>
    </div>
</template>
