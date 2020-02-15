import { ServerErrorCodes} from '@/domains/shared/enums/serverErrorCodes';

export class ServerErrorHelper {
    public isRecordNotFound(error: any): boolean {
        return ((error.hasOwnProperty('code')) && (error.code === ServerErrorCodes.RECORD_NOT_FOUND));
    }
}
