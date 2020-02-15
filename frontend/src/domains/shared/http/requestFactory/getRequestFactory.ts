import RequestFactory from '@/domains/shared/http/requestFactory/requestFactory';

let requestFactoryInstance: RequestFactory|null = null;

export function setRequestFactory(requestFactory: RequestFactory) {
    requestFactoryInstance = requestFactory;
}

export function getRequestFactory(): RequestFactory {
    if (!requestFactoryInstance) {
        throw new Error('RequestFactory not initialised!');
    }

    return requestFactoryInstance;
}
