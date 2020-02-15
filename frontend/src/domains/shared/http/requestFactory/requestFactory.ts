import { ApiClient } from '@/domains/shared/apiClient';

import { RoomRequestFactory } from '@/domains/room/requests/roomRequestFactory';
import { UserRequestFactory } from '@/domains/user/requests/userRequestFactory';

export default class RequestFactory {
    private readonly apiClient: ApiClient;
    private roomRequestFactory!: RoomRequestFactory;
    private userRequestFactory!: UserRequestFactory;

    constructor(apiClient: ApiClient) {
        this.apiClient = apiClient;
    }

    public install(vue: any) {
        Object.defineProperty(vue.prototype, '$requestFactory', { value: this });
    }

    public updateToken(token: string): void {
        this.apiClient.setToken(token);
    }

    public get room(): RoomRequestFactory {
        if (!this.roomRequestFactory) {
            this.roomRequestFactory = new RoomRequestFactory(this.apiClient);
        }

        return this.roomRequestFactory;
    }

    public get user(): UserRequestFactory {
        if (!this.userRequestFactory) {
            this.userRequestFactory = new UserRequestFactory(this.apiClient);
        }

        return this.userRequestFactory;
    }
}
