import { AbstractRequest } from '@/domains/shared/abstractRequest';

export class DeleteRoomRequest extends AbstractRequest {
    public async execute(roomId: string): Promise<void> {
        return new Promise<void>(async (resolve, reject) => {
            try {
                const response = await this.apiClient.delete(`/room/${roomId}`, {});

                if (!response.hasOwnProperty('data')) {
                    reject(Error('DeleteRoomRequest - Invalid response from API'));
                    return;
                }

                resolve();
            } catch (error) {
                reject(this.convertResponseError(error,
                    'Could not delete room'));
            }
        });
    }
}
