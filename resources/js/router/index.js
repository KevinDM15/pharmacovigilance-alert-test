import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import LoginPage from '../pages/LoginPage.vue';
import MedicationSearchPage from '../pages/MedicationSearchPage.vue';
import OrderDetailPage from '../pages/OrderDetailPage.vue';
import CustomerDetailPage from '../pages/CustomerDetailPage.vue';
import AlertAuditPage from '../pages/AlertAuditPage.vue';

const routes = [
    {
        path: '/pharmacovigilance/login',
        name: 'login',
        component: LoginPage,
    },
    {
        path: '/pharmacovigilance',
        name: 'search',
        component: MedicationSearchPage,
        meta: { requiresAuth: true },
    },
    {
        path: '/pharmacovigilance/orders/:id',
        name: 'order-detail',
        component: OrderDetailPage,
        meta: { requiresAuth: true },
    },
    {
        path: '/pharmacovigilance/customers/:id',
        name: 'customer-detail',
        component: CustomerDetailPage,
        meta: { requiresAuth: true },
    },
    {
        path: '/pharmacovigilance/alerts',
        name: 'alert-audit',
        component: AlertAuditPage,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const { isAuthenticated } = useAuthStore();

    if (to.meta.requiresAuth && !isAuthenticated()) {
        return { name: 'login' };
    }
});

export default router;
