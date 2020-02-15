import {LogLevel} from '@/domains/shared/logger/types/LogLevel';

export interface LoggerInterface {
    log(logLevel: LogLevel, message: string): void;
}
