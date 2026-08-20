import { reactive, watchEffect } from 'vue';

const state = reactive({
    dark: (localStorage.getItem('pv-theme') ?? 'dark') === 'dark',
});

watchEffect(() => {
    document.documentElement.setAttribute('data-theme', state.dark ? 'dark' : 'light');
});

export function useTheme() {
    function toggle() {
        state.dark = !state.dark;
        localStorage.setItem('pv-theme', state.dark ? 'dark' : 'light');
    }

    return { state, toggle };
}
