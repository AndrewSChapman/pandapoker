<template>
    <Container :heading-text="headingText">
        <template slot="content">
            <Spinner v-if="!loaded" />

            <div class="roomDetail" v-if="loaded">
                <h1>{{ room.name }}</h1>
                <div class="ellipsisWrapper">
                    <EllipsisMenu :menu-options="getRoomMenuOptions()" @clicked="handleMenuOptionClick" />
                </div>

                <ErrorMessage :message="errorMessage" v-if="errorMessage.length > 0" />

                <ButtonBar v-if="showOpenVotingButton" class="actions">
                    <SpinnerButton v-if="showOpenVotingButton"
                                   button-text="Open Voting"
                                   display-type="positive"
                                   :loading="roomVotingStatusChanging"
                                   @clicked="openVoting"
                    />
                </ButtonBar>

                <div class="myVote" v-if="room.voting_open && getParticipantIsVoting(this.loggedInUserId)">
                    <div class="heading">
                        <h2>My Vote</h2>
                        <Spinner v-if="userVoteIsPosting" />
                    </div>

                    <RoomVoteOptionList
                            :available-options="roomVoteOptions"
                            @click="castVote"
                    />
                </div>

                <div class="participants" v-if="showParticipants">
                    <h2>Participants in Room</h2>

                    <Table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Vote</th>
                                <th v-if="loggedInUserOwnsRoom(room.id)">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="participant in participants">
                                <td>
                                    <Animal
                                            :animal-type="getUserById(participant.user_id).totem_animal"
                                            :selected="false"
                                    />
                                    {{ getUserById(participant.user_id).username }}
                                </td>
                                <td class="vote">{{ getUserVoteInformation(participant.user_id) }}</td>
                                <td v-if="loggedInUserOwnsRoom(room.id)">
                                    <SpinnerButton
                                            display-type="text_only"
                                            button-text="Remove User"
                                            :loading="roomBeingChangedId === room.id"
                                            @clicked="removeUserFromRoom(room.id, participant.user_id)"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </Table>

                    <dl class="winningVote" v-if="hasWinningVote && everyoneHasVoted">
                        <dt>Winning Vote</dt>
                        <dd>{{ room.winning_vote }}</dd>
                    </dl>

                    <SpinnerButton v-if="room.voting_open && room.votes.length > 0 && loggedInUserOwnsRoom(room.id)"
                                   button-text="Reset Votes"
                                   display-type="text_only"
                                   :loading="votesResetting"
                                   @clicked="resetVotes"
                    />
                </div>
            </div>
        </template>
    </Container>
</template>

