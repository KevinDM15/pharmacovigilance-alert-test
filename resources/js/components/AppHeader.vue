<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useTheme } from '../stores/theme';

const router = useRouter();
const { logout } = useAuthStore();
const { state: theme, toggle: toggleTheme } = useTheme();

async function handleLogout() {
    await logout();
    router.push({ name: 'login' });
}
</script>

<template>
    <header class="flex items-center justify-between border-b border-border bg-card px-10 py-4">
        <div class="flex items-center gap-3">
            <span class="grid h-[26px] w-[26px] place-items-center rounded bg-[#4ade80] font-mono text-xs font-bold text-[#12161b]">
                Rx
            </span>
            <div class="flex items-baseline gap-3">
                <span class="font-mono text-[13px] font-semibold tracking-[.1em] text-ink">PHARMACOVIGILANCE</span>
                <span class="font-mono text-[11px] text-muted">/ recall &amp; alert console</span>
            </div>
        </div>

        <div class="flex items-center gap-3.5">
            <button
                type="button"
                title="Toggle theme"
                class="flex items-center gap-1.5 rounded-full border border-ghost px-3 py-1.5 font-mono text-[11px] font-medium tracking-[.06em] text-sub"
                @click="toggleTheme"
            >
                <span
                    class="h-2 w-2 rounded-full"
                    :class="theme.dark ? 'bg-[#f5d78e]' : 'bg-ink'"
                ></span>
                {{ theme.dark ? 'DARK' : 'LIGHT' }}
            </button>

            <button type="button" class="text-xs font-medium text-sub hover:text-ink" @click="handleLogout">
                Sign out
            </button>
        </div>
    </header>
</template>
