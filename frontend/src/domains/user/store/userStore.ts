import { Module, VuexModule, Mutation, Action } from 'vuex-module-decorators';

import { TokenInfo } from '@/domains/user/interfaces/TokenInfo';
import { getLogger } from '@/domains/shared/logger/getLogger';
import { getRequestFactory } from '@/domains/shared/http/requestFactory/getRequestFactory';
import { LogLevel } from '@/domains/shared/logger/types/LogLevel';
import { UserActionIntent } from '@/domains/user/types/UserActionIntent';
import { ErrorObject } from '@/domains/shared/interfaces/errorObject';
import { UserItem } from '@/domains/user/interfaces/UserItem';
import { CreateUserActionParams } from '@/domains/user/interfaces/CreateUserActionParams';
import { UpdateUserActionParams } from '@/domains/user/interfaces/UpdateUserActionParams';
import { AnimalType } from '@/domains/user/enums/AnimalType';

@Module
export class UserStore extends VuexModule {
    public lastChangeLogId: number = 0;
    public token: string = '';
    public userId: string = '';
    public userDisplayName: string = '';
    public totemAnimal: AnimalType|null = null;
    public socketConnected: boolean = false;
    public userLoggedIn: boolean = false;
    public userActionIntent: UserActionIntent|null = null;
    public users: UserItem[] = [];
    public saving: boolean = false;
    private error: ErrorObject|null = null;

    /******************************
     * MUTATIONS
     *****************************/
    @Mutation
    public setSessionData(tokenInfo: TokenInfo) {
        this.token = tokenInfo.token;
        this.userId = tokenInfo.user_id;
        this.userLoggedIn = this.token.length > 0;
    }

    @Mutation
    public setToken(value: string) {
        this.token = value;
    }

    @Mutation
    public setUserId(value: string) {
        this.userId = value;
    }

    @Mutation
    public setUserLoggedIn(value: boolean) {
        this.userLoggedIn = value;
    }

    @Mutation
    public setLastChangeLogId(value: number) {
        this.lastChangeLogId = value;
    }

    @Mutation
    public setSocketConnected(value: boolean) {
        this.socketConnected = value;
    }

    @Mutation
    public setUserActionIntent(value: UserActionIntent|null) {
        this.userActionIntent = value;
    }

    @Mutation
    public setUserDisplayName(value: string) {
        this.userDisplayName = value;
    }

    @Mutation
    public setUserSaving(value: boolean) {
        this.saving = value;
    }

    @Mutation
    public setUsers(users: UserItem[]) {
        this.users = users;
    }

    @Mutation
    public setTotemAnimal(totemAnimal: AnimalType|null) {
        this.totemAnimal = totemAnimal;
    }

    @Mutation
    public setErrorObject(value: ErrorObject) {
        this.error = value;

        if ((this.error) && (this.error.message) && (this.error.message.length > 0)) {
            const logger = getLogger();
            logger.log(LogLevel.ERROR, this.error.message);
        }
    }

    @Mutation
    public appendUser(user: UserItem) {
        this.users.push(user);
    }

    /******************************
     * ACTIONS
     *****************************/
    @Action
    public logoutUser() {
        this.context.commit('setUserDisplayName', '');
        this.context.commit('setTotemAnimal', null);
        this.context.commit('setToken', '');
        this.context.commit('setUserId', '');
        this.context.commit('setUserLoggedIn', false);
    }

    @Action
    public async loadUsers(): Promise<void> {
        const logger = getLogger();
        await this.context.commit('setErrorObject', null);

        try {
            const requestFactory = getRequestFactory();
            const users = await requestFactory.user.userList.execute();
            this.context.commit('setUsers', users);
        } catch (error) {
            logger.log(LogLevel.ERROR, `Error loading users: ${error.toString()}`);
            await this.context.commit('setErrorObject', error);
        }
    }

    @Action
    public async assumeUser(userDisplayName: string): Promise<void> {
        await this.context.commit('setErrorObject', null);

        const requestFactory = getRequestFactory();
        this.context.commit('setUserSaving', true);

        try {
            const tokenInfo = await requestFactory.user.assumeUser.execute(userDisplayName);
            this.context.commit('setSessionData', tokenInfo);
            this.context.commit('setUserDisplayName', userDisplayName);

            requestFactory.updateToken(tokenInfo.token);
        } catch (error) {
            await this.context.commit('setErrorObject', error);
        } finally {
            this.context.commit('setUserSaving', false);
        }
    }

    @Action
    public async createUser(params: CreateUserActionParams): Promise<void> {
        await this.context.commit('setErrorObject', null);

        const requestFactory = getRequestFactory();
        this.context.commit('setUserSaving', true);

        try {
            const tokenInfo = await requestFactory.user.createUser.execute(params.username, params.totemAnimal);
            this.context.commit('setSessionData', tokenInfo);
            this.context.commit('setUserDisplayName', params.username);
            this.context.commit('setTotemAnimal', params.totemAnimal);

            const apiClient = requestFactory.updateToken(tokenInfo.token);

        } catch (error) {
            await this.context.commit('setErrorObject', error);
        } finally {
            this.context.commit('setUserSaving', false);
        }
    }

    @Action
    public async updateUser(params: UpdateUserActionParams): Promise<void> {
        if (!this.token) {
            throw new Error('User not logged in');
        }

        await this.context.dispatch('clearError');

        const requestFactory = getRequestFactory();
        this.context.commit('setUserSaving', true);

        try {
            const userItem = await requestFactory.user.updateUser.execute(
                this.userId,
                params.username,
                params.totemAnimal,
            );

            this.context.commit('setUserDisplayName', userItem.username);
            this.context.commit('setTotemAnimal', params.totemAnimal);
        } catch (error) {
            await this.context.commit('setErrorObject', error);
        } finally {
            this.context.commit('setUserSaving', false);
        }
    }

    @Action
    public updateExistingUser(updatedUser: UserItem) {
        const updatedUsers: UserItem[] = [];
        let foundUser = false;

        for (const thisUser of this.users) {
            if (thisUser.id === updatedUser.id) {
                updatedUsers.push(updatedUser);
                foundUser = true;
            } else {
                updatedUsers.push(thisUser);
            }
        }

        if (foundUser) {
            this.context.commit('setUsers', updatedUsers);
        }
    }

    @Action
    public async clearError(): Promise<void> {
        this.context.commit('setErrorObject',  null);
    }
}
