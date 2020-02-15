import DateHelper from './dateHelper';
import BreakPointHelper from './breakPointHelper';
import SettingsHelper from './settingsHelper';
import { ServerErrorHelper } from './serverErrorHelper';
import { Environment } from '@/domains/shared/enums/Environment';

export default class HelperFactory {
    private vue: any;
    private vueStore: any;
    private readonly environment: Environment;
    private dateHelperInstance!: DateHelper;
    private breakPointHelperInstance!: BreakPointHelper;
    private settingsHelperInstance!: SettingsHelper;
    private serverErrorHelperInstance!: ServerErrorHelper;

    private readonly baseUrl: string;

    constructor(baseUrl: string, environment: Environment, vueStore: any) {
        this.baseUrl = baseUrl;
        this.environment = environment;
        this.vueStore = vueStore;
    }

    public install(vue: any) {
        Object.defineProperty(vue.prototype, '$helperFactory', { value: this });
        this.vue = vue;
    }

    get dateHelper(): DateHelper {
        if (!this.dateHelperInstance) {
            this.dateHelperInstance = new DateHelper();
        }

        return this.dateHelperInstance;
    }

    get breakPoint(): BreakPointHelper {
        if (!this.breakPointHelperInstance) {
            this.breakPointHelperInstance = new BreakPointHelper();
        }

        return this.breakPointHelperInstance;
    }

    get settingsHelper(): SettingsHelper {
        if (!this.settingsHelperInstance) {
            this.settingsHelperInstance = new SettingsHelper(this.baseUrl);
        }

        return this.settingsHelperInstance;
    }

    get serverErrorHelper(): ServerErrorHelper {
        if (!this.serverErrorHelperInstance) {
            this.serverErrorHelperInstance = new ServerErrorHelper();
        }

        return this.serverErrorHelperInstance;
    }
}
