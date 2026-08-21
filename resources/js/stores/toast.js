import { reactive } from 'vue';

const state = reactive({
    toasts: [],
});

let nextId = 1;

export function useToast() {
    function push(message, type = 'success') {
        const id = nextId++;
        state.toasts.push({ id, message, type });

        setTimeout(() => dismiss(id), 4000);
    }

    function dismiss(id) {
        state.toasts = state.toasts.filter((toast) => toast.id !== id);
    }

    return {
        state,
        success: (message) => push(message, 'success'),
        error: (message) => push(message, 'error'),
        dismiss,
    };
}
