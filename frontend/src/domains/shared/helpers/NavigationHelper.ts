import { VueRouter } from 'vue-router/types/router';
import { RoomRouteName } from '@/domains/room/routes/roomRouteName';

let navigationHelper: NavigationHelper|null = null;

export function navigateTo(router: VueRouter): NavigationHelper {
    if (!navigationHelper) {
        navigationHelper = new NavigationHelper(router);
    }

    return navigationHelper;
}

class NavigationHelper {
    constructor(private router: VueRouter) {
    }

    public async home(): Promise<void> {
        await this.redirectToNamedRoute('home');
    }

    public async roomList(): Promise<void> {
        await this.redirectToNamedRoute('rooms');
    }

    public async roomSettings(roomId: string = ''): Promise<void> {
        let params = null;
        let routeName = RoomRouteName.ROOM_SETTINGS;

        if (roomId.length > 0) {
            params = {
                roomId,
            };

            routeName = RoomRouteName.EDIT_ROOM;
        }

        await this.redirectToNamedRoute(routeName, params);
    }

    public async roomDetails(roomId: string): Promise<void> {
        await this.redirectToNamedRoute('room_detail', {
            roomId,
        });
    }

    public async userProfile(): Promise<void> {
        await this.redirectToNamedRoute('user_profile');
    }

    private async redirectToNamedRoute(namedRoute: string, params: {[key: string]: any}|null = null): Promise<void> {
        try {
            const routeData: any = {
                name: namedRoute,
            };

            if (params) {
                routeData.params = params;
            }

            await this.router.push(routeData);
        } catch {
            // This can happen when the redirection is cancelled - nothing to worry about.
        }
    }
}
