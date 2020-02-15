import BreakPoint from '@/domains/shared/enums/breakPoint';

export default class BreakPointHelper {
    private vueStore: any;
    private intervalId: any;

    public init(vueStore: any): void {
        this.vueStore = vueStore;
        this.vueStore.commit('setScreenWidth', window.innerWidth);
        this.watchScreenResize();
    }

    public greaterThanOrEqualTo(breakpoint: BreakPoint): boolean {
        const screenWidth = this.vueStore.state.settings.screenWidth;

        switch (breakpoint) {
            case BreakPoint.SMALL:
                return screenWidth >= this.smallWidth;

                case BreakPoint.MEDIUM:
                    return screenWidth >= this.mediumWidth;

                case BreakPoint.LARGE:
                    return screenWidth >= this.largeWidth;

                default:
                    throw new Error('BreakPointHelper::greaterThanOrEqualTo - ' +
                        'Invalid Breakpoint! Breakpoint name was: ' + breakpoint);
        }
    }

    public greaterThan(breakpoint: BreakPoint): boolean {
        const screenWidth = this.vueStore.state.settings.screenWidth;

        switch (breakpoint) {
            case BreakPoint.SMALL:
                return screenWidth > this.smallWidth;

            case BreakPoint.MEDIUM:
                return screenWidth > this.mediumWidth;

            case BreakPoint.LARGE:
                return screenWidth > this.largeWidth;

            default:
                throw new Error('BreakPointHelper::greaterThan - ' +
                    'Invalid Breakpoint! Breakpoint name was: ' + breakpoint);
        }
    }

    public lessThanOrEqualTo(breakpoint: BreakPoint): boolean {
        const screenWidth = this.vueStore.state.settings.screenWidth;

        switch (breakpoint) {
            case BreakPoint.SMALL:
                return screenWidth <= this.smallWidth;

                case BreakPoint.MEDIUM:
                    return screenWidth <= this.mediumWidth;

                case BreakPoint.LARGE:
                    return screenWidth <= this.largeWidth;

                default:
                    throw new Error('BreakPointHelper::lessThanOrEqualTo - ' +
                        'Invalid Breakpoint! Breakpoint name was: ' + breakpoint);
        }
    }

    /**
     * Watch for any window "onresize" events and update the Vue
     * store with the new width.
     */
    private watchScreenResize(): void {
        window.onresize = () => {
            /*
            A window resize event can fire many times as the window expands or contracts.
            Add debouncing to only fire the event once every 100ms.
            */
            if (this.intervalId) {
                clearInterval(this.intervalId);
            }

            this.intervalId = setInterval(() => {
                this.vueStore.commit('setScreenWidth', window.innerWidth);
                clearInterval(this.intervalId);
            }, 100);
        };
    }

    /**
     * Define the breakpoint widths.
     * Change them here if you want to change the definitions of
     * "small", "medium" and "large"
     */
    private get smallWidth(): number {
        return 600;
    }

    private get mediumWidth(): number {
        return 900;
    }

    private get largeWidth(): number {
        return 1200;
    }
}
