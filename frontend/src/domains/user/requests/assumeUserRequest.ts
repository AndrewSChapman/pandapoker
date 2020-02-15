import { AbstractRequest } from '@/domains/shared/abstractRequest';
import { TokenInfo} from '@/domains/user/interfaces/TokenInfo';

export class AssumeUserRequest extends AbstractRequest {
    public async execute(username: string): Promise<TokenInfo> {
        return new Promise<TokenInfo>(async (resolve, reject) => {
            try {
                const response = await this.apiClient.post(`/user/assume`, {
                    username,
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
