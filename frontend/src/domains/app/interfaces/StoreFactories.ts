import HelperFactory from '@/domains/shared/helpers/helperFactory';
import RequestFactory from '@/domains/shared/http/requestFactory/requestFactory';

export interface StoreFactories {
    helperFactory: HelperFactory;
    requestFactory: RequestFactory;
}
