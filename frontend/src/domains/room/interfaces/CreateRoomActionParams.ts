import { RoomVoteOptionItem } from '@/domains/room/interfaces/RoomVoteOptionItem';

export interface CreateRoomActionParams {
    roomName: string;
    roomVoteOptions: RoomVoteOptionItem[];
}