<script lang="ts">
    // Components
    import { Component, Vue, Prop, Watch } from 'vue-property-decorator';

    // Typescript
    import Container from '@/domains/app/components/Container.vue';
    import { navigateTo } from '@/domains/shared/helpers/NavigationHelper';
    import { StoreProvider } from '@/domains/shared/storeProvider';
    import { RoomItem } from '@/domains/room/interfaces/RoomItem';
    import Spinner from '@/domains/ui/Spinner.vue';
    import SpinnerButton from '@/domains/ui/buttons/SpinnerButton.vue';
    import Table from '@/domains/ui/containers/Table.vue';
    import { RoomParticipant } from '@/domains/room/interfaces/RoomParticipant';
    import { UserItem } from '@/domains/user/interfaces/UserItem';
    import ErrorMessage from '@/domains/ui/messages/ErrorMessage.vue';
    import RoomVoteOptionList from '@/domains/room/components/RoomVoteOptionList.vue';
    import { RoomVoteOptionItem } from '@/domains/room/interfaces/RoomVoteOptionItem';
    import ButtonBar from '@/domains/ui/containers/ButtonBar.vue';
    import BasicButton from '@/domains/ui/buttons/BasicButton.vue';
    import EllipsisMenu from '@/domains/ui/selectors/EllipsisMenu/EllipsisMenu.vue';
    import { EllipsisMenuItemInterface } from '@/domains/ui/selectors/EllipsisMenu/EllipsisMenuItemInterface';
    import Animal from '@/domains/user/components/Animal.vue';

    @Component({
        components: {
            Animal,
            EllipsisMenu,
            BasicButton, ButtonBar, RoomVoteOptionList, ErrorMessage, Table, SpinnerButton, Spinner, Container},
    })
    export default class RoomDetail extends Vue {
        public roomName = 'Loading...';
        public loaded = false;
        public errorMessage: string = '';
        public roomBeingChangedId = '';
        public roomVotingStatusChanging = false;
        public userVoteIsPosting = false;
        public votesResetting = false;

        private $storeProvider!: StoreProvider;

        public mounted(): void {
            const roomId = this.$route.params.roomId;
            if (!roomId) {
                navigateTo(this.$router).roomList();
            }

            this.loadRoom(roomId);
        }

        public get headingText(): string {
            return !this.room.voting_open ? 'Voting Not Yet Open' : 'Voting Time!';
        }

        public get room(): RoomItem {
            return this.$storeProvider.room.currentRoom;
        }

        public get everyoneHasVoted(): boolean {
            return this.room.participants.length === this.room.votes.length;
        }

        public get hasWinningVote(): boolean {
            const currentRoom: RoomItem = this.room;

            if ((currentRoom.winning_vote) && (currentRoom.winning_vote > 0)) {
                return true;
            }

            return false;
        }

        public get participants(): RoomParticipant[] {
            return this.room.participants;
        }

        public get showParticipants(): boolean {
            return true;
        }

        public get loggedInUserId(): string {
            return this.$storeProvider.user.loggedInUserId;
        }

        public get showOpenVotingButton(): boolean {
            return !this.room.voting_open && this.loggedInUserOwnsRoom(this.room.id);
        }

        public getUserById(userId: string): UserItem {
            return this.$storeProvider.user.getUserById(userId);
        }

        public get roomVoteOptions(): RoomVoteOptionItem[] {
            const roomVoteOptions: RoomVoteOptionItem[] = [];

            const loggedInUserId = this.$storeProvider.user.loggedInUserId;
            let currentLoggedInUserVote = 0;

            for (const vote of this.room.votes) {
                if (vote.user_id === loggedInUserId) {
                    currentLoggedInUserVote = vote.vote;
                    break;
                }
            }

            for (const value of this.room.vote_options) {
                roomVoteOptions.push({
                    value,
                    selected: currentLoggedInUserVote === value,
                });
            }

            return roomVoteOptions;
        }

        public getUserVoteInformation(userId: string): string {
            if (!this.room.voting_open) {
                return 'Voting Not Open';
            }

            if (!this.getParticipantIsVoting(userId)) {
                return 'NA';
            }

            const vote = this.getVoteForParticipant(userId);

            if (vote === 0) {
                return 'Not Voted';
            }

            if (!this.everyoneHasVoted) {
                return 'Voted';
            }

            return vote.toString();
        }

        public getParticipantIsVoting(userId: string): boolean {
            for (const participant of this.room.participants) {
                if (participant.user_id === userId) {
                    return participant.is_voting;
                }
            }

            return false;
        }

        public getVoteForParticipant(userId: string): number {
            for (const vote of this.room.votes) {
                if (vote.user_id === userId) {
                    return vote.vote;
                }
            }

            return 0;
        }

        public loggedInUserOwnsRoom(roomId: string): boolean {
            const loggedInUserId = this.$storeProvider.user.loggedInUserId;
            const room = this.$storeProvider.room.getRoomById(roomId);

            if (!loggedInUserId || !room) {
                return false;
            }

            return room.created_by_user_id === loggedInUserId;
        }

        public async removeUserFromRoom(roomId: string, userId: string) {
            this.roomBeingChangedId = roomId;
            await this.$storeProvider.room.deleteUserFromRoom(roomId, userId);
            this.roomBeingChangedId = '';
        }

        public async openVoting() {
            this.errorMessage = '';
            this.roomVotingStatusChanging = true;
            const success = await this.$storeProvider.room.openVoting(this.room.id);
            this.roomVotingStatusChanging = false;

            if (!success) {
                this.errorMessage = this.$storeProvider.room.errorMessage;
            }
        }

        public async closeVoting() {
            this.errorMessage = '';
            this.roomVotingStatusChanging = true;
            let success = await this.$storeProvider.room.closeVoting(this.room.id);
            if (success) {
                success = await this.$storeProvider.room.resetVotes(this.room.id);
            }

            this.roomVotingStatusChanging = false;

            if (!success) {
                this.errorMessage = this.$storeProvider.room.errorMessage;
            }
        }

        public async resetVotes() {
            this.errorMessage = '';
            this.votesResetting = true;
            const success = await this.$storeProvider.room.resetVotes(this.room.id);
            this.votesResetting = false;

            if (!success) {
                this.errorMessage = this.$storeProvider.room.errorMessage;
            }
        }

        public async castVote(userVote: RoomVoteOptionItem): Promise<void> {
            this.errorMessage = '';
            this.userVoteIsPosting = true;
            const success = await this.$storeProvider.room.vote(this.room.id, userVote.value);
            this.userVoteIsPosting = false;

            if (!success) {
                this.errorMessage = this.$storeProvider.room.errorMessage;
            }
        }

        public async changeRoomOwnership(): Promise<void> {
            this.errorMessage = '';
            const success = await this.$storeProvider.room.changeRoomOwnership(this.room.id);

            if (!success) {
                this.errorMessage = this.$storeProvider.room.errorMessage;
            }
        }

        public handleGotoRoomSettings(): void {
            navigateTo(this.$router).roomSettings(this.room.id);
        }

        public async deleteCurrentRoom(): Promise<void> {
            this.errorMessage = '';
            const success = await this.$storeProvider.room.deleteCurrentRoom();

            if (!success) {
                this.errorMessage = this.$storeProvider.room.errorMessage;
            }

            navigateTo(this.$router).roomList();
        }

        public getRoomMenuOptions(): EllipsisMenuItemInterface[] {
            const menuOptions = [];

            const userIsRoomOwner = this.room.created_by_user_id === this.$storeProvider.user.loggedInUserId;

            if (userIsRoomOwner) {
                menuOptions.push({id: 'RoomSettings', text: 'Edit Room Settings'});

                if (!this.room.voting_open) {
                    menuOptions.push({id: 'DeleteRoom', text: 'Delete Room'});
                } else {
                    menuOptions.push({id: 'CloseVoting', text: 'Close Voting'});
                }
            } else {
                menuOptions.push({id: 'ChangeRoomOwnership', text: 'Take Control of Room'});
            }

            return menuOptions;
        }

        public handleMenuOptionClick(clickedItem: EllipsisMenuItemInterface): void {
            switch (clickedItem.id) {
                case 'RoomSettings':
                    this.handleGotoRoomSettings();
                    break;

                case 'ChangeRoomOwnership':
                    this.changeRoomOwnership();
                    break;

                case 'CloseVoting':
                    this.closeVoting();
                    break;

                case 'DeleteRoom':
                    this.deleteCurrentRoom();
                    break;
            }
        }

        private async loadRoom(roomId: string): Promise<void> {
            const room = await this.$storeProvider.room.getRoomById(roomId);
            if (!room) {
                navigateTo(this.$router).roomList();
                return;
            }

            await this.$storeProvider.room.setCurrentRoom(room);

            this.loaded = true;
        }
    }
