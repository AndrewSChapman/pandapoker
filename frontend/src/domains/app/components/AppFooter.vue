<template>
    <footer>
        <div class="footer-container">
            <p class="version">
                PandaPoker&nbsp; &ndash; &copy;ChapmanDigital 2019
            </p>
            <IncludeIf breakpoint="medium" operator=">=">
                <ul class="footer-actions" v-if="isLoggedIn">
                    <li :class="{'active': isProfileModuleActive}">
                        <router-link :to="{ name: 'profile'}">Profile</router-link>
                    </li>
                    <li>
                        <router-link to="/logout">Logout</router-link>
                    </li>
                </ul>
            </IncludeIf>
        </div>
    </footer>
</template>

<script lang="ts">
    // Components
    import {Component, Vue, Prop, Watch} from 'vue-property-decorator';
    import IncludeIf from '@/domains/shared/includeIf.vue';

    // Typescript
    import { ModuleType } from '@/domains/shared/enums/moduleType';

    @Component({
        components: {
            IncludeIf,
        },
    })
    export default class AppFooter extends Vue {
        get isAdmin(): boolean {
            return this.$store.state.user.isAdmin;
        }

        private isSelectedModule(moduleName: string): boolean {
            return moduleName === this.selectedModule;
        }

        public get isProfileModuleActive(): boolean {
            return this.isSelectedModule(ModuleType.PROFILE);
        }

        get isLoggedIn(): boolean {
            return this.$store.state.user.userLoggedIn;
        }

        get selectedModule(): ModuleType {
            return this.$store.state.settings.moduleType;
        }

        get userDisplayName(): string {
            return this.$store.state.user.userDisplayName;
        }
    }
</script>

<style lang="scss" scoped>
    @import 'src/scss/globals.scss';

    footer {
        width: 100%;
        background-color: $dark-blue;
        text-align: left;
        position: fixed;
        bottom: 0;
        padding: 10px;
        box-sizing: border-box;
        color: $offwhite;

        @media(max-width: 900px) {
            position: static;
            margin-top: $margin-small;

            span {
                display: block;
            }

            p {
                margin-top: -2px;
            }
        }

        .footer-container {
            height: 30px;
        }

        @media(min-width: 900px) {
            height: 50px;

            span {
                padding-left: 50px;
            }

            p {
                box-sizing: border-box;
                padding-top: 3px;
            }
        }

        .footer-container {
            max-width: 1200px;
            position: relative;

            > div {
                position: absolute;
                top: 5px;
                right: 0;
            }
        }
    }

    .footer-actions {
        list-style-type: none;
        margin: 0;
        padding: 2px 0 0 0;

        li {
            display: inline-block;
            margin: 0 0 0 10px;
            padding: 0;

            &::after {
                content: '|';
                padding-left: 10px;
            }

            &:last-of-type {
                &::after {
                    content: '';
                }
            }

            &.active a, a:hover {
                color: $positive;
            }
        }

        a {
            color: $offwhite;
            padding: 5px;
            transition: color 200ms linear;
            text-decoration: none;
        }
    }

    @media (max-width: 900px) {
        p {
            padding-top: 5px;
        }
    }
</style>
