import { StoreProvider } from '@/domains/shared/storeProvider';
import { WebsocketEventInterface } from '@/domains/shared/webSockets/interfaces/websockEventInterface';
import { WebSocketEventType } from '@/domains/shared/webSockets/enums/webSocketEventType';
import { getLogger } from '@/domains/shared/logger/getLogger';
import { LogLevel } from '@/domains/shared/logger/types/LogLevel';
import { AnimalHelper } from '@/domains/user/helpers/AnimalHelper';
import VueRouter from 'vue-router';
import { navigateTo } from '@/domains/shared/helpers/NavigationHelper';

export class WebSocketManager {
    private connected: boolean = false;
    private socket: WebSocket|null = null;

    constructor(
        private websocketDomain: string,
        private websocketPort: number,
        private useSSL: boolean,
        private storeProvider: StoreProvider,
        private router: VueRouter,
    ) {

    }

    public init(): void {
        this.storeProvider.user.setSocketStatus(false);

        const protocol = this.useSSL ? 'wss' : 'ws';

        this.socket = new WebSocket(`${protocol}://${this.websocketDomain}:${this.websocketPort}/`);

        this.socket.onmessage = (message) => {
            const messageData = JSON.parse(message.data);
            if (!messageData.hasOwnProperty('id')) {
                return;
            }

            const eventSequenceNumber = parseInt(messageData.id, 10);

            if (messageData.hasOwnProperty('event_data')) {
                const event = messageData.event_data;
                this.handleEvent(eventSequenceNumber, event);
            }
        };

        this.socket.onopen = () => {
            this.connected = true;
            this.storeProvider.user.setSocketStatus(true);
        };

        this.socket.onclose = () => {
            this.connected = true;
            this.storeProvider.user.setSocketStatus(false);

            this.init();
        };

        setTimeout(() => {
            if (!this.connected) {
                this.init();
            }
        }, 500);
    }

    private async handleEvent(changeLogId: number, event: WebsocketEventInterface) {
        getLogger().log(LogLevel.INFO, 'WebSocketManager::handleEvent - Handling event id: ' + changeLogId);

        switch (event.event_name) {
            case WebSocketEventType.USER_CREATED:
                if (!event.user) {
                    return;
                }

                getLogger().log(LogLevel.INFO, 'WebSocketManager::handleEvent - Handling new user event');

                await this.storeProvider.user.handleNewUserFromSocketEvent(event.user);
                break;

            case WebSocketEventType.USER_UPDATED:
                if (!event.user) {
                    return;
                }

                getLogger().log(LogLevel.INFO, 'WebSocketManager::handleEvent - Handling updated user event');

                await this.storeProvider.user.handleUpdateUserFromSocketEvent(event.user);
                break;

            case WebSocketEventType.ROOM_CREATED:
                if (!event.room) {
                    return;
                }

                getLogger().log(LogLevel.INFO, 'WebSocketManager::handleEvent - Handling room created event');

                await this.storeProvider.room.createRoomFromSocketEvent(event.room);
                break;

            case WebSocketEventType.ROOM_UPDATED:
                if (!event.room) {
                    return;
                }

                getLogger().log(LogLevel.INFO, 'WebSocketManager::handleEvent - Handling updated room event');

                await this.storeProvider.room.updateRoomFromSocketEvent(event.room);

                break;

            case WebSocketEventType.ROOM_DELETED:
                if (!event.room) {
                    return;
                }

                getLogger().log(LogLevel.INFO, 'WebSocketManager::handleEvent - Handling deleted room event');

                // If the room the user is in is the room that was deleted, we need to redirect
                const userCurrentRoom = this.storeProvider.room.currentRoom;
                if (userCurrentRoom) {
                    if (userCurrentRoom.id === event.room.id) {
                        await this.storeProvider.room.handleRoomDeleted(event.room.id, this.storeProvider.user.loggedInUserId);
                        navigateTo(this.router).roomList();
                    }
                }

                break;

            case WebSocketEventType.ROOM_VOTE:
                if ((!event.event_created_by) || (!event.room_id)) {
                    return;
                }

                getLogger().log(LogLevel.INFO, 'WebSocketManager::handleEvent - Handling room vote');

                const roomId = event.room_id;
                const userId = event.event_created_by;
                const currentRoom = this.storeProvider.room.currentRoom;
                if (currentRoom.id.length <= 1) {
                    getLogger().log(LogLevel.INFO, 'WebSocketManager::handleEvent - vote not for users current room');
                    return;
                }

                const userWhoVoted = this.storeProvider.user.getUserById(userId);
                await AnimalHelper.playSound(userWhoVoted.totem_animal);
                break;

            default:
                getLogger().log(
                    LogLevel.WARN,
                    'WebSocketManager::handleEvent - Unhandled event type: ' + event.event_name,
                );
                break;
        }

        await this.storeProvider.user.setLastChangeLogId(changeLogId);
    }
}
