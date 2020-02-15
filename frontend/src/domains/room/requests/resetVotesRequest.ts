import { AbstractRequest } from '@/domains/shared/abstractRequest';
import { RoomItem } from '@/domains/room/interfaces/RoomItem';

export class ResetVotesRequest extends AbstractRequest {
    public async execute(roomId: string): Promise<RoomItem> {
        return new Promise<RoomItem>(async (resolve, reject) => {
            try {
                const response = await this.apiClient.patch(`/room/${roomId}/voting/reset`, {});

                if (!response.hasOwnProperty('data')) {
                    reject(Error('ResetVotesRequest - Invalid response from API'));
                    return;
                }

                resolve(response.data);
            } catch (error) {
                reject(this.convertResponseError(error,
                    'Could not reset voting on room'));
            }
        });
    }
}
