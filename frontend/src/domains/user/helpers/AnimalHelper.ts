import { AnimalType } from '@/domains/user/enums/AnimalType';
import { getLogger } from '@/domains/shared/logger/getLogger';
import { LogLevel } from '@/domains/shared/logger/types/LogLevel';

export class AnimalHelper {
    public static async playSound(animalType: AnimalType): Promise<void> {
        const mp3Path = '/audio/' + animalType + '.mp3';
        const audio = new Audio(mp3Path);

        try {
            await audio.play();
        } catch (error) {
            getLogger().log(LogLevel.WARN, `AnimalHelper::playSound - unable to play animal sound: ${animalType}`);
        }
    }
}
