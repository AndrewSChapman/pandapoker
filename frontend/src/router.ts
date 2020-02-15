import Vue from 'vue';
import VueRouter from 'vue-router';

import { RoomRoutes } from '@/domains/room/routes/roomRoutes';
import { UserRoutes } from '@/domains/user/routes/userRoutes';
import UserProfile from '@/domains/user/components/UserProfile.vue';

Vue.use(VueRouter);

const routes = [
    {
        path: '/',
        name: 'home',
        component: UserProfile,
    },
    ...RoomRoutes,
    ...UserRoutes,
];

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes,
});

export default router;
