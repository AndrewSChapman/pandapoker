<template>
    <div class="basicButton">
        <button 
            type="button"
            v-bind:class="[displayTypeClass, disabledClass]"
            v-on:click="buttonClicked"
        >
            <span>{{ buttonText }}</span>
        </button>
    </div>
</template>

<script lang="ts">
    import {Component, Vue, Prop} from 'vue-property-decorator';
    import { DisplayType, DisplayTypeHelper } from '@/domains/shared/enums/displayType';

    @Component({
        components: {
        },
    })
    export default class BasicButton extends Vue {
        @Prop({required: true})
        private buttonText!: string;

        @Prop({required: false})
        private displayType!: DisplayType;

        @Prop({required: false, type: Boolean, default: false})
        private disabled!: boolean;

        private buttonClicked(): void {
            if (this.disabled) {
                return;
            }

            this.$emit('clicked', {});
        }

        get displayTypeClass(): string {
            const helper = new DisplayTypeHelper(this.displayType);
            return helper.className;
        }

        get disabledClass(): string {
            return this.disabled ? 'disabled' : '';
        }
    }
</script>

<style lang="scss" scoped>
@import 'src/scss/globals.scss';
@import 'src/scss/mixins.scss';

.basicButton {
    display: inline-block;
}

button {
    @include button;
    @include action-states-buttons;

    &.selector {
        background-color: $white;
        border-radius: 0;
        border: 1px dashed $light-grey;
    }
}
</style>
