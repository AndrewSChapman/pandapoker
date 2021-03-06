import Axios from 'axios';

/**
 * A client to talk to the Core API
 */
export class ApiClient {
    private readonly apiUrl: string;
    private token: string;

    constructor(apiUrl: string) {
        this.apiUrl = apiUrl;
        this.token = '';
    }

    public install(vue: any) {
        Object.defineProperty(vue.prototype, '$http', { value: this });
    }

    public setToken(token: string) {
        this.token = token;
    }

    public async get(url: string): Promise<any> {
        return this.request(url, 'GET', {});
    }

    public async post(url: string, params: object): Promise<any> {
        return this.request(url, 'POST', params);
    }

    public async patch(url: string, params: object): Promise<any> {
        return this.request(url, 'PATCH', params);
    }

    public async put(url: string, params: object): Promise<any> {
        return this.request(url, 'PUT', params);
    }

    public async delete(url: string, params: object): Promise<any> {
        return this.request(url, 'DELETE', params);
    }

    public async uploadFile(url: string, formData: FormData): Promise<any> {
        return this.request(url, 'POST', formData, this.formMultipartContentType);
    }

    private async request(
        url: string,
        method: string,
        params: object,
        contentType: string = 'application/json',
    ): Promise<any> {
        let data: any;

        if (contentType === this.formMultipartContentType) {
            data = params;
        } else if (method !== 'GET') {
            data = JSON.stringify(params);
        }

        const requestUrl = this.apiUrl + url;

        const headers: any = {
            'Content-Type': contentType,
        };

        if (this.token && this.token.length > 0) {
            headers.Authorization = `Bearer ${this.token}`;
        }

        return Axios({
            baseURL: requestUrl,
            data,
            method,
            headers,
        });
    }

    get formMultipartContentType(): string {
        return 'multipart/form-data';
    }
}
