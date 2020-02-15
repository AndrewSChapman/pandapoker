<template>
    <div class="buttonBar">
        <slot></slot>
    </div>
</template>

<script lang="ts">
    // Components
    import {Component, Vue, Prop, Watch} from 'vue-property-decorator';

    // Typescript
    import HelperFactory from '@/domains/shared/helpers/helperFactory';

    @Component({
        components: {
        },
    })
    export default class ButtonBar extends Vue {
        private $helperFactory!: HelperFactory;
    }
</script>

<style lang="scss" scoped>
    @import '../../../scss/globals.scss';

    .buttonBar {
        padding: $padding-large 0;

        .basicButton, .spinnerButton {
            margin-right: $margin;
        }
    }
</style>
