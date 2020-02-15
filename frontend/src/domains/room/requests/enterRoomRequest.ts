import { AbstractRequest } from '@/domains/shared/abstractRequest';
import { RoomItem } from '@/domains/room/interfaces/RoomItem';

export class EnterRoomRequest extends AbstractRequest {
    public async execute(roomId: string): Promise<RoomItem[]> {
        return new Promise<RoomItem[]>(async (resolve, reject) => {
            try {
                const response = await this.apiClient.post(`/room/${roomId}/enter`, {});

                if (!response.hasOwnProperty('data')) {
                    reject(Error('EnterRoomRequest - Invalid response from API'));
                    return;
                }

                resolve(response.data);
            } catch (error) {
                reject(this.convertResponseError(error,
                    'Could not enter room'));
            }
        });
    }
}
