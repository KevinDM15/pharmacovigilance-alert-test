<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const { login } = useAuthStore();

const username = ref('');
const password = ref('');
const error = ref('');
const isSubmitting = ref(false);

async function handleSubmit() {
    error.value = '';
    isSubmitting.value = true;

    try {
        await login(username.value, password.value);
        router.push({ name: 'search' });
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Unable to sign in. Please try again.';
    } finally {
        isSubmitting.value = false;
    }
}
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-page px-4">
        <div class="w-full max-w-sm">
            <div class="mb-8 flex items-center gap-3">
                <span class="grid h-9 w-9 place-items-center rounded bg-[#4ade80] font-mono text-sm font-bold text-[#12161b]">
                    Rx
                </span>
                <div>
                    <p class="font-mono text-xs font-semibold tracking-[.1em] text-ink">PHARMACOVIGILANCE</p>
                    <p class="font-mono text-xs text-muted">/ recall &amp; alert console</p>
                </div>
            </div>

            <div class="rounded-md border border-border bg-card p-8">
                <h1 class="mb-1 text-lg font-semibold text-ink">Sign in</h1>
                <p class="mb-6 text-sm text-sub">Restricted to pharmacovigilance staff.</p>

                <form class="space-y-4" @submit.prevent="handleSubmit">
                    <div>
                        <label for="username" class="mb-1.5 block font-mono text-[10px] font-medium tracking-[.12em] text-muted">USERNAME</label>
                        <input
                            id="username"
                            v-model="username"
                            type="text"
                            autocomplete="username"
                            required
                            class="w-full border-0 border-b-[1.5px] border-line bg-transparent px-0.5 py-2 text-sm text-ink outline-none"
                        >
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block font-mono text-[10px] font-medium tracking-[.12em] text-muted">PASSWORD</label>
                        <input
                            id="password"
                            v-model="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="w-full border-0 border-b-[1.5px] border-line bg-transparent px-0.5 py-2 text-sm text-ink outline-none"
                        >
                    </div>

                    <p v-if="error" class="rounded border border-red-border bg-red-bg px-3 py-2 text-sm text-red">
                        {{ error }}
                    </p>

                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="w-full rounded bg-[#f5a524] py-2.5 text-sm font-semibold text-[#12161b] transition-colors hover:bg-[#ffc04d] disabled:opacity-60"
                    >
                        {{ isSubmitting ? 'Signing in…' : 'Sign in' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
