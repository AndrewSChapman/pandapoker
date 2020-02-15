<template>
    <div :class="'input-icon ' + (status === 'True' ? 'icon-success' : 'icon-failure')" v-show="status != 'Unknown'">
        <font-awesome-icon icon="check" v-show="status === 'True'" />
        <font-awesome-icon icon="times" v-show="status === 'False'" />
    </div>
</template>

<script lang="ts">
    import {Component, Vue, Prop} from 'vue-property-decorator';
    import { Trilean} from '@/domains/shared/enums/trilean';

    @Component({
        components: {},
    })
    export default class InputChecker extends Vue {
        @Prop({required: true})
        private status!: Trilean;
    }
</script>

<style lang="scss" scoped>
    @import '../../../scss/globals';

    .input-icon {
        box-sizing: border-box;
        position: absolute;
        bottom: -4px;
        right: 0;
        width: 40px;
        height: 46px;
        font-size: 1.6em;
        text-align: center;
        padding: 7px 0;

        &.icon-success {
            color: $positive;
        }

        &.icon-failure {
            color: $negative;
        }
    }
</style>
