<script setup>
defineProps({
    lot: { type: String, required: true },
});
</script>

<template>
    <span class="rounded border border-red-border bg-red-bg px-2.5 py-1 font-mono text-[11px] font-medium tracking-wide text-red">
        LOT {{ lot }}
    </span>
</template>
