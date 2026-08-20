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
                :title="theme.dark ? 'Switch to light mode' : 'Switch to dark mode'"
                class="flex h-8 w-8 items-center justify-center rounded-full border border-ghost text-sub hover:text-ink"
                @click="toggleTheme"
            >
                <svg v-if="theme.dark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                    <circle cx="12" cy="12" r="4" />
                    <path stroke-linecap="round" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
                </svg>
                <svg v-else viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                    <path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 1020.354 15.354z" />
                </svg>
            </button>

            <button type="button" class="text-xs font-medium text-sub hover:text-ink" @click="handleLogout">
                Sign out
            </button>
        </div>
    </header>
</template>
