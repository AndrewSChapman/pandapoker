export default class SettingsHelper {
    private baseUrl: string;

    constructor(baseUrl: string) {
        this.baseUrl = baseUrl;
    }

    public getBaseUrl(): string {
        return this.baseUrl;
    }
}
