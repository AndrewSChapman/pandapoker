import { AbstractRequest } from '@/domains/shared/abstractRequest';
import { UserItem } from '@/domains/user/interfaces/UserItem';

export class GetUserRequest extends AbstractRequest {
    public async execute(userId: string): Promise<UserItem> {
        return new Promise<UserItem>(async (resolve, reject) => {
            try {
                const response = await this.apiClient.get(`/user/${userId}`);

                if (!response.hasOwnProperty('data')) {
                    reject(Error('GetUserRequest - Invalid response from API'));
                    return;
                }

                resolve(response.data);
            } catch (error) {
                reject(this.convertResponseError(error,
                    'Could not load user from the API'));
            }
        });
    }
}
