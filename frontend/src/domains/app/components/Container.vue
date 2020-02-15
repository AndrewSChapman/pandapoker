<template>
    <div class="containerWrapper">
        <header v-if="headingText">
            <h1>{{ headingText }}</h1>
        </header>
        <div class="container">
            <article>
                <slot name="content"></slot>
            </article>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Vue, Prop} from 'vue-property-decorator';

    @Component({
        components: {},
    })
    export default class Container extends Vue {
        @Prop({required: false, type: String})
        private headingText!: string;
    }
</script>

<style lang="scss" scoped>
    @import '../../../scss/globals';

    header {
        margin-top: 10px;
        padding: $padding-large;
        background-color: $light-grey;
    }

    .container {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        justify-content: flex-start;
        align-content: flex-start;
        align-items: stretch;
        margin-top: $margin-large;
    }

    article {
        box-sizing: border-box;
        width: calc(100% - #{$sidebar-width} - 40px);
        margin-left: 20px;
        min-height: calc(100vh - #{$header-height} - #{$footer-height} - 110px);
        text-align: left;
        max-width: 1200px;

        &.no-sidebar {
            margin: 0 auto;
        }

        @media (max-width: 900px) {
            width: calc(100% - 40px);
        }

        h1 {
            margin-bottom: $margin;
        }
    }
</style>
