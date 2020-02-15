import { AbstractRequest } from '@/domains/shared/abstractRequest';
import { RoomItem } from '@/domains/room/interfaces/RoomItem';

export class RoomListRequest extends AbstractRequest {
    public async execute(): Promise<RoomItem[]> {
        return new Promise<RoomItem[]>(async (resolve, reject) => {
            try {
                const response = await this.apiClient.get(`/rooms`);

                if (!response.hasOwnProperty('data')) {
                    reject(Error('RoomListRequest - Invalid response from API'));
                    return;
                }

                resolve(response.data);
            } catch (error) {
                reject(this.convertResponseError(error,
                    'Could not load rooms from the API'));
            }
        });
    }
}
