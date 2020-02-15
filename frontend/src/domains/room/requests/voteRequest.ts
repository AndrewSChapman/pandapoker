import { AbstractRequest } from '@/domains/shared/abstractRequest';
import { RoomItem } from '@/domains/room/interfaces/RoomItem';

export class VoteRequest extends AbstractRequest {
    public async execute(roomId: string, vote: number): Promise<RoomItem> {
        return new Promise<RoomItem>(async (resolve, reject) => {
            try {
                const response = await this.apiClient.patch(`/room/${roomId}/vote`, {
                    vote,
                });

                if (!response.hasOwnProperty('data')) {
                    reject(Error('VoteRequest - Invalid response from API'));
                    return;
                }

                resolve(response.data);
            } catch (error) {
                reject(this.convertResponseError(error,
                    'Could not post user vote'));
            }
        });
    }
}
