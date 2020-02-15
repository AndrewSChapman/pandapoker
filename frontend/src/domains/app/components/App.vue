<template>
  <div id="app" class="app">
    <AppNavigation />
    <transition name="content" mode="out-in">
      <router-view v-if="ready" />
      <WaitingForConnection v-else />
    </transition>
    <AppFooter />
  </div>
</template>

<script lang="ts">
  // Components
  import { Component, Vue, Watch } from 'vue-property-decorator';
  import AppFooter from '@/domains/app/components/AppFooter.vue';

  // Typescript
  import HelperFactory from '@/domains/shared/helpers/helperFactory';
  import AppNavigation from '@/domains/app/components/AppNavigation.vue';
  import { StoreProvider } from '@/domains/shared/storeProvider';
  import Spinner from '@/domains/ui/Spinner.vue';
  import WaitingForConnection from '@/domains/app/components/WaitingForConnection.vue';
  import { navigateTo } from '@/domains/shared/helpers/NavigationHelper';

  @Component({
    components: {
      WaitingForConnection,
      Spinner,
      AppFooter,
      AppNavigation,
    },
  })
  export default class App extends Vue {
    private $helperFactory!: HelperFactory;
    private $storeProvider!: StoreProvider;
    private loaded: boolean = false;
    private uxTimeDelayLapsed = false;

    public created(): void {
      this.$helperFactory.breakPoint.init(this.$store);
      this.loaded = false;
    }

    public async mounted(): Promise<void> {
      await this.$storeProvider.user.clearUserActionIntent();
      if (await this.$storeProvider.user.loadUsers()) {
        this.loaded = true;
      }
    }

    get ready(): boolean {
      return this.loaded && this.$storeProvider.user.hasSocketConnection;
    }
  }
</script>

<style lang="scss">
  @import 'src/scss/globals';
  @import 'src/scss/reset';

  body {
    background-color: $app-background-color;
  }

  .content-enter-active, .content-leave-active {
    transition: opacity 0.5s ease;
  }

  .content-enter, .content-leave {
    opacity: 0;
  }

  .app {
    font-family: $font-stack;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    font-family: 'Hind', sans-serif;
    color: $dark-blue;
    padding: 70px 0 0 0;

    @media(min-width: 900px) {
      padding-bottom: 50px;
    }
  }

  h1 {
    font-size: 1.8em;
    font-family: 'Acme', sans-serif;
  }

  h2 {
    font-size: 1.6em;
    font-family: 'Acme', sans-serif;
  }

  p {
    line-height: 1.4em;
    font-family: 'Hind', sans-serif;
  }

  .block {
    display: block;
  }

  .padding {
    padding: $padding;
  }

  .padding-top-bottom {
    padding: $padding 0;
  }

  dl {
    max-width: 750px;

    dt {
      display: inline-block;
      width: 25%;
      text-align: right;
      box-sizing: border-box;
      padding: 5px;
      font-weight: $font-bold;
      vertical-align: top;
    }

    dd {
      display: inline-block;
      width: 75%;
      padding: 5px;
      box-sizing: border-box;
      vertical-align: middle;
    }

    &.block {
      dt, dd {
        display: block;
        width: 100%;
        text-align: left;
      }

      dd {
        margin-bottom: $margin;
        padding-top: 0;
        padding-bottom: 0;
      }
    }
  }

  @media (max-width: 900px) {
    .app {
      text-align: left;
    }

    dl {
      max-width: 100%;
    }
  }

  .no-right-margin {
    margin-right: 0 !important;
  }

</style>
