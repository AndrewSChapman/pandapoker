<template>
    <div class="waitingForConnection">
        <h1>Waiting for connection...</h1>

        <div>
            <img src="@/assets/app-icon.png" />
        </div>

        <spinner />
    </div>
</template>

<script lang="ts">
    // Components
    import { Component, Vue, Prop, Watch } from 'vue-property-decorator';
    import Spinner from '@/domains/ui/Spinner.vue';

    @Component({
        components: {Spinner},
    })
    export default class WaitingForConnection extends Vue {
    }
</script>

<style lang="scss" scoped>
    @import 'src/scss/globals';

    .waitingForConnection {
        margin-top: 50px;

        > div {
            margin: $margin;
        }
    }
</style>
