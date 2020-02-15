<template>
    <a :class="['iconButton', presentationClassName()]"
        v-on:click="broadcastIconClicked();"
        :alt="tooltipText"
        :title="tooltipText"
    >
        <font-awesome-icon :icon="iconName" :size="iconSize" />
    </a>
</template>

<style lang="scss" scoped>
@import '@/scss/globals.scss';
@import '@/scss/mixins.scss';

.iconButton {
    &:hover {
        cursor: pointer;
    }
    
    @include action-states;
}
</style>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator';

@Component({
  components: {},
})
export default class IconButton extends Vue {
    @Prop({required: true})
    private iconName!: string;

    @Prop({required: false, type: Boolean})
    private selected!: boolean;

    @Prop({required: false})
    private defaultClass!: string;

    @Prop({required: false})
    private selectedClass!: string;

    @Prop({required: false})
    private tooltipMessage!: string;

    @Prop({required: false, type: Number})
    private size!: number;

    private tooltipText = '';

    public created(): void {
        if (!!this.tooltipMessage) {
            this.tooltipText = this.tooltipMessage;
        }
    }

    public presentationClassName(): string {
        if (!this.selected) {
            return this.defaultClass ? this.defaultClass : '';
        }

        if (!!this.selectedClass) {
            return this.selectedClass;
        }

        return 'info';
    }

    public get iconSize(): string {
        if (!this.size) {
            return '1x';
        }

        return this.size + 'x';
    }

    public broadcastIconClicked(): void {
        this.$emit('icon-clicked');
    }


}
</script>