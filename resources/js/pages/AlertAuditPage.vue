<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import AppHeader from '../components/AppHeader.vue';

const router = useRouter();

const alerts = ref([]);
const meta = ref(null);
const isLoading = ref(true);
const error = ref('');

async function loadPage(page = 1) {
    isLoading.value = true;
    error.value = '';

    try {
        const response = await api.get('/alerts', { params: { page } });
        alerts.value = response.data.data;
        meta.value = response.data.meta;
    } catch (e) {
        error.value = 'Unable to load alert history.';
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => loadPage());

function formatDate(iso) {
    return new Date(iso).toLocaleString(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
}
</script>

<template>
    <div class="min-h-screen bg-page">
        <AppHeader />

        <main class="mx-auto max-w-[1120px] px-6 py-9">
            <button
                type="button"
                class="mb-4 text-sm text-sub hover:text-ink"
                @click="router.push({ name: 'search' })"
            >
                ← Back to search
            </button>

            <div class="overflow-hidden rounded-md border border-border bg-card">
                <div class="border-b border-border bg-band px-8 pt-7 pb-6">
                    <span class="text-base font-semibold text-ink">Alert history</span>
                    <p class="mt-0.5 text-[12.5px] text-sub">
                        Audit log of every recall alert sent — who sent it, to whom, and when.
                    </p>
                </div>

                <p v-if="error" class="border-b border-border bg-red-bg px-8 py-3 text-[12.5px] text-red">
                    {{ error }}
                </p>

                <div class="px-8 py-6">
                    <p v-if="isLoading" class="py-8 text-center text-sm text-sub">Loading…</p>

                    <p v-else-if="alerts.length === 0" class="rounded border border-dashed border-border px-4 py-8 text-center text-sm text-sub">
                        No alerts have been sent yet.
                    </p>

                    <template v-else>
                        <div class="grid grid-cols-[70px_1.4fr_1fr_1fr_auto] items-center gap-x-4 rounded-t border border-border bg-thead px-3 py-2.5">
                            <span class="font-mono text-[10px] font-medium tracking-[.12em] text-muted">ORDER</span>
                            <span class="font-mono text-[10px] font-medium tracking-[.12em] text-muted">CUSTOMER</span>
                            <span class="font-mono text-[10px] font-medium tracking-[.12em] text-muted">SENT BY</span>
                            <span class="font-mono text-[10px] font-medium tracking-[.12em] text-muted">SENT AT</span>
                            <span class="text-right font-mono text-[10px] font-medium tracking-[.12em] text-muted">ACTIONS</span>
                        </div>

                        <div
                            v-for="alert in alerts"
                            :key="alert.id"
                            class="grid grid-cols-[70px_1.4fr_1fr_1fr_auto] items-center gap-x-4 border border-t-0 border-border px-3 py-2.5"
                        >
                            <span class="font-mono text-[12.5px] font-medium text-amber">#{{ alert.order_id }}</span>
                            <span class="text-[13px] font-medium text-ink">{{ alert.customer.name }}</span>
                            <span class="font-mono text-[12.5px] text-sub">{{ alert.sent_by }}</span>
                            <span class="font-mono text-[12.5px] text-sub">{{ formatDate(alert.sent_at) }}</span>
                            <div class="flex justify-end">
                                <button
                                    type="button"
                                    class="rounded border border-ghost px-3 py-1.5 text-[11.5px] font-medium text-sub"
                                    @click="router.push({ name: 'order-detail', params: { id: alert.order_id } })"
                                >
                                    View order
                                </button>
                            </div>
                        </div>
                    </template>

                    <div v-if="meta && meta.last_page > 1" class="mt-4 flex items-center justify-between text-[12.5px] text-sub">
                        <span>Page {{ meta.current_page }} of {{ meta.last_page }} — {{ meta.total }} alerts</span>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                :disabled="meta.current_page === 1"
                                class="rounded border border-ghost px-3 py-1.5 text-[11.5px] font-medium text-sub disabled:opacity-40"
                                @click="loadPage(meta.current_page - 1)"
                            >
                                Previous
                            </button>
                            <button
                                type="button"
                                :disabled="meta.current_page === meta.last_page"
                                class="rounded border border-ghost px-3 py-1.5 text-[11.5px] font-medium text-sub disabled:opacity-40"
                                @click="loadPage(meta.current_page + 1)"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
