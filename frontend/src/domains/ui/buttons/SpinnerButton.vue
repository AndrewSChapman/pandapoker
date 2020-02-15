<template>
    <div class="spinnerButton">
        <button
            type="button"
            v-bind:class="['right', displayTypeClass, disabledClass, className]"
            v-on:click="buttonClicked"
        >
            <span class="spinner" v-if="loading" ><Spinner /></span>
            <span>{{ buttonText }}</span>
        </button>
    </div>
</template>

<script lang="ts">
    import {Component, Vue, Prop} from 'vue-property-decorator';
    import Spinner from '@/domains/ui/Spinner.vue';
    import { DisplayType, DisplayTypeHelper } from '@/domains/shared/enums/displayType';

    @Component({
        components: {
            Spinner,
        },
    })
    export default class SpinnerButton extends Vue {
        @Prop({required: true}) public buttonText!: string;
        @Prop({required: true}) public loading!: boolean;
        @Prop({required: false}) public displayType!: DisplayType;
        @Prop({required: false, type: String, default: ''}) public className!: string;

        private buttonClicked(): void {
            // If we are in a loading state, don't process the button
            if (this.loading) {
                return;
            }

            this.$emit('clicked', {});
        }

        get displayTypeClass(): string {
            const helper = new DisplayTypeHelper(this.displayType);
            return helper.className;
        }

        get disabledClass(): string {
            return this.loading ? 'disabled' : '';
        }
    }
</script>

<style lang="scss" scoped>
@import 'src/scss/globals';
@import 'src/scss/mixins.scss';

.spinnerButton {
    display: inline-block;
}

button {
    @include button;
    @include action-states-buttons;

    .spinner {
        padding-right: 5px;
        display: inline-block;
    }
}
</style>
