<template>
    <table>
        <slot></slot>
    </table>
</template>

<script lang="ts">
    // Components
    import { Component, Vue, Prop, Watch } from 'vue-property-decorator';

    // Typescript
    import HelperFactory from '@/domains/shared/helpers/helperFactory';

    @Component({
        components: {},
    })
    export default class Table extends Vue {
        private helperFactory!: HelperFactory;
    }
</script>

<style lang="scss" scoped>
    @import 'src/scss/globals';

    table {
        width: 100%;
        margin-bottom: $margin;
        -webkit-box-shadow: 0px 3px 5px 0px rgba(204, 204, 204, 1);
        -moz-box-shadow: 0px 3px 5px 0px rgba(204,204,204,1);
        box-shadow: 0px 3px 5px 0px rgba(204,204,204,1);

        thead tr {
            background-color: $light-blue;

            th {
                padding: $table-cell-padding;
                color: $white;
                border-bottom: $input-border;
                text-align: center;

                &:first-child {
                    text-align: left;
                }
            }
        }

        tbody tr {
            &:hover {
                background-color: $offwhite;
                transition: background-color 200ms linear;
            }

            td {
                padding: $table-cell-padding;
                border-bottom: $input-border;
                text-align: center;

                &:first-child {
                    text-align: left;
                }
            }
        }
    }
</style>
