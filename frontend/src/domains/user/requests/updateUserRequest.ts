import { AbstractRequest } from '@/domains/shared/abstractRequest';
import { UserItem } from '@/domains/user/interfaces/UserItem';

export class UpdateUserRequest extends AbstractRequest {
    public async execute(userId: string, username: string, totemAnimal: string): Promise<UserItem> {
        return new Promise<UserItem>(async (resolve, reject) => {
            try {
                const response = await this.apiClient.patch(`/user/${userId}`, {
                    username,
                    totem_animal: totemAnimal,
                });

                if (!response.hasOwnProperty('data')) {
                    reject(Error('UpdateUserRequest - Invalid response from API'));
                    return;
                }

                resolve(response.data);
            } catch (error) {
                reject(this.convertResponseError(error,
                    'Could not update user'));
            }
        });
    }
}
