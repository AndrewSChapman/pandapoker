<template>
    <div class="roomVoteOptionList">
        <template v-for="option in availableOptions">
            <RoomVoteOption :key="option.value"
                    :vote-option="option.value"
                    :selected="option.selected"
                    @click="handleOptionSelected"
            />
        </template>
    </div>
</template>

<script lang="ts">
    // Components
    import { Component, Vue, Prop, Watch } from 'vue-property-decorator';

    // Typescript
    import HelperFactory from '@/domains/shared/helpers/helperFactory';
    import RoomVoteOption from '@/domains/room/components/RoomVoteOption.vue';
    import { RoomVoteOptionItem } from '@/domains/room/interfaces/RoomVoteOptionItem';

    @Component({
        components: {RoomVoteOption},
    })
    export default class RoomVoteOptionList extends Vue {
        @Prop({ required: true, type: Array})
        public availableOptions!: RoomVoteOptionItem[];

        private helperFactory!: HelperFactory;

        public handleOptionSelected(roomVoteOption: RoomVoteOptionItem): void {
            this.$emit('click', roomVoteOption);
        }
    }
</script>

<style lang="scss" scoped>
    @import "src/scss/globals";

    .roomVoteOptionList {
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: flex-start;
        width: 100%;
        flex-wrap: wrap;
    }
</style>
