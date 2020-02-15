import { ApiClient } from '@/domains/shared/apiClient';
import { ErrorObject } from '@/domains/shared/interfaces/errorObject';

export abstract class AbstractRequest {
    protected apiClient: ApiClient;

    constructor(apiClient: ApiClient) {
        this.apiClient = apiClient;
    }

    protected convertResponseError(error: any, defaultMessage: string): ErrorObject {
        if ((error.hasOwnProperty('response')) && (error.response.hasOwnProperty('data'))
            && (error.response.data.hasOwnProperty('error'))) {
            const message = error.response.data.error.message.length > 0 ?
                error.response.data.error.message : 'An error has occurred';

            return this.createErrorObject(message, error.response.data.error.code);
        } else {
            return this.createErrorObject(defaultMessage, '');
        }
    }

    private createErrorObject(message: string, code: string): ErrorObject {
        return {
            message,
            code,
            toString: () => {
                return message;
            },
        };
    }
}
