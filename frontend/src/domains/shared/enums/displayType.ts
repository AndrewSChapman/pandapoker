export enum DisplayType {
    POSITIVE = 'positive',
    INFO = 'info',
    WARNING = 'warning',
    ERROR = 'error',
    SELECTOR = 'selector',
    TEXT_ONLY = 'text_only',
}

export class DisplayTypeHelper {
    private displayType: DisplayType;

    constructor(displayType: DisplayType) {
        if (Object.values(DisplayType).includes(displayType)) {
            this.displayType = displayType;
        } else {
            this.displayType = DisplayType.WARNING;
        }
    }

    get className(): string {
        switch (this.displayType) {
            case DisplayType.POSITIVE:
                return 'positive';

            case DisplayType.ERROR:
                return 'error';

            case DisplayType.SELECTOR:
                return 'selector';

            case DisplayType.INFO:
                return 'info';

            case DisplayType.TEXT_ONLY:
                return 'text_only';

            default:
                return 'warning';
        }
    }
}

export default DisplayType;
