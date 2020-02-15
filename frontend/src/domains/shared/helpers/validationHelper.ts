import { faBullseye } from '@fortawesome/free-solid-svg-icons';

export default class ValidationHelper {
    public static emailAddressValid(emailAddress: string): boolean {
        /* tslint:disable */
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        /* tslint:enable */
        return re.test(emailAddress);
    }

    public static isNumber(value: any): boolean {
        if (isNaN(value)) {
            return false;
        }

        return true;
    }

    public static isISODate(value: string): boolean {
        if ((!value) || (value === '')) {
           return false;
        }

        const parts = value.split('-');
        if (parts.length !== 3) {
            return false;
        }

        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10);
        const day = parseInt(parts[2], 10);

        if ((isNaN(year)) || (isNaN(month)) || (isNaN(day))) {
            return false;
        }

        if (year < 0) {
            return false;
        }

        if ((month < 1) || (month > 12)) {
            return false;
        }

        if ((day < 1) || (day > 31)) {
            return false;
        }

        return true;
    }
}
