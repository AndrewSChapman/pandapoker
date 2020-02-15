import { AbstractRequest } from '@/domains/shared/abstractRequest';
import { RoomItem } from '@/domains/room/interfaces/RoomItem';

export class ChangeRoomOwnershipRequest extends AbstractRequest {
    public async execute(roomId: string): Promise<RoomItem> {
        return new Promise<RoomItem>(async (resolve, reject) => {
            try {
                const response = await this.apiClient.patch(`/room/${roomId}/change_ownership`, {});

                if (!response.hasOwnProperty('data')) {
                    reject(Error('ChangeRoomOwnershipRequest - Invalid response from API'));
                    return;
                }

                resolve(response.data);
            } catch (error) {
                reject(this.convertResponseError(error,
                    'Could not change room ownership'));
            }
        });
    }
}
