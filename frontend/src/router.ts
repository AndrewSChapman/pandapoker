import Vue from 'vue';
import VueRouter from 'vue-router';

import { RoomRoutes } from '@/domains/room/routes/roomRoutes';
import { UserRoutes } from '@/domains/user/routes/userRoutes';
import UserProfile from '@/domains/user/components/UserProfile.vue';
import { Route } from 'vue-router/types/router';
import { StoreProvider } from '@/domains/shared/storeProvider';
import store from '@/store';
import { UserRouteName } from '@/domains/user/routes/userRouteName';
import { ModuleType } from '@/domains/shared/enums/moduleType';

Vue.use(VueRouter);

const routes = [
    {
        path: '/',
        name: 'home',
        component: UserProfile,
        beforeEnter: async (to: Route, from: Route, next: any) => {
            next({name: UserRouteName.USER_PROFILE});
        },
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
