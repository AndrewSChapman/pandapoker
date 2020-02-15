import { Store } from 'vuex';
import { UserActionIntent } from '@/domains/user/types/UserActionIntent';
import { getEmptyUserItem, UserItem } from '@/domains/user/interfaces/UserItem';
import { CreateUserActionParams } from '@/domains/user/interfaces/CreateUserActionParams';
import { UpdateUserActionParams } from '@/domains/user/interfaces/UpdateUserActionParams';
import { AnimalType } from '@/domains/user/enums/AnimalType';

export class UserStoreProvider {
    private store: Store<any>;
    private userStore: any;

    constructor(store: Store<any>, userStore: any) {
        this.store = store;
        this.userStore = userStore;
    }

    get hasSocketConnection(): boolean {
        return this.userStore.socketConnected;
    }

    get isLoggedIn(): boolean {
        return this.userStore.userLoggedIn;
    }

    get loggedInUserId(): string {
        if (!this.userStore.userId) {
            return '';
        }

        return this.userStore.userId;
    }

    get displayName(): string {
        return this.userStore.userDisplayName;
    }

    get totemAnimal(): AnimalType|null {
        return this.userStore.totemAnimal;
    }

    get errorMessage(): string {
        if (this.hasError) {
            return this.userStore.error.message;
        }

        return '';
    }

    get hasError(): boolean {
        return ((this.userStore.error !== null) && (this.userStore.error.hasOwnProperty('message'))
            && (this.userStore.error.message.length > 0));
    }

    get lastChangeLogId(): number {
        return this.userStore.lastChangeLogId;
    }


    get userWantsToCreateRoom(): boolean {
        return this.userStore.userActionIntent === UserActionIntent.CREATE_ROOM;
    }

    public clearUserActionIntent(): void {
        this.store.commit('setUserActionIntent', null);
    }

    public flagUserWantsToCreateRoom(): void {
        this.store.commit('setUserActionIntent', UserActionIntent.CREATE_ROOM);
    }

    public async assumeUser(displayName: string): Promise<boolean> {
        await this.store.dispatch('assumeUser', displayName);
        return !this.hasError;
    }

    public async createUser(displayName: string, totemAnimal: AnimalType): Promise<boolean> {
        const params: CreateUserActionParams = {
            username: displayName,
            totemAnimal,
        };

        await this.store.dispatch('createUser', params);

        if (!this.hasError) {
            await this.store.dispatch('loadUsers', displayName);
        }

        return !this.hasError;
    }

    public async updateUser(displayName: string, totemAnimal: AnimalType): Promise<boolean> {
        const params: UpdateUserActionParams = {
            username: displayName,
            totemAnimal,
        };

        await this.store.dispatch('updateUser', params);

        if (!this.hasError) {
            await this.store.dispatch('loadUsers', displayName);
        }

        return !this.hasError;
    }

    public async loadUsers(): Promise<boolean> {
        await this.store.dispatch('loadUsers');
        return !this.hasError;
    }

    public getUserById(userId: string): UserItem {
        for (const thisUser of this.userStore.users) {
            if (thisUser.id === userId) {
                return thisUser;
            }
        }

        return getEmptyUserItem();
    }

    public async logout(): Promise<void> {
        await this.store.dispatch('logoutUser');
    }

    public async setSocketStatus(connected: boolean) {
        await this.store.commit('setSocketConnected', connected);
    }

    public async setLastChangeLogId(changeLogId: number): Promise<void> {
        await this.store.commit('setLastChangeLogId', changeLogId);
    }

    public async handleNewUserFromSocketEvent(userItem: UserItem): Promise<void> {
        // If this event is about the user adding themselves, do nothing.
        if (this.loggedInUserId === userItem.id) {
            return;
        }

        await this.store.commit('appendUser', userItem);
    }

    public async handleUpdateUserFromSocketEvent(userItem: UserItem): Promise<void> {
        await this.store.dispatch('updateExistingUser', userItem);
    }
 }
