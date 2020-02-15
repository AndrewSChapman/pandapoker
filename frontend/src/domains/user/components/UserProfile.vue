import Trilean from "@/domains/shared/enums/trilean";
<template>
    <Container headingText="Halt! Who goes there?">
        <template slot="content">
            <div class="userProfile">
                <h1>Identify Yourself</h1>

                <Form @submit="handleSaveUserIntent">
                    <slot>
                        <fieldset>
                            <div class="input-group">
                                <label for="name" v-if="!userLoggedIn">I shall be known as</label>
                                <label for="name" v-if="userLoggedIn">Update my display name to</label>
                                <input type="text" id="name" name="name"
                                       v-model="userName"
                                       @keyup="checkUsernameOk"
                                       @change="checkUsernameOk"
                                />
                                <InputChecker :status="userNameOk" />
                            </div>
                        </fieldset>

                        <fieldset>
                            <div class="input-group">
                                <label for="name">Choose a totem animal</label>
                                <AnimalList
                                        :selected-animal-type="totemAnimal"
                                        @clicked="handleTotemAnimalSelected"
                                />
                            </div>
                        </fieldset>

                        <SpinnerButton v-show="formValid"
                                button-text="Proceed >>"
                                display-type="positive"
                                :loading="creatingUser"
                                @clicked="handleSaveUserIntent"
                        />

                        <div class="userAlreadyExists" v-if="userAlreadyExists">
                            A user already exists with the name &ldquo;{{ userName }}&rdquo;.<br />
                            If this is your user, you may
                            <SpinnerButton v-show="formValid"
                                           button-text="identify as this user."
                                           display-type="text_only"
                                           :loading="assumingUser"
                                           @clicked="handleAssumeUserIntent"
                            />
                        </div>

                        <SuccessMessage :message="successMessage"
                                        :display-time-seconds="2"
                                        v-if="successMessage.length > 0"
                                        @hide="successMessage = ''"
                        />

                        <ErrorMessage v-show="errorMessage.length > 0" :message="errorMessage" />
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
    // Typescript
    import BasicButton from '@/domains/ui/buttons/BasicButton.vue';
    import Form from '@/domains/ui/containers/Form.vue';
    import SpinnerButton from '@/domains/ui/buttons/SpinnerButton.vue';
    import InputChecker from '@/domains/ui/input/InputChecker.vue';
    import Trilean from '@/domains/shared/enums/trilean';
    import { StoreProvider } from '@/domains/shared/storeProvider';
    import ErrorMessage from '@/domains/ui/messages/ErrorMessage.vue';
    import SuccessMessage from '@/domains/ui/messages/SuccessMessage.vue';
    import { navigateTo } from '@/domains/shared/helpers/NavigationHelper';
    import { AnimalType } from '@/domains/user/enums/AnimalType';
    import Animal from '@/domains/user/components/Animal.vue';
    import AnimalList from '@/domains/user/components/AnimalList.vue';

    @Component({
        components: {
            AnimalList,
            Animal,
            SuccessMessage,
            ErrorMessage,
            InputChecker,
            SpinnerButton,
            Form,
            BasicButton,
            ButtonBar,
            Container,
        },
    })
    export default class UserProfile extends Vue {
        public userName = '';
        public userNameOk: Trilean = Trilean.Unknown;
        public totemAnimal: AnimalType|null = null;
        public errorMessage: string = '';
        public successMessage: string = '';
        private userAlreadyExists: boolean = false;
        private assumingUser: boolean = false;

        private $storeProvider!: StoreProvider;

        public mounted() {
            this.userName = this.$storeProvider.user.displayName;
            this.totemAnimal = this.$storeProvider.user.totemAnimal;
        }

        public checkUsernameOk() {
            this.userNameOk = this.userName.length >= 2 ? Trilean.True : Trilean.False;
        }

        public handleTotemAnimalSelected(animalType: AnimalType): void {
            this.totemAnimal = animalType;

            if (this.userName.length > 2) {
                this.checkUsernameOk();
            }
        }

        public async handleSaveUserIntent(): Promise<void> {
            if ((!this.formValid) || (!this.totemAnimal)) {
                return;
            }

            this.errorMessage = '';

            if (this.userLoggedIn) {
                const success = await this.$storeProvider.user.updateUser(this.userName, this.totemAnimal);
                if (success) {
                    this.successMessage = 'Your profile has been updated';
                } else {
                    this.errorMessage = this.$storeProvider.user.errorMessage;
                }
            } else {
                const success = await this.$storeProvider.user.createUser(this.userName, this.totemAnimal);

                if (!success) {
                    const errorMessage = this.$storeProvider.user.errorMessage;

                    this.userAlreadyExists = errorMessage.indexOf('already exists') > 0;

                    if (!this.userAlreadyExists) {
                        this.errorMessage = errorMessage;
                    }

                    return;
                }

                if (this.$storeProvider.user.userWantsToCreateRoom) {
                    navigateTo(this.$router).roomSettings();
                } else {
                    navigateTo(this.$router).roomList();
                }
            }
        }

        public async handleAssumeUserIntent(): Promise<void> {
            if (!this.formValid) {
                return;
            }

            this.assumingUser = true;
            const success = await this.$storeProvider.user.assumeUser(this.userName);
            this.assumingUser = false;

            if (!success) {
                this.errorMessage = this.$storeProvider.user.errorMessage;
                return;
            }

            this.errorMessage = '';

            if (this.$storeProvider.user.userWantsToCreateRoom) {
                navigateTo(this.$router).roomSettings();
            } else {
                navigateTo(this.$router).roomList();
            }
        }

        public get formValid(): boolean {
            if (this.userNameOk !== Trilean.True) {
                return false;
            }

            if (this.totemAnimal === null) {
                return false;
            }

            return true;
        }

        public get creatingUser(): boolean {
            return this.$store.state.user.saving;
        }

        public get userDisplayName(): string {
            return this.$store.state.user.userDisplayName;
        }

        public get userLoggedIn(): boolean {
            return this.$store.state.user.userLoggedIn;
        }
    }
</script>

<style lang="scss" scoped>
    @import 'src/scss/globals';

    .userAlreadyExists {
        max-width: 500px;
        padding: $padding-large;
        background-color: $light-grey;
        margin-top: $margin;
    }
</style>
