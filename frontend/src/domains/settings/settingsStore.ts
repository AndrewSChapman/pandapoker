import { Module, Mutation, VuexModule } from 'vuex-module-decorators';
import { ModuleType } from '@/domains/shared/enums/moduleType';

@Module
export class SettingsStore extends VuexModule {
    public screenWidth: number = 0;
    public moduleType: ModuleType = ModuleType.ROOMS;

    @Mutation
    public setScreenWidth(value: number) {
        this.screenWidth = value;
    }

    @Mutation
    public setModuleType(value: ModuleType) {
        this.moduleType = value;
    }
}
