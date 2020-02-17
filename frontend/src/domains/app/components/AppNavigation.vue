<template>
    <nav>
        <div class="navigation-wrapper" v-bind:class="[{ 'menu-open' : this.menuOpen}]">
            <div :class="{'logo-wrapper': true, 'socket-connected': isSocketConnected}" v-on:click="menuOpen = !menuOpen">
                <img src="@/assets/app-icon.png" />
            </div>

            <h1>PandaPoker</h1>

            <ul>
                <li v-on:click="menuOpen = false" :class="{'active': isRoomsModuleActive}" v-if="isLoggedIn">
                    <router-link to="/rooms">Rooms</router-link>
                </li>
                <li v-on:click="menuOpen = false" :class="{'active': isRoomDetailsModuleActive}" v-if="isLoggedIn">
                    <router-link to="/room_settings">Create New Room</router-link>
                </li>
                <li v-on:click="menuOpen = false" v-if="isLoggedIn" :class="{'active': isProfileModuleActive}" >
                    <router-link to="/user_profile">My Profile</router-link>
                </li>
                <li v-on:click="menuOpen = false" v-if="!isLoggedIn" :class="{'active': isProfileModuleActive}">
                    <router-link to="/user_profile">My Identity</router-link>
                </li>
                <li v-on:click="menuOpen = false" v-if="isLoggedIn">
                    <a @click="logoutUser">Logout</a>
                </li>
            </ul>

            <ul v-if="isLoggedIn && !syncFinished">
            </ul>
        </div>
    </nav>
</template>

<script lang="ts">
    import {Component, Vue, Prop, Watch} from 'vue-property-decorator';
    import { ModuleType} from '@/domains/shared/enums/moduleType';
    import { StoreProvider } from '@/domains/shared/storeProvider';
    import { navigateTo } from '@/domains/shared/helpers/NavigationHelper';

    @Component({
        components: {},
    })
    export default class AppNavigation extends Vue {
        public menuOpen: boolean = false;
        private $storeProvider!: StoreProvider;

        get selectedModule(): ModuleType {
            return this.$store.state.settings.moduleType;
        }

        get appName(): string {
            return 'PandaPoker';
        }

        get isLoggedIn(): boolean {
            return this.$storeProvider.user.isLoggedIn;
        }

        get syncFinished(): boolean {
            return this.$store.state.user.syncFinished;
        }

        get isSocketConnected(): boolean {
            return this.$store.state.user.socketConnected;
        }

        public dispatchStoreAction(actionName: string) {
            this.$store.dispatch(actionName);
        }

        public get isRoomsModuleActive(): boolean {
            return this.isSelectedModule(ModuleType.ROOMS);
        }

        public get isRoomDetailsModuleActive(): boolean {
            return this.isSelectedModule(ModuleType.ROOM_SETTINGS);
        }

        public get isProfileModuleActive(): boolean {
            return this.isSelectedModule(ModuleType.PROFILE);
        }

        public async logoutUser(): Promise<void> {
            await this.$storeProvider.room.resetCurrentRoom(this.$storeProvider.user.loggedInUserId);
            await this.$storeProvider.user.logout();
            navigateTo(this.$router).userProfile();
        }

        private isSelectedModule(moduleName: string): boolean {
            return moduleName === this.selectedModule;
        }
    }
</script>

<style lang="scss" scoped>
    @import 'src/scss/globals';

    nav {
        position: fixed;
        z-index: 3;
        top: 0;
        width: 100%;
    }

    .navigation-wrapper {
        position: relative;
        height: $header-height;
        box-sizing: border-box;
        background-color: $light-blue;
        margin: 0;
        text-align: left;

        -webkit-box-shadow: 0px 3px 5px 0px rgba(204, 204, 204, 1);
        -moz-box-shadow: 0px 3px 5px 0px rgba(204,204,204,1);
        box-shadow: 0px 3px 5px 0px rgba(204,204,204,1);

        .logo-wrapper {
            display: inline-block;
            vertical-align: middle;
            margin: 10px;
            opacity: 0.3;
            img {
                height: 60px;
            }

            border-radius: 6px;
            padding: 5px 5px 0 5px;
            margin-top: 5px;

            &.socket-connected {
                opacity: 1.0;
                background-color: $green;
            }
        }

        h1 {
            color: $offwhite;
            display: inline-block;
            margin-left: 10px;
            font-weight: bold;
            vertical-align: middle;
        }

        ul {
            display: inline-block;
            margin: 0;
            padding: 0;
            list-style-type: none;

            &.not-authenticated {
                display: none;
            }
        }


        li {
            list-style-type: none;
            padding: 0;
            margin: 0 0 0 20px;
            display: inline-block;

            a {
                display: inline-block;
                font-weight: bold;
                color: $offwhite;
                background-color: $light-blue;
                text-decoration: none;
                padding: 10px;
                border-radius: $border-radius;

                &.router-link-exact-active, .router-link-active {
                    background-color: $violet;
                }

                &.router-link-active {
                    background-color: $violet;
                }

                &:hover {
                    cursor: pointer;
                    background-color: $violet;
                    transition: background-color 200ms linear;
                }
            }

            &.mobile-only {
                display: none;
            }
        }

        li.active {
            a {
                background-color: $violet;

                &:hover {
                    background-color: $violet;
                    transition: background-color 200ms linear;
                }
            }
        }

        .spaceSelectorWrapper {
            position: absolute;
            right: 10px;
            top: 10px;
            width: 400px;
            text-align: right;

            label {
                display: inline-block;
                width: 140px;
                color: $white;
            }

            select {
                display: inline-block;
                width: 250px;
            }
        }
    }

    @media (max-width: 900px) {
        .navigation-wrapper {
            position: relative;

            .logo-wrapper {
                margin-top: 10px;
                margin-left: 10px;

                img {
                    height: 40px;
                    border: 1px dashed white;
                    padding: 5px;
                    border-radius: $border-radius;
                    cursor: pointer;
                }
            }

            ul {
                display: none;
            }

            &.menu-open {
                ul {
                    display: block;
                    position: absolute;
                    top: 80px;
                    left: 0;
                    width: 100%;
                    z-index: 2;

                    li {
                        display: block;
                        width: 100%;
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                        border-top: 1px solid white;

                        a {
                            display: block;
                            width: calc(100% - 40px);
                            border-radius: 0;
                            background-color: $dark-blue;
                            color: $offwhite;
                            padding: 20px;
                        }

                        &.mobile-only {
                            display: block;
                        }
                    }

                    li.active {
                        a {
                            background-color: $violet;
                        }
                    }
                }
            }

            .spaceSelectorWrapper {
                width: 200px;

                select {
                    width: 100%;
                    height: 45px;
                    font-size: 14px;
                    margin-top: $margin-small;
                }
            }
        }
    }
</style>
