import { RoomStoreProvider } from '@/domains/room/store/roomStoreProvider';
import { Store } from 'vuex';
import { UserStoreProvider } from '@/domains/user/store/userStoreProvider';

export class StoreProvider {
    private readonly store: Store<any>;
    private userStoreProvider: UserStoreProvider|null = null;
    private roomStoreProvider: RoomStoreProvider|null = null;

    constructor(store: Store<any>) {
        this.store = store;
    }

    public install(vue: any) {
        Object.defineProperty(vue.prototype, '$storeProvider', { value: this });
    }

    get user(): UserStoreProvider {
        if (!this.userStoreProvider) {
            this.userStoreProvider = new UserStoreProvider(this.store, this.store.state.user);
        }

        return this.userStoreProvider;
    }

    get room(): RoomStoreProvider {
        if (!this.roomStoreProvider) {
            this.roomStoreProvider = new RoomStoreProvider(this.store, this.store.state.room);
        }

        return this.roomStoreProvider;
    }
}
