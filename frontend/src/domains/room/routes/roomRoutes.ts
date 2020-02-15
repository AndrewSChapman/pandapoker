import { ModuleType } from '@/domains/shared/enums/moduleType';
import RoomDetail from '@/domains/room/components/RoomDetail.vue';
import RoomList from '@/domains/room/components/RoomList.vue';
import RoomSettings from '@/domains/room/components/RoomSettings.vue';
import { Route } from 'vue-router/types/router';
import { StoreProvider } from '@/domains/shared/storeProvider';
import store from '@/store';
import { RoomRouteName } from './roomRouteName';
import { UserRouteName } from '@/domains/user/routes/userRouteName';

export const RoomRoutes = [
    {
        path: '/rooms',
        name:  RoomRouteName.ROOM_LIST,
        component: RoomList,
        beforeEnter: async (to: Route, from: Route, next: any) => {
            const storeProvider = new StoreProvider(store);
            if (!storeProvider.user.isLoggedIn) {
                next({name: UserRouteName.USER_PROFILE});
                return;
            }

            await storeProvider.room.resetCurrentRoom(storeProvider.user.loggedInUserId);
            store.commit('setModuleType', ModuleType.ROOMS);
            next();
        },
    },
    {
        path: '/room_settings',
        name: RoomRouteName.ROOM_SETTINGS,
        component: RoomSettings,
        beforeEnter: async (to: Route, from: Route, next: any) => {
            const storeProvider = new StoreProvider(store);
            if (!storeProvider.user.isLoggedIn) {
                next({name: UserRouteName.USER_PROFILE});
            } else {
                await storeProvider.room.resetCurrentRoom(storeProvider.user.loggedInUserId);
                await store.commit('setModuleType', ModuleType.ROOM_SETTINGS);
                next();
            }
        },
    },
    {
        path: '/room_settings/:roomId',
        name: RoomRouteName.EDIT_ROOM,
        component: RoomSettings,
        beforeEnter: async (to: Route, from: Route, next: any) => {
            const storeProvider = new StoreProvider(store);
            if (!storeProvider.user.isLoggedIn) {
                next({name: UserRouteName.USER_PROFILE});
                return;
            } else {
                const roomId = to.params.roomId;
                const room = await storeProvider.room.getRoomById(roomId);
                if (room == null) {
                    next({name: UserRouteName.USER_PROFILE});
                    return;
                }

                await storeProvider.room.setCurrentRoom(room);

                store.commit('setModuleType', ModuleType.ROOMS);
                next();
            }
        },
    },
    {
        path: '/room_detail/:roomId',
        name: RoomRouteName.ROOM_DETAIL,
        component: RoomDetail,
        beforeEnter: (to: Route, from: Route, next: any) => {
            const storeProvider = new StoreProvider(store);
            if (!storeProvider.user.isLoggedIn) {
                next({name: UserRouteName.USER_PROFILE});
                return;
            } else {
                store.commit('setModuleType', ModuleType.ROOMS);
                next();
            }
        },
    },
];
