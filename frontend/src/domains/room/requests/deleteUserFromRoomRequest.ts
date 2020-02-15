import { AbstractRequest } from '@/domains/shared/abstractRequest';
import { RoomItem } from '@/domains/room/interfaces/RoomItem';

export class DeleteUserFromRoomRequest extends AbstractRequest {
    public async execute(roomId: string, userId: string): Promise<RoomItem> {
        return new Promise<RoomItem>(async (resolve, reject) => {
            try {
                const response = await this.apiClient.delete(`/room/${roomId}/user/${userId}`, {});

                if (!response.hasOwnProperty('data')) {
                    reject(Error('DeleteUserFromRoomRequest - Invalid response from API'));
                    return;
                }

                resolve(response.data);
            } catch (error) {
                reject(this.convertResponseError(error,
                    'Could not delete user from room'));
            }
        });
    }
}
