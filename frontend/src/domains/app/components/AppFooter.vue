<template>
    <footer>
        <div class="footer-container">
            <p>
                <span>PandaPoker</span> &nbsp;&ndash; <a href="https://github.com/AndrewSChapman/pandapoker" target="_blank">View on GitHub</a>
            </p>
        </div>
    </footer>
</template>

<script lang="ts">
    // Components
    import { Component, Vue } from 'vue-property-decorator';

    @Component({
        components: {
        },
    })
    export default class AppFooter extends Vue {
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

        p {
            font-family: $font-stack;
            font-weight: $font-bold;
        }

        a {
            color: $light-blue;
            text-decoration: none;
        }

        @media(max-width: 900px) {
            position: static;
            margin-top: $margin-small;

            p {
                margin-top: -2px;
                padding-top: 5px;
            }
        }


        @media(min-width: 900px) {
            height: 50px;

            p {
                box-sizing: border-box;
                padding-top: 3px;
            }
        }

        .footer-container {
            max-width: 1200px;
            position: relative;
            height: 30px;

            > div {
                position: absolute;
                top: 5px;
                right: 0;
            }
        }
    }
</style>
