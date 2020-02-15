<template>
    <div v-if="breakpointValid">
        <slot></slot>
    </div>
</template>

<script lang="ts">
    import {Component, Vue, Prop} from 'vue-property-decorator';
    import HelperFactory from '@/domains/shared/helpers/helperFactory';
    import {  BreakPoint, BreakPointOperator } from '@/domains/shared/enums/breakPoint';

    @Component({
        components: {},
    })
    export default class IncludeIf extends Vue {
        @Prop({required: true, type: String}) private breakpoint!: BreakPoint;
        @Prop({required: true, type: String}) private operator!: BreakPointOperator;

        private $helperFactory!: HelperFactory;

        get breakpointValid(): boolean {
            switch (this.operator) {
                case BreakPointOperator.LESS_THAN_OR_EQUAL_TO:
                    return this.$helperFactory.breakPoint.lessThanOrEqualTo(this.breakpoint);

                case BreakPointOperator.GREATER_THAN_OR_EQUAL_TO:
                    return this.$helperFactory.breakPoint.greaterThanOrEqualTo(this.breakpoint);

                case BreakPointOperator.GREATER_THAN:
                    return this.$helperFactory.breakPoint.greaterThan(this.breakpoint);

                default:
                    throw new Error('IncludeIf::show - Invalid operator: ' + this.operator);
            }
        }
    }
</script>
