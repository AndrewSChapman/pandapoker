<template>
    <Container heading-text="Get a room!">
        <template slot="content">
            <div class="rooms">
                <h1>Available Rooms</h1>

                <Spinner v-if="loading" />

                <div v-if="!loading">
                    <p v-if="noRoomsAvailable">
                        There are currently no rooms available.<br>
                        PandaPoker is a sad panda!
                    </p>

                    <ErrorMessage :message="errorMessage" v-if="errorMessage.length > 0" />

                    <IncludeIf breakpoint="medium" operator=">=">
                        <Table v-if="!noRoomsAvailable">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Created By</th>
                                <th>No. Participants</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="room in rooms">
                                <td>{{ room.name }}</td>
                                <td>{{ getUserNameFromId(room.created_by_user_id) }}</td>
                                <td>{{ room.participants.length }}</td>
                                <td>
                                    <BasicButton v-if="userLoggedIn"
                                                 display-type="info"
                                                 button-text="Enter Room"
                                                 @clicked="handleEnterRoom(room.id)"
                                    />
                                    <p v-else>You may not enter a room until you
                                        <BasicButton
                                                display-type="text_only"
                                                button-text="set your identity"
                                                @clicked="handleSetIdentity"
                                        />
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </Table>
                    </IncludeIf>


                    <IncludeIf breakpoint="medium" operator="<=">
                        <Table v-if="!noRoomsAvailable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="room in rooms">
                                    <td>{{ room.name }}</td>
                                    <td>
                                        <BasicButton v-if="userLoggedIn"
                                                display-type="info"
                                                button-text="Enter"
                                                @clicked="handleEnterRoom(room.id)"
                                        />
                                        <p v-else>You may not enter a room until you
                                            <BasicButton
                                                    display-type="text_only"
                                                    button-text="set your identity"
                                                    @clicked="handleSetIdentity"
                                            />
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </Table>
                    </IncludeIf>
                    <ButtonBar>
                        <slot>
                            <BasicButton button-text="Create Room" display-type="positive" @clicked="handleCreateRoom" />
                        </slot>
                    </ButtonBar>
                </div>
            </div>
        </template>
    </Container>
</template>

<script lang="ts">
    // Components
    import { Component, Vue } from 'vue-property-decorator';
    import ButtonBar from '@/domains/ui/containers/ButtonBar.vue';
    import Container from '@/domains/app/components/Container.vue';

    // Typescript
    import AppNavigation from '@/domains/app/components/AppNavigation.vue';
    import { RoomItem } from '@/domains/room/interfaces/RoomItem';
    import BasicButton from '@/domains/ui/buttons/BasicButton.vue';
    import IncludeIf from '@/domains/shared/includeIf.vue';
    import { navigateTo } from '@/domains/shared/helpers/NavigationHelper';
    import { StoreProvider } from '@/domains/shared/storeProvider';
    import Spinner from '@/domains/ui/Spinner.vue';
    import Table from '@/domains/ui/containers/Table.vue';
    import ErrorMessage from '@/domains/ui/messages/ErrorMessage.vue';

    @Component({
        components: {
            ErrorMessage,
            Table,
            Spinner,
            BasicButton,
            AppNavigation,
            ButtonBar,
            Container,
            IncludeIf,
        },
    })
    export default class RoomList extends Vue {
        public loading = false;
        public errorMessage = '';

        private $storeProvider!: StoreProvider;

        public async mounted() {
            this.loading = true;
            await this.$storeProvider.room.loadRooms();
            this.loading = false;
        }

        get rooms(): RoomItem[] {
            return this.$storeProvider.room.rooms;
        }

        get userLoggedIn(): boolean {
            return this.$storeProvider.user.isLoggedIn;
        }

        get noRoomsAvailable(): boolean {
            return this.rooms.length === 0;
        }

        public getUserNameFromId(userId: string): string {
            const user = this.$storeProvider.user.getUserById(userId);
            return user.username;
        }

        public handleCreateRoom(): void {
            if (this.$storeProvider.user.isLoggedIn) {
                navigateTo(this.$router).roomSettings();
            } else {
                // If the user wants to create a room but they are not logged in,
                // remember the intent and then navigate to the create user screen.
                // We will redirect back to creating a room once the profile has been created.
                this.$storeProvider.user.flagUserWantsToCreateRoom();
                navigateTo(this.$router).userProfile();
            }
        }

        public async handleEnterRoom(roomId: string): Promise<void> {
            this.loading = true;
            const success = await this.$storeProvider.room.enterRoom(roomId);
            this.loading = false;
            if (success) {
                navigateTo(this.$router).roomDetails(roomId);
            } else {
                this.errorMessage = this.$storeProvider.room.errorMessage;
            }
        }

        public handleSetIdentity(): void {
            navigateTo(this.$router).userProfile();
        }
    }
</script>

<style lang="scss" scoped>
    @import "src/scss/globals";

    .error-message {
        margin-bottom: $margin;
    }
</style>
