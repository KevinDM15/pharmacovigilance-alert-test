<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import AppHeader from '../components/AppHeader.vue';

const route = useRoute();
const router = useRouter();

const customer = ref(null);
const isLoading = ref(true);
const error = ref('');

onMounted(async () => {
    try {
        const response = await api.get(`/customers/${route.params.id}`);
        customer.value = response.data.data;
    } catch (e) {
        error.value = e.response?.status === 404 ? 'Customer not found.' : 'Unable to load this customer.';
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

            <p v-if="isLoading" class="text-sm text-sub">Loading customer…</p>
            <p v-else-if="error" class="rounded border border-red-border bg-red-bg px-4 py-3 text-sm text-red">{{ error }}</p>

            <section v-else class="rounded-md border border-border bg-card p-6">
                <h1 class="mb-6 text-lg font-semibold text-ink">{{ customer.name }}</h1>

                <dl class="space-y-4">
                    <div>
                        <dt class="font-mono text-[10px] font-medium tracking-[.12em] text-muted">EMAIL</dt>
                        <dd class="text-sm text-ink">{{ customer.email }}</dd>
                    </div>
                    <div v-if="customer.phone">
                        <dt class="font-mono text-[10px] font-medium tracking-[.12em] text-muted">PHONE</dt>
                        <dd class="text-sm text-ink">{{ customer.phone }}</dd>
                    </div>
                </dl>
            </section>
        </main>
    </div>
</template>
