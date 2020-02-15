import { getBlankRoom, RoomItem } from '@/domains/room/interfaces/RoomItem';
import { Store } from 'vuex';
import { RoomVoteOptionItem } from '@/domains/room/interfaces/RoomVoteOptionItem';
import { CreateRoomActionParams } from '@/domains/room/interfaces/CreateRoomActionParams';
import { DeleteUserFromRoomActionParams } from '@/domains/room/interfaces/DeleteUserFromRoomActionParams';

export class RoomStoreProvider {
    private store: Store<any>;
    private roomStore: any;

    constructor(store: Store<any>, roomStore: any) {
        this.store = store;
        this.roomStore = roomStore;
    }

    get errorMessage(): string {
        if (this.hasError) {
            return this.roomStore.error.message;
        }

        return '';
    }

    get hasError(): boolean {
        return ((this.roomStore.error !== null) && (this.roomStore.error.hasOwnProperty('message'))
            && (this.roomStore.error.message.length > 0));
    }

    public get rooms(): RoomItem[] {
        return this.roomStore.rooms;
    }

    public get roomVoteOptions(): RoomVoteOptionItem[] {
        return this.roomStore.roomVoteOptions;
    }

    public get currentRoom(): RoomItem {
        let room = this.roomStore.currentRoom;
        if (room === null) {
            room = getBlankRoom();
        }

        return room;
    }

    public async setCurrentRoom(room: RoomItem): Promise<void> {
        await this.store.commit('setCurrentRoom', room);
    }

    public async setRoomOptionSelected(roomVoteOption: RoomVoteOptionItem): Promise<void> {
        await this.store.commit('setRoomVoteOption', roomVoteOption);
    }

    public async setRoomVoteOptions(roomVoteOptions: RoomVoteOptionItem[]): Promise<void> {
        await this.store.commit('setRoomVoteOptions', roomVoteOptions);
    }

    public async createRoom(roomName: string, roomVoteOptions: RoomVoteOptionItem[]): Promise<boolean> {
        const params: CreateRoomActionParams = {
            roomName,
            roomVoteOptions,
        };

        await this.store.dispatch('createRoom', params);

        if (this.hasError) {
            return false;
        }

        await this.enterRoom(this.currentRoom.id);

        return !this.hasError;
    }

    public async deleteUserFromRoom(roomId: string, userId: string): Promise<boolean> {
        const params: DeleteUserFromRoomActionParams = {
            roomId,
            userId,
        };

        await this.store.dispatch('deleteUserFromRoom', params);

        return !this.hasError;
    }

    public async exitCurrentRoom(userId: string): Promise<boolean> {
        if (!this.currentRoom) {
            return false;
        }

        const params: DeleteUserFromRoomActionParams = {
            roomId: this.currentRoom.id,
            userId,
        };

        await this.store.dispatch('deleteUserFromRoom', params);

        return !this.hasError;
    }

    public async loadRooms(): Promise<void> {
        await this.store.dispatch('loadRooms');
    }

    public getRoomById(roomId: string): RoomItem|null {
        for (const room of this.rooms) {
            if (room.id === roomId) {
                return room;
            }
        }

        return null;
    }

    public async enterRoom(roomId: string): Promise<boolean> {
        await this.store.dispatch('enterRoom', roomId);
        return !this.hasError;
    }

    public async openVoting(roomId: string): Promise<boolean> {
        await this.store.dispatch('openVoting', roomId);
        return !this.hasError;
    }

    public async closeVoting(roomId: string): Promise<boolean> {
        await this.store.dispatch('closeVoting', roomId);
        return !this.hasError;
    }

    public async resetVotes(roomId: string): Promise<boolean> {
        await this.store.dispatch('resetVotes', roomId);
        return !this.hasError;
    }

    public async vote(roomId: string, vote: number): Promise<boolean> {
        await this.store.dispatch('vote', {
            roomId,
            vote,
        });

        return !this.hasError;
    }

    public async resetCurrentRoom(loggedInUserId: string): Promise<void> {
        if (this.currentRoom && this.currentRoom.id.length > 1) {
            await this.exitCurrentRoom(loggedInUserId);
        }

        const clearedRoomVoteOptions = this.roomVoteOptions.map((roomVoteOption) => {
            roomVoteOption.selected = false;
            return roomVoteOption;
        });

        this.setRoomVoteOptions(clearedRoomVoteOptions);
        await this.store.commit('setCurrentRoom', null);
    }

    public async createRoomFromSocketEvent(roomItem: RoomItem): Promise<void> {
        // The updateRoom mutation will add the room if it doesn't exist.
        await this.store.commit('updateRoom', roomItem);
    }

    public async updateRoomFromSocketEvent(roomItem: RoomItem): Promise<void> {
        await this.store.commit('updateRoom', roomItem);

        if (this.currentRoom.id === roomItem.id) {
            await this.setCurrentRoom(roomItem);
        }
    }

    public async changeRoomOwnership(roomId: string): Promise<boolean> {
        await this.store.dispatch('changeRoomOwnership', roomId);
        return !this.hasError;
    }

    public async deleteCurrentRoom(): Promise<boolean> {
        await this.store.dispatch('deleteCurrentRoom');
        return !this.hasError;
    }

    public async handleRoomDeleted(roomId: string, loggedInUserId: string): Promise<void> {
        await this.store.commit('deleteRoom', roomId);
        await this.store.commit('setCurrentRoom', null);
    }
}
