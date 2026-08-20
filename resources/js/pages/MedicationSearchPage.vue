<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import { useToast } from '../stores/toast';
import AppHeader from '../components/AppHeader.vue';
import LotBadge from '../components/LotBadge.vue';
import AlertModal from '../components/AlertModal.vue';

const route = useRoute();
const router = useRouter();
const toast = useToast();

const lot = ref(route.query.lot ?? '951357');
const startDate = ref(route.query.start_date ?? '');
const endDate = ref(route.query.end_date ?? '');

const orders = ref([]);
const meta = ref(null);
const searchedLot = ref('');
const isSearching = ref(false);
const searchError = ref('');
const hasSearched = ref(false);
const isExporting = ref(false);

const selectedIds = ref(new Set());
const modalOrders = ref(null);

const allSelected = computed(() => orders.value.length > 0 && selectedIds.value.size === orders.value.length);

function searchParams(page = 1) {
    return {
        lot: lot.value,
        start_date: startDate.value || undefined,
        end_date: endDate.value || undefined,
        page,
    };
}

async function handleSearch(page = 1) {
    isSearching.value = true;
    searchError.value = '';
    selectedIds.value = new Set();

    router.replace({ query: searchParams(page) });

    try {
        const response = await api.get('/orders', { params: searchParams(page) });

        orders.value = response.data.data;
        meta.value = response.data.meta;
        searchedLot.value = lot.value;
        hasSearched.value = true;
    } catch (e) {
        orders.value = [];
        meta.value = null;
        searchError.value = e.response?.data?.message ?? 'Unable to search orders. Please check your filters.';
    } finally {
        isSearching.value = false;
    }
}

function goToPage(page) {
    handleSearch(page);
}

onMounted(() => {
    if (route.query.lot) {
        handleSearch(Number(route.query.page) || 1);
    }
});

function toggleSelected(id) {
    if (selectedIds.value.has(id)) {
        selectedIds.value.delete(id);
    } else {
        selectedIds.value.add(id);
    }

    selectedIds.value = new Set(selectedIds.value);
}

function toggleSelectAll() {
    selectedIds.value = allSelected.value
        ? new Set()
        : new Set(orders.value.map((order) => order.id));
}

function openAlertFor(order) {
    modalOrders.value = [order];
}

function openBulkAlert() {
    modalOrders.value = orders.value.filter((order) => selectedIds.value.has(order.id));
}

function onAlertSent(message) {
    toast.success(message);
    modalOrders.value = null;
    selectedIds.value = new Set();
}

async function exportCsv() {
    isExporting.value = true;

    try {
        const response = await api.get('/orders/export', {
            params: { lot: searchedLot.value, start_date: startDate.value || undefined, end_date: endDate.value || undefined },
            responseType: 'blob',
        });

        const url = URL.createObjectURL(response.data);
        const link = document.createElement('a');
        link.href = url;
        link.download = `orders-lot-${searchedLot.value}.csv`;
        link.click();
        URL.revokeObjectURL(url);

        toast.success('CSV export downloaded.');
    } catch (e) {
        toast.error('Unable to export CSV.');
    } finally {
        isExporting.value = false;
    }
}
</script>

