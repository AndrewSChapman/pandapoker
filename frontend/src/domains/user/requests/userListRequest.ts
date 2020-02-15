import { AbstractRequest } from '@/domains/shared/abstractRequest';
import { UserItem } from '@/domains/user/interfaces/UserItem';

export class UserListRequest extends AbstractRequest {
    public async execute(): Promise<UserItem[]> {
        return new Promise<UserItem[]>(async (resolve, reject) => {
            try {
                const response = await this.apiClient.get(`/users`);

                if (!response.hasOwnProperty('data')) {
                    reject(Error('UserListRequest - Invalid response from API'));
                    return;
                }

                resolve(response.data);
            } catch (error) {
                reject(this.convertResponseError(error,
                    'Could not load users from the API'));
            }
        });
    }
}
