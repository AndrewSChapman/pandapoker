<template>
    <div class="ellipsisMenu">
        <div :class="{ 'opener' : true, 'open': open }" @click="handleToggleOpen">
            <font-awesome-icon :icon="['fas', 'ellipsis-v']" />
        </div>
        <ul class="options" v-if="open">
            <li v-for="menuOption in menuOptions"
                @mouseenter="handleItemHover"
                @click="handleItemClick(menuOption)"
                :key="menuOption.id"
            >{{ menuOption.text }}</li>
        </ul>
    </div>
</template>

<script lang="ts">
    // Components
    import { Component, Vue, Prop, Watch } from 'vue-property-decorator';

    // Typescript
    import { EllipsisMenuItemInterface } from '@/domains/ui/selectors/EllipsisMenu/EllipsisMenuItemInterface';

    @Component({
        components: {},
    })
    export default class EllipsisMenu extends Vue {
        @Prop({required: true})
        public menuOptions!: EllipsisMenuItemInterface[];

        public open: boolean = false;
        private fadeInterval: any = null;

        public handleToggleOpen() {
            this.open = !this.open;

            if (this.open) {
                this.handleAutoFadeOut();
            }
        }

        public handleItemHover() {
            this.handleAutoFadeOut();
        }

        public handleAutoFadeOut(): void {
            clearInterval(this.fadeInterval);

            this.fadeInterval = setInterval(() => {
                this.open = false;
                clearInterval(this.fadeInterval);
            }, 2000);
        }

        public handleItemClick(item: EllipsisMenuItemInterface) {
            this.$emit('clicked', item);
            clearInterval(this.fadeInterval);
            this.open = false;
        }
    }
</script>

<style lang="scss" scoped>
    @import 'src/scss/globals';

    .ellipsisMenu {
        position: relative;
        width: 200px;
        z-index: 2;
    }

    .opener {
        position: absolute;
            right: 0;
            top: 0;

        width: 30px;
        height: 30px;
        box-sizing: border-box;

        background-color: white;
        color: $grey;
        padding: $padding-small;
        border-radius: $border-radius;

        text-align: center;

        &:hover {
            background-color: $positive;
            color: $white;
            cursor: pointer;
        }

        &.open {
            border: 1px solid $positive;
            border-bottom: none;
            cursor: pointer;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }
    }

    .options {
        padding: 30px 0 0 0;
        margin: 0;
        width: 100%;

        li {
            list-style-type: none;
            width: 100%;
            box-sizing: border-box;
            padding: $padding $padding-small;
            margin: 0;

            background-color: white;
            border-top: none;
            border-bottom: 1px solid $positive;
            border-right: 1px solid $positive;
            border-left: 1px solid $positive;


            &:first-of-type {
                border-top: 1px solid $positive;
                border-top-left-radius: $border-radius;
            }

            &:last-of-type {
                border-bottom-left-radius: $border-radius;
                border-bottom-right-radius: $border-radius;
            }

            &:hover {
                cursor: pointer;
                background-color: $positive;
                color: white;
            }
        }
    }
</style>
