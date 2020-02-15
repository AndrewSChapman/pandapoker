import { WebSocketEventType } from '@/domains/shared/webSockets/enums/webSocketEventType';
import { RoomItem } from '@/domains/room/interfaces/RoomItem';
import { UserItem } from '@/domains/user/interfaces/UserItem';

export interface WebsocketEventInterface {
    event_id: string;
    event_created_at: number;
    event_created_by: string;
    event_name: WebSocketEventType;
    room?: RoomItem;
    user?: UserItem;
    room_id?: string;
}
