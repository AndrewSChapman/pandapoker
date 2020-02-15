<template>
    <div class="spinner">
        <font-awesome-icon :icon="['fas', 'spinner']" spin/>
    </div>
</template>

<script lang="ts">
    import {Component, Vue, Prop} from 'vue-property-decorator';

    @Component({
        components: {},
    })
    export default class Spinner extends Vue {}
</script>

<style lang="scss" scoped>
    .spinner {
        display: inline-block;
    }
</style>