<template>
    <div class="success-message">{{ message }}</div>
</template>

<script lang="ts">
    import {Component, Vue, Prop, Watch} from 'vue-property-decorator';

    @Component({
        components: {},
    })
    export default class SuccessMessage extends Vue {
        @Prop({required: true}) private message!: string;
        @Prop({required: false, default: 0, type: Number}) private displayTimeSeconds!: number;

        private waitingToHide = false;

        public mounted() {
            this.handleDisplayTime();
        }

        @Watch('message')
        private handleDisplayTime(): void {
            if ((this.displayTimeSeconds <= 0) || (this.message === '') ||
                (this.waitingToHide)) {
                return;
            }

            this.waitingToHide = true;

            setTimeout(() => {
                this.$emit('hide');
                this.waitingToHide = false;
            }, this.displayTimeSeconds * 1000);
        }
    }
</script>

<style lang="scss" scoped>
    @import '../../../scss/globals';

    .success-message {
        display: block;
        width: 100%;
        box-sizing: border-box;
        border: $input-border;
        border-radius: $border-radius;
        border-color: $positive;
        font-weight: $font-bold;
        color: $white;
        background-color: $positive;
        padding: $component-padding;
        margin-top: $margin-large;
        text-align: center;
    }
</style>