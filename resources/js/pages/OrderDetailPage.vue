<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import AppHeader from '../components/AppHeader.vue';

const route = useRoute();
const router = useRouter();

const order = ref(null);
const isLoading = ref(true);
const error = ref('');

onMounted(async () => {
    try {
        const response = await api.get(`/orders/${route.params.id}`);
        order.value = response.data.data;
    } catch (e) {
        error.value = e.response?.status === 404 ? 'Order not found.' : 'Unable to load this order.';
    } finally {
        isLoading.value = false;
    }
});
</script>

<template>
    <div class="min-h-screen bg-page">
        <AppHeader />

        <main class="mx-auto max-w-2xl px-6 py-9">
            <button
                type="button"
                class="mb-4 text-sm text-sub hover:text-ink"
                @click="router.back()"
            >
                ← Back
            </button>

            <p v-if="isLoading" class="text-sm text-sub">Loading order…</p>
            <p v-else-if="error" class="rounded border border-red-border bg-red-bg px-4 py-3 text-sm text-red">{{ error }}</p>

            <section v-else class="rounded-md border border-border bg-card p-6">
                <div class="mb-6 flex items-center justify-between">
                    <h1 class="font-mono text-lg font-semibold text-amber">Order #{{ order.id }}</h1>
                    <span class="font-mono text-sm text-sub">{{ order.purchase_date }}</span>
                </div>

                <div class="mb-6">
                    <h2 class="mb-2 font-mono text-[10px] font-medium tracking-[.12em] text-muted">CUSTOMER</h2>
                    <button
                        type="button"
                        class="text-sm font-medium text-ink hover:underline"
                        @click="router.push({ name: 'customer-detail', params: { id: order.customer.id } })"
                    >
                        {{ order.customer.name }}
                    </button>
                    <p class="text-sm text-sub">{{ order.customer.email }}</p>
                    <p v-if="order.customer.phone" class="text-sm text-sub">{{ order.customer.phone }}</p>
                </div>

                <div>
                    <h2 class="mb-2 font-mono text-[10px] font-medium tracking-[.12em] text-muted">MEDICATIONS</h2>
                    <ul class="space-y-2">
                        <li
                            v-for="medication in order.medications"
                            :key="medication.id"
                            class="flex items-center justify-between rounded border border-border px-3 py-2 text-sm"
                        >
                            <span class="text-ink">{{ medication.name }}</span>
                            <span class="font-mono text-sub">Lot {{ medication.lot_number }}</span>
                        </li>
                    </ul>
                </div>
            </section>
        </main>
    </div>
</template>
