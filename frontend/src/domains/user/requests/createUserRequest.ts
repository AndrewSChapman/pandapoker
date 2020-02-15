import { AbstractRequest } from '@/domains/shared/abstractRequest';
import { TokenInfo} from '@/domains/user/interfaces/TokenInfo';

export class CreateUserRequest extends AbstractRequest {
    public async execute(username: string, totemAnimal: string): Promise<TokenInfo> {
        return new Promise<TokenInfo>(async (resolve, reject) => {
            try {
                const response = await this.apiClient.post(`/user`, {
                    username,
                    totem_animal: totemAnimal,
                });

                if (!response.hasOwnProperty('data')) {
                    reject(Error('CreateUserRequest - Invalid response from API'));
                    return;
                }

                resolve(response.data);
            } catch (error) {
                reject(this.convertResponseError(error,
                    'Could not create user'));
            }
        });
    }
}
