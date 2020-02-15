import { Action, Module, Mutation, VuexModule } from 'vuex-module-decorators';

import { RoomItem } from '@/domains/room/interfaces/RoomItem';
import { getLogger } from '@/domains/shared/logger/getLogger';
import { LogLevel } from '@/domains/shared/logger/types/LogLevel';
import { getRequestFactory } from '@/domains/shared/http/requestFactory/getRequestFactory';
import { RoomVoteOptionItem } from '@/domains/room/interfaces/RoomVoteOptionItem';
import { ErrorObject } from '@/domains/shared/interfaces/errorObject';
import { CreateRoomActionParams } from '@/domains/room/interfaces/CreateRoomActionParams';
import { DeleteUserFromRoomActionParams } from '@/domains/room/interfaces/DeleteUserFromRoomActionParams';
import { VoteActionParams } from '@/domains/room/interfaces/VoteActionParams';

@Module
export class RoomStore extends VuexModule {
    /******************************
     * STATE
     *****************************/
    public error: ErrorObject|null = null;
    public currentRoom: RoomItem|null = null;
    public rooms: RoomItem[] = [];
    public saving: boolean = false;

    public roomVoteOptions: RoomVoteOptionItem[] = [
        { value: 1, selected: false },
        { value: 2, selected: false },
        { value: 3, selected: false },
        { value: 4, selected: false },
        { value: 5, selected: false },
        { value: 6, selected: false },
        { value: 7, selected: false },
        { value: 8, selected: false },
        { value: 9, selected: false },
        { value: 10, selected: false },
        { value: 11, selected: false },
        { value: 12, selected: false },
        { value: 13, selected: false },
        { value: 14, selected: false },
        { value: 15, selected: false },
        { value: 16, selected: false },
        { value: 17, selected: false },
        { value: 18, selected: false },
        { value: 19, selected: false },
        { value: 20, selected: false },
    ];

    /******************************
     * MUTATIONS
     *****************************/
    @Mutation
    public deleteRoom(roomId: string) {
        const newRooms = this.rooms.filter((room) => {
            return room.id !== roomId;
        });

        this.rooms = newRooms;
    }

    @Mutation
    public setRoomErrorObject(value: ErrorObject) {
        this.error = value;

        if ((this.error) && (this.error.message) && (this.error.message.length > 0)) {
            const logger = getLogger();
            logger.log(LogLevel.ERROR, this.error.message);
        }
    }

    @Mutation
    public setRooms(rooms: RoomItem[]) {
        this.rooms = rooms;
    }

    @Mutation
    public setRoomSaving(value: boolean) {
        this.saving = value;
    }

    @Mutation
    public async setRoomVoteOption(roomVoteOption: RoomVoteOptionItem): Promise<void> {
        const newVoteOptions = [...this.roomVoteOptions];
        for (const thisVoteOption of newVoteOptions) {
            if (thisVoteOption.value === roomVoteOption.value) {
                thisVoteOption.selected = roomVoteOption.selected;
                break;
            }
        }

        this.roomVoteOptions = newVoteOptions;
    }

    @Mutation
    public async setRoomVoteOptions(newVoteOptions: RoomVoteOptionItem[]): Promise<void> {
        this.roomVoteOptions = newVoteOptions;
    }

    @Mutation
    public setCurrentRoom(room: RoomItem|null) {
        this.currentRoom = room;
    }

    @Mutation
    public updateRoom(roomToUpdate: RoomItem) {
        const newRooms = [];
        let found = false;

        // Find the matching room and replace the old room with the new.
        for (const thisRoom of this.rooms) {
            if (thisRoom.id === roomToUpdate.id) {
                newRooms.push(roomToUpdate);
                found = true;
            } else {
                newRooms.push(thisRoom);
            }
        }

        // If it's a new room it wont have been found in the existing list, so add it.
        if (!found) {
            newRooms.push(roomToUpdate);
        }

        this.rooms = newRooms;
    }

    /******************************
     * ACTIONS
     *****************************/
    @Action
    public async loadRooms(): Promise<void> {
        const logger = getLogger();
        try {
            const requestFactory = getRequestFactory();
            const rooms = await requestFactory.room.roomList.execute();
            this.context.commit('setRooms', rooms);
        } catch (error) {
            logger.log(LogLevel.ERROR, `Error loading rooms: ${error.toString()}`);
        }
    }

    @Action
    public async createRoom(params: CreateRoomActionParams): Promise<void> {
        await this.context.commit('setRoomErrorObject', null);

        const requestFactory = getRequestFactory();
        await this.context.commit('setRoomSaving', true);

        const selectedRoomPointOptions = params.roomVoteOptions.filter((roomVoteOption) => {
            return roomVoteOption.selected;
        }).map((roomVoteOption) => {
           return roomVoteOption.value;
        });

        try {
            const roomItem = await requestFactory.room.createRoom.execute(params.roomName, selectedRoomPointOptions);
            await this.context.commit('setCurrentRoom', roomItem);
            await this.context.commit('updateRoom', roomItem);
        } catch (error) {
            await this.context.commit('setRoomErrorObject', error);
            await this.context.commit('setCurrentRoom', null);
        } finally {
            await this.context.commit('setRoomSaving', false);
        }
    }

