<template>
    <div :class="{'roomVoteOption': true, 'selected': selected}" @click="handleOptionClicked">
        <span>{{ voteOption }}</span>
    </div>
</template>

<script lang="ts">
    // Components
    import { Component, Vue, Prop, Watch } from 'vue-property-decorator';
    import { RoomVoteOptionItem } from '@/domains/room/interfaces/RoomVoteOptionItem';


    @Component({
        components: {},
    })
    export default class RoomVoteOption extends Vue {
        @Prop({ required: true, type: Number})
        public voteOption!: number;

        @Prop({ required: true, type: Boolean})
        public selected!: boolean;

        public handleOptionClicked(): void {
            this.$emit('click', {
                value: this.voteOption,
                selected: !this.selected,
            } as RoomVoteOptionItem);
        }
    }
</script>

<style lang="scss" scoped>
    @import 'src/scss/globals';

    .roomVoteOption {
        width: 50px;
        height: 50px;
        margin-right: $margin-small;
        margin-bottom: $margin-small;

        display: flex;
            align-content: center;
            justify-content: center;
            align-items: center;

        border: $input-border-wide;
        border-radius: $border-radius;

        color: $dark-blue;
        font-weight: bold;

        &:hover, &.selected {
            cursor: pointer;
            border-color: $violet;
            background-color: $light-blue;
            color: white;
        }
    }
</style>