</script>

<style lang="scss" scoped>
    @import "src/scss/globals";

    .roomDetail {
        position: relative;

        h1 {
            max-width: 250px;
        }
    }

    .ellipsisWrapper {
        position: absolute;
        top: 0;
        left: 0;
        margin-left: 75px;
    }

    .actions {
        margin-top: -20px;
    }

    .participants {
        position: relative;
        margin-top: $margin-large;
        margin-bottom: 50px;

        h2 {
            margin-bottom: $margin;
        }
    }

    .myVote {
        margin-top: $margin-large;

        h2 {
            margin-bottom: $margin;
        }

        .heading {
            position: relative;
            width: 200px;

            .spinner {
                position: absolute;
                right: 0;
                top: 0;
            }
        }
    }

    td.vote {
        width: 120px;
    }

    .winningVote {
        width: 220px;
        position: absolute;
        right: 0;
        bottom: -50px;
        height: 35px;

        -webkit-box-shadow: 0px 3px 5px 0px rgba(204, 204, 204, 1);
        -moz-box-shadow: 0px 3px 5px 0px rgba(204,204,204,1);
        box-shadow: 0px 3px 5px 0px rgba(204,204,204,1);

        dt {
            text-align: left;
            background-color: $green;
            color: $white;
            padding: 10px;
            box-sizing: border-box;
            width: 170px;
            font-size: $heading-tertiary-size;
            height: 35px;
            border-top-left-radius: $border-radius;
            border-bottom-left-radius: $border-radius;
        }

        dd {
            border-top-right-radius: $border-radius;
            border-bottom-right-radius: $border-radius;
            background-color: $light-grey;
            padding: 10px;
            box-sizing: border-box;
            width: 50px;
            text-align: center;
            font-size: $heading-tertiary-size;
            height: 35px;
        }
    }

    .error-message {
        margin: $margin 0;
    }
</style>
