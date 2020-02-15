import { LoggerInterface } from '@/domains/shared/logger/interfaces/LoggerInterface';
import { LogLevel } from '@/domains/shared/logger/types/LogLevel';
import { Environment } from '@/domains/shared/enums/Environment';
import { Logger } from '@/domains/shared/logger/Logger';

let logger: Logger|null = null;

export function initialiseLogger(environment: Environment) {
    if (!logger) {
        switch (environment) {
            case Environment.Production:
                logger = new Logger(LogLevel.ERROR);
                break;

            case Environment.Testing:
                logger = new Logger(LogLevel.WARN);
                break;

            default:
                logger = new Logger(LogLevel.DEBUG);
                break;
        }
    }
}

export function getLogger(): LoggerInterface {
    if (!logger) {
        throw Error('Logger not set!');
    }

    return logger;
}
