import { ModuleType } from '@/domains/shared/enums/moduleType';
import { Route } from 'vue-router/types/router';
import store from '@/store';
import UserProfile from '@/domains/user/components/UserProfile.vue';
import { StoreProvider } from '@/domains/shared/storeProvider';

export const UserRoutes = [
    {
        path: '/user_profile',
        name: 'user_profile',
        component: UserProfile,
        beforeEnter: async (to: Route, from: Route, next: any) => {
            const storeProvider = new StoreProvider(store);
            if (storeProvider.user.isLoggedIn) {
                await storeProvider.room.resetCurrentRoom(storeProvider.user.loggedInUserId);
            }

            store.commit('setModuleType', ModuleType.PROFILE);
            store.commit('setUserSaving', false);
            next();
        },
    },
];
