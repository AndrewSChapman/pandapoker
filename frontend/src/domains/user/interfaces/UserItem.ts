import { AnimalType } from '@/domains/user/enums/AnimalType';

export interface UserItem {
    id: string;
    username: string;
    totem_animal: AnimalType;
    created_at: number;
    updated_at: number;
}

export function getEmptyUserItem(): UserItem {
    return {
        id: '0',
        username: 'Unknown',
        totem_animal: AnimalType.MONKEY,
        created_at: 0,
        updated_at: 0,
    };
}