    @Action
    public async enterRoom(roomId: string): Promise<void> {
        await this.context.commit('setRoomErrorObject', null);

        const requestFactory = getRequestFactory();
        await this.context.commit('setRoomSaving', true);

        try {
            const roomItem = await requestFactory.room.enterRoom.execute(roomId);
            await this.context.commit('updateRoom', roomItem);
        } catch (error) {
            await this.context.commit('setRoomErrorObject', error);
        } finally {
            await this.context.commit('setRoomSaving', false);
        }
    }

    @Action
    public async deleteUserFromRoom(params: DeleteUserFromRoomActionParams): Promise<void> {
        await this.context.commit('setRoomErrorObject', null);

        const requestFactory = getRequestFactory();
        await this.context.commit('setRoomSaving', true);

        try {
            const roomItem = await requestFactory.room.deleteUserFromRoom.execute(params.roomId, params.userId);
            await this.context.commit('updateRoom', roomItem);
            await this.context.commit('setCurrentRoom', roomItem);
        } catch (error) {
            await this.context.commit('setRoomErrorObject', error);
            await this.context.commit('setCurrentRoom', null);
        } finally {
            await this.context.commit('setRoomSaving', false);
        }
    }

    @Action
    public async openVoting(roomId: string): Promise<void> {
        await this.context.commit('setRoomErrorObject', null);

        const requestFactory = getRequestFactory();
        await this.context.commit('setRoomSaving', true);

        try {
            const roomItem = await requestFactory.room.openVoting.execute(roomId);
            await this.context.commit('updateRoom', roomItem);
            await this.context.commit('setCurrentRoom', roomItem);
        } catch (error) {
            await this.context.commit('setRoomErrorObject', error);
            await this.context.commit('setCurrentRoom', null);
        } finally {
            await this.context.commit('setRoomSaving', false);
        }
    }

    @Action
    public async closeVoting(roomId: string): Promise<void> {
        await this.context.commit('setRoomErrorObject', null);

        const requestFactory = getRequestFactory();
        await this.context.commit('setRoomSaving', true);

        try {
            const roomItem = await requestFactory.room.closeVoting.execute(roomId);
            await this.context.commit('updateRoom', roomItem);
            await this.context.commit('setCurrentRoom', roomItem);
        } catch (error) {
            await this.context.commit('setRoomErrorObject', error);
            await this.context.commit('setCurrentRoom', null);
        } finally {
            await this.context.commit('setRoomSaving', false);
        }
    }

    @Action
    public async vote(params: VoteActionParams): Promise<void> {
        await this.context.commit('setRoomErrorObject', null);

        const requestFactory = getRequestFactory();
        await this.context.commit('setRoomSaving', true);

        try {
            const roomItem = await requestFactory.room.vote.execute(params.roomId, params.vote);
            await this.context.commit('updateRoom', roomItem);
            await this.context.commit('setCurrentRoom', roomItem);
        } catch (error) {
            await this.context.commit('setRoomErrorObject', error);
            await this.context.commit('setCurrentRoom', null);
        } finally {
            await this.context.commit('setRoomSaving', false);
        }
    }

    @Action
    public async resetVotes(roomId: string): Promise<void> {
        await this.context.commit('setRoomErrorObject', null);

        const requestFactory = getRequestFactory();
        await this.context.commit('setRoomSaving', true);

        try {
            const roomItem = await requestFactory.room.resetVotes.execute(roomId);
            await this.context.commit('updateRoom', roomItem);
            await this.context.commit('setCurrentRoom', roomItem);
        } catch (error) {
            await this.context.commit('setRoomErrorObject', error);
            await this.context.commit('setCurrentRoom', null);
        } finally {
            await this.context.commit('setRoomSaving', false);
        }
    }

    @Action
    public async changeRoomOwnership(roomId: string): Promise<void> {
        await this.context.commit('setRoomErrorObject', null);

        const requestFactory = getRequestFactory();
        await this.context.commit('setRoomSaving', true);

        try {
            const roomItem = await requestFactory.room.changeRoomOwnership.execute(roomId);
            await this.context.commit('updateRoom', roomItem);
            await this.context.commit('setCurrentRoom', roomItem);
        } catch (error) {
            await this.context.commit('setRoomErrorObject', error);
            await this.context.commit('setCurrentRoom', null);
        } finally {
            await this.context.commit('setRoomSaving', false);
        }
    }

    @Action
    public async deleteCurrentRoom(): Promise<void> {
        if (!this.currentRoom) {
            return;
        }

        await this.context.commit('setRoomErrorObject', null);

        const requestFactory = getRequestFactory();
        await this.context.commit('setRoomSaving', true);

        const roomId = this.currentRoom.id;

        try {
            await requestFactory.room.deleteRoom.execute(roomId);
            await this.context.commit('setCurrentRoom', null);
            await this.context.commit('deleteRoom', roomId);
        } catch (error) {
            await this.context.commit('setRoomErrorObject', error);
        } finally {
            await this.context.commit('setRoomSaving', false);
        }
    }
}
