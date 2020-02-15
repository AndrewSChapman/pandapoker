import { AbstractRequest } from '@/domains/shared/abstractRequest';
import { RoomItem } from '@/domains/room/interfaces/RoomItem';

export class CreateRoomRequest extends AbstractRequest {
    public async execute(roomName: string, roomVoteOptions: number[]): Promise<RoomItem> {
        return new Promise<RoomItem>(async (resolve, reject) => {
            try {
                const response = await this.apiClient.post(`/room`, {
                    name: roomName,
                    vote_options: roomVoteOptions,
                });

                if (!response.hasOwnProperty('data')) {
                    reject(Error('CreateRoomRequest - Invalid response from API'));
                    return;
                }

                resolve(response.data);
            } catch (error) {
                reject(this.convertResponseError(error,
                    'Could not create room'));
            }
        });
    }
}