<template>
    <div class="min-h-screen bg-page">
        <AppHeader />

        <main class="mx-auto max-w-[1120px] px-6 py-9">
            <div class="overflow-hidden rounded-md border border-border bg-card">
                <div class="border-b border-border bg-band px-8 pt-7 pb-6">
                    <div class="mb-5 flex flex-col gap-0.5">
                        <span class="text-base font-semibold text-ink">Medication search</span>
                        <span class="text-[12.5px] text-sub">
                            Find every order containing a specific lot number to trigger recall alerts.
                        </span>
                    </div>

                    <form class="grid grid-cols-1 items-end gap-6 sm:grid-cols-[1.6fr_1fr_1fr_auto]" @submit.prevent="handleSearch">
                        <div class="flex flex-col gap-1.5">
                            <label for="lot" class="font-mono text-[10px] font-medium tracking-[.12em] text-muted">LOT NUMBER</label>
                            <input
                                id="lot"
                                v-model="lot"
                                type="text"
                                required
                                placeholder="e.g. 951357"
                                class="w-full border-0 border-b-[1.5px] border-line bg-transparent px-0.5 py-2 font-mono text-base font-medium text-amber outline-none"
                            >
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="start_date" class="font-mono text-[10px] font-medium tracking-[.12em] text-muted">START DATE</label>
                            <input
                                id="start_date"
                                v-model="startDate"
                                type="date"
                                class="w-full border-0 border-b-[1.5px] border-line bg-transparent px-0.5 py-2 font-mono text-[13px] text-sub outline-none"
                            >
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="end_date" class="font-mono text-[10px] font-medium tracking-[.12em] text-muted">END DATE</label>
                            <input
                                id="end_date"
                                v-model="endDate"
                                type="date"
                                class="w-full border-0 border-b-[1.5px] border-line bg-transparent px-0.5 py-2 font-mono text-[13px] text-sub outline-none"
                            >
                        </div>

                        <button
                            type="submit"
                            :disabled="isSearching"
                            class="rounded bg-[#f5a524] px-6.5 py-2.5 text-[13px] font-semibold text-[#12161b] hover:bg-[#ffc04d] disabled:opacity-60"
                        >
                            Search
                        </button>
                    </form>

                    <span class="mt-3.5 block text-[11.5px] text-muted">Leave dates empty to default to the last 30 days.</span>
                </div>

                <p v-if="searchError" class="border-b border-border bg-red-bg px-8 py-3 text-[12.5px] text-red">
                    {{ searchError }}
                </p>

                <div class="px-8 py-6">
                    <div class="mb-3 flex items-center gap-3">
                        <span class="text-sm font-semibold text-ink">Order results</span>
                        <LotBadge v-if="searchedLot" :lot="searchedLot" />

                        <button
                            v-if="hasSearched && orders.length > 0"
                            type="button"
                            :disabled="isExporting"
                            class="ml-auto rounded border border-ghost px-3 py-1.5 text-xs font-medium text-sub disabled:opacity-60"
                            @click="exportCsv"
                        >
                            {{ isExporting ? 'Exporting…' : 'Export CSV' }}
                        </button>

                        <button
                            v-if="selectedIds.size > 0"
                            type="button"
                            class="rounded bg-[#e5484d] px-4 py-1.5 text-xs font-semibold text-white hover:bg-[#f2555a]"
                            @click="openBulkAlert"
                        >
                            Alert {{ selectedIds.size }} selected
                        </button>
                    </div>

                    <p v-if="!hasSearched" class="rounded border border-dashed border-border px-4 py-8 text-center text-sm text-sub">
                        Search a lot number to see matching orders here.
                    </p>

                    <p v-else-if="orders.length === 0" class="rounded border border-border px-4 py-8 text-center text-sm text-sub">
                        No orders found for this lot in the selected date range.
                    </p>

                    <template v-else>
                        <div class="grid grid-cols-[26px_90px_1.4fr_1fr_auto] items-center gap-x-4 rounded-t border border-border bg-thead px-3 py-2.5">
                            <input type="checkbox" class="h-[13px] w-[13px] accent-[#f5a524]" :checked="allSelected" @change="toggleSelectAll">
                            <span class="font-mono text-[10px] font-medium tracking-[.12em] text-muted">ORDER ID</span>
                            <span class="font-mono text-[10px] font-medium tracking-[.12em] text-muted">CUSTOMER</span>
                            <span class="font-mono text-[10px] font-medium tracking-[.12em] text-muted">PURCHASE DATE</span>
                            <span class="text-right font-mono text-[10px] font-medium tracking-[.12em] text-muted">ACTIONS</span>
                        </div>

                        <div
                            v-for="order in orders"
                            :key="order.id"
                            class="grid grid-cols-[26px_90px_1.4fr_1fr_auto] items-center gap-x-4 border border-t-0 border-border px-3 py-2.5"
                        >
                            <input
                                type="checkbox"
                                class="h-[13px] w-[13px] accent-[#f5a524]"
                                :checked="selectedIds.has(order.id)"
                                @change="toggleSelected(order.id)"
                            >
                            <span class="font-mono text-[12.5px] font-medium text-amber">#{{ order.id }}</span>
                            <span class="text-[13px] font-medium text-ink">{{ order.customer.name }}</span>
                            <span class="font-mono text-[12.5px] text-sub">{{ order.purchase_date }}</span>
                            <div class="flex justify-end gap-2">
                                <button
                                    type="button"
                                    class="rounded border border-ghost px-3 py-1.5 text-[11.5px] font-medium text-sub"
                                    @click="router.push({ name: 'order-detail', params: { id: order.id } })"
                                >
                                    View order
                                </button>
                                <button
                                    type="button"
                                    class="rounded bg-[#e5484d] px-3.5 py-1.5 text-[11.5px] font-semibold text-white hover:bg-[#f2555a]"
                                    @click="openAlertFor(order)"
                                >
                                    Alert buyer
                                </button>
                                <button
                                    type="button"
                                    class="rounded border border-ghost px-3 py-1.5 text-[11.5px] font-medium text-sub"
                                    @click="router.push({ name: 'customer-detail', params: { id: order.customer.id } })"
                                >
                                    View buyer
                                </button>
                            </div>
                        </div>
                    </template>

                    <div v-if="meta && meta.last_page > 1" class="mt-4 flex items-center justify-between text-[12.5px] text-sub">
                        <span>Page {{ meta.current_page }} of {{ meta.last_page }} — {{ meta.total }} orders</span>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                :disabled="meta.current_page === 1"
                                class="rounded border border-ghost px-3 py-1.5 text-[11.5px] font-medium text-sub disabled:opacity-40"
                                @click="goToPage(meta.current_page - 1)"
                            >
                                Previous
                            </button>
                            <button
                                type="button"
                                :disabled="meta.current_page === meta.last_page"
                                class="rounded border border-ghost px-3 py-1.5 text-[11.5px] font-medium text-sub disabled:opacity-40"
                                @click="goToPage(meta.current_page + 1)"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <AlertModal
            v-if="modalOrders"
            :lot="searchedLot"
            :orders="modalOrders"
            @close="modalOrders = null"
            @sent="onAlertSent"
        />
    </div>
</template>

<style scoped>
input[type='date'] {
    min-width: 0;
}

input[type='date']::-webkit-calendar-picker-indicator {
    margin-left: 8px;
}

input[type='date']::-webkit-datetime-edit {
    min-width: 90px;
}
</style>
