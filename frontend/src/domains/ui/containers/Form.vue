<template>
    <form @submit="broadcastSubmit">
        <slot></slot>
    </form>
</template>

<script lang="ts">
    // Components
    import { Component, Vue, Prop, Watch } from 'vue-property-decorator';

    // Typescript
    import HelperFactory from '@/domains/shared/helpers/helperFactory';

    @Component({
        components: {},
    })
    export default class Form extends Vue {
        private helperFactory!: HelperFactory;

        public broadcastSubmit(evt: Event): void {
            evt.preventDefault();
            this.$emit('submit');
        }
    }
</script>

<style lang="scss" scoped>
    @import '@/scss/globals.scss';

    .input-group {
        position: relative;
        margin: 0 0 $margin 0;
        width: 100%;
        text-align: left;
    }

    form {
        font-size: 1em;

        fieldset {
            width: 300px;
        }

        label {
            display: block;
            font-weight: bold;
            width: 100%;
        }

        input[type="text"], input[type="number"], input[type="password"] {
            box-sizing: border-box;
            width: 100%;
            margin-top: 3px;
            border: $input-border;
            padding: $padding 40px $padding $padding;
            border-radius: $border-radius;
            font-size: 1em;
        }
    }
</style>
