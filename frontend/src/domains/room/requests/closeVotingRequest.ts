import { AbstractRequest } from '@/domains/shared/abstractRequest';
import { RoomItem } from '@/domains/room/interfaces/RoomItem';

export class CloseVotingRequest extends AbstractRequest {
    public async execute(roomId: string): Promise<RoomItem> {
        return new Promise<RoomItem>(async (resolve, reject) => {
            try {
                const response = await this.apiClient.patch(`/room/${roomId}/voting/close`, {});

                if (!response.hasOwnProperty('data')) {
                    reject(Error('OpenVotingRequest - Invalid response from API'));
                    return;
                }

                resolve(response.data);
            } catch (error) {
                reject(this.convertResponseError(error,
                    'Could not open voting on room'));
            }
        });
    }
}
