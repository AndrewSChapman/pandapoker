<template>
    <div class="error-message">{{ message }}</div>
</template>

<script lang="ts">
    import {Component, Vue, Prop} from 'vue-property-decorator';

    @Component({
        components: {},
    })
    export default class ErrorMessage extends Vue {
        @Prop({required: true}) private message!: string;
    }
</script>

<style lang="scss" scoped>
    @import '../../../scss/globals';

    .error-message {
        display: block;
        width: 100%;
        box-sizing: border-box;
        border: $input-border $negative;
        border-radius: $border-radius;
        font-weight: $font-bold;
        color: $white;
        background-color: $negative;
        padding: $component-padding;
        margin-top: $margin-large;
        text-align: center;
    }
</style>
