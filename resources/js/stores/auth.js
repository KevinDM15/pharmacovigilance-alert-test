import { reactive } from 'vue';
import api from '../services/api';

const state = reactive({
    user: null,
    token: localStorage.getItem('pharmacovigilance_token'),
});

export function useAuthStore() {
    async function login(username, password) {
        const response = await api.post('/login', { username, password });

        state.token = response.data.token;
        state.user = response.data.user;
        localStorage.setItem('pharmacovigilance_token', state.token);
    }

    async function logout() {
        await api.post('/logout');

        state.token = null;
        state.user = null;
        localStorage.removeItem('pharmacovigilance_token');
    }

    function isAuthenticated() {
        return state.token !== null;
    }

    return { state, login, logout, isAuthenticated };
}
