import { RoomListRequest } from '@/domains/room/requests/roomListRequest';
import { ApiClient } from '@/domains/shared/apiClient';
import { CreateRoomRequest } from '@/domains/room/requests/createRoomRequest';
import { EnterRoomRequest } from '@/domains/room/requests/enterRoomRequest';
import { DeleteUserFromRoomRequest } from '@/domains/room/requests/deleteUserFromRoomRequest';
import { OpenVotingRequest } from '@/domains/room/requests/openVotingRequest';
import { CloseVotingRequest } from '@/domains/room/requests/closeVotingRequest';
import { VoteRequest } from '@/domains/room/requests/voteRequest';
import { ResetVotesRequest } from '@/domains/room/requests/resetVotesRequest';
import { ChangeRoomOwnershipRequest } from '@/domains/room/requests/changeRoomOwnershipRequest';
import { DeleteRoomRequest } from '@/domains/room/requests/deleteRoomRequest';

export class RoomRequestFactory {
    private readonly apiClient: ApiClient;

    constructor(apiClient: ApiClient) {
        this.apiClient = apiClient;
    }

    public get changeRoomOwnership(): ChangeRoomOwnershipRequest {
        return new ChangeRoomOwnershipRequest(this.apiClient);
    }

    public get closeVoting(): CloseVotingRequest {
        return new CloseVotingRequest(this.apiClient);
    }

    public get createRoom(): CreateRoomRequest {
        return new CreateRoomRequest(this.apiClient);
    }

    public get deleteRoom(): DeleteRoomRequest {
        return new DeleteRoomRequest(this.apiClient);
    }

    public get deleteUserFromRoom(): DeleteUserFromRoomRequest {
        return new DeleteUserFromRoomRequest(this.apiClient);
    }

    public get enterRoom(): EnterRoomRequest {
        return new EnterRoomRequest(this.apiClient);
    }

    public get openVoting(): OpenVotingRequest {
        return new OpenVotingRequest(this.apiClient);
    }

    public get resetVotes(): ResetVotesRequest {
        return new ResetVotesRequest(this.apiClient);
    }

    public get roomList(): RoomListRequest {
        return new RoomListRequest(this.apiClient);
    }

    public get vote(): VoteRequest {
        return new VoteRequest(this.apiClient);
    }
}
