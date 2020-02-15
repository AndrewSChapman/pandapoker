import { LoggerInterface } from '@/domains/shared/logger/interfaces/LoggerInterface';
import { LogLevel } from '@/domains/shared/logger/types/LogLevel';

export class Logger implements LoggerInterface {
    private readonly thresholdLevel: LogLevel;

    constructor(threshold: LogLevel) {
        this.thresholdLevel = threshold;
    }

    public log(logLevel: LogLevel, message: string): void {
        const now = new Date();
        const dateString = now.toUTCString();

        /* tslint:disable */
        console.log(dateString + ' / ' + this.logLevelToString(logLevel) + ': ' + message);
        /* tslint:enable */
    }

    private logLevelToString(logLevel: LogLevel): string {
        switch (logLevel) {
            case LogLevel.DEBUG:
                return 'Debug';

            case LogLevel.INFO:
                return 'Information';

            case LogLevel.WARN:
                return 'Warning';

            case LogLevel.ERROR:
                return 'Error';

            case LogLevel.CRITICAL:
                return 'Critical';
        }
    }
}

