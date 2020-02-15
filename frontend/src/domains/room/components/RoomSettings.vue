<template>
    <Container heading-text="Get a room!">
        <template slot="content">
            <div class="roomSettings">
                <h1 v-if="!editingExistingRoom">Create a Room</h1>
                <h1 v-if="editingExistingRoom">Edit Room</h1>

                <Form @submit="handleCreateRoomIntent">
                    <slot>
                        <fieldset>
                            <div class="input-group">
                                <label for="name">Please enter a name for this room</label>
                                <input type="text" id="name" name="name"
                                       v-model="name"
                                       @keyup="checkNameOk"
                                       @change="checkNameOk"
                                />
                                <InputChecker :status="nameOk" />
                            </div>
                        </fieldset>

                        <fieldset>
                            <label>Available Voting Points</label>
                            <RoomVoteOptionList
                                    :available-options="roomVoteOptions"
                                    @click="handleVoteOptionSelected"
                            />

                            <BasicButton
                                    display-type="text_only"
                                    button-text="Use Fibonacci points"
                                    @clicked="setFibonacciSequence"
                            />

                            <BasicButton
                                    display-type="text_only"
                                    button-text="Reset points"
                                    @clicked="resetPoints"
                            />
                        </fieldset>

                        <ButtonBar>
                            <slot>
                                <SpinnerButton
                                        button-text="Proceed >>"
                                        display-type="positive"
                                        :loading="saving"
                                        @clicked="handleCreateRoomIntent"
                                />
                            </slot>
                        </ButtonBar>

                        <SuccessMessage :message="successMessage"
                                        :display-time-seconds="2"
                                        v-if="successMessage.length > 0"
                                        @hide="successMessage = ''"
                        />

                        <ErrorMessage
                                v-show="errorMessage.length > 0"
                                :message="errorMessage" />
                    </slot>
                </Form>
            </div>
        </template>
    </Container>
</template>

<script lang="ts">
    // Components
    import { Component, Vue } from 'vue-property-decorator';
    import ButtonBar from '@/domains/ui/containers/ButtonBar.vue';
    import Container from '@/domains/app/components/Container.vue';
    import ErrorMessage from '@/domains/ui/messages/ErrorMessage.vue';
    import Form from '@/domains/ui/containers/Form.vue';
    import InputChecker from '@/domains/ui/input/InputChecker.vue';
    import SuccessMessage from '@/domains/ui/messages/SuccessMessage.vue';
    import SpinnerButton from '@/domains/ui/buttons/SpinnerButton.vue';

    // Typescript
    import { StoreProvider } from '@/domains/shared/storeProvider';
    import Trilean from '@/domains/shared/enums/trilean';
    import RoomVoteOption from '@/domains/room/components/RoomVoteOption.vue';
    import RoomVoteOptionList from '@/domains/room/components/RoomVoteOptionList.vue';
    import { RoomVoteOptionItem } from '@/domains/room/interfaces/RoomVoteOptionItem';
    import BasicButton from '@/domains/ui/buttons/BasicButton.vue';
    import { navigateTo } from '@/domains/shared/helpers/NavigationHelper';

    @Component({
        components: {
            BasicButton,
            RoomVoteOptionList,
            RoomVoteOption,
            ButtonBar,
            Container,
            ErrorMessage,
            Form,
            InputChecker,
            SpinnerButton,
            SuccessMessage,
        },
    })
    export default class RoomSettings extends Vue {
        // Model fields
        public name = '';
        public nameOk: Trilean = Trilean.Unknown;

        public errorMessage: string = '';
        public successMessage: string = '';
        public saving = false;
        public editingExistingRoom = false;

        private $storeProvider!: StoreProvider;

        public async mounted() {
            // See if we're editing an existing room.
            const room = await this.$storeProvider.room.currentRoom;

            if ((room == null) || (room.id.length <= 1)) {
                this.name = '';
                this.editingExistingRoom = false;
            } else {
                this.name = room.name;
                this.editingExistingRoom = true;

                const roomVoteOptions: RoomVoteOptionItem[] = [];

                for (let value = 1; value <= 20; value++) {
                    let selected = false;

                    for (const thisValue of room.vote_options) {
                        if (thisValue === value) {
                            selected = true;
                        }
                    }

                    roomVoteOptions.push({
                        value,
                        selected,
                    });
                }

                this.$storeProvider.room.setRoomVoteOptions(roomVoteOptions);
            }
        }

        public checkNameOk() {
            this.nameOk = this.name.length >= 2 ? Trilean.True : Trilean.False;
        }

        public async handleCreateRoomIntent(): Promise<void> {
            if (this.nameOk !== Trilean.True) {
                this.errorMessage = 'Please enter a name for the room';
                return;
            }

            if (!this.userHasSelectedAtLeastOneRoomVoteOption) {
                this.errorMessage = 'Please select the available point options for this room';
                return;
            }

            this.saving = true;

            const success = await this.$storeProvider.room.createRoom(this.name, this.roomVoteOptions);
            this.saving = false;

            if (success) {
                const room = this.$storeProvider.room.currentRoom;
                if (!room) {
                    this.errorMessage = 'Room could not be loaded';
                    return;
                }

                navigateTo(this.$router).roomDetails(room.id);
            } else {
                this.errorMessage = this.$storeProvider.room.errorMessage;
            }
        }

        public get roomVoteOptions(): RoomVoteOptionItem[] {
            return this.$storeProvider.room.roomVoteOptions;
        }

        public handleVoteOptionSelected(roomVoteOption: RoomVoteOptionItem): void {
            this.$storeProvider.room.setRoomOptionSelected(roomVoteOption);
        }

        public setFibonacciSequence(): void {
            let nextFibonacci = 1;
            const newVoteOptions = this.roomVoteOptions.map((voteOption) => {
                if (voteOption.value === nextFibonacci) {
                    voteOption.selected = true;
                    nextFibonacci = nextFibonacci + voteOption.value;
                } else {
                    voteOption.selected = false;
                }

                return voteOption;
            });

            this.$storeProvider.room.setRoomVoteOptions(newVoteOptions);
        }

        public resetPoints(): void {
            const newVoteOptions = this.roomVoteOptions.map((voteOption) => {
                voteOption.selected = false;
                return voteOption;
            });

            this.$storeProvider.room.setRoomVoteOptions(newVoteOptions);
        }

        private get userHasSelectedAtLeastOneRoomVoteOption(): boolean {
            for (const voteOption of this.roomVoteOptions) {
                if (voteOption.selected) {
                    return true;
                }
            }

            return false;
        }
    }
</script>

<style lang="scss" scoped>
    @import 'src/scss/globals';

    .roomVoteOptionList {
        margin-top: $margin-small;
    }
</style>
