export default class DateHelper {
    public isoDateToLocaleString(isoDate: string): string {
        const timestamp = Date.parse(isoDate);
        if (isNaN(timestamp)) {
            return '';
        }

        const theDate = new Date(timestamp);
        return theDate.toLocaleDateString();
    }

    public isoDateToFriendlyShortDate(isoDate: string): string {
        const timestamp = Date.parse(isoDate);
        if (isNaN(timestamp)) {
            return '';
        }

        const theDate = new Date(timestamp);

        return this.dayOfWeekToDayName(theDate.getDay()) + ' ' +
            theDate.getDate() + this.getDayNumberSuffix(theDate.getDate()) +
            ' ' + this.monthNumberToMonthName(theDate.getMonth());
    }

    public timestampToFriendlyDateWithTime(timestamp: number): string {
        const theDate = new Date(timestamp);

        return this.dayOfWeekToDayName(theDate.getDay()) + ' ' +
            theDate.getDate() + this.getDayNumberSuffix(theDate.getDate()) +
            ' ' + this.monthNumberToMonthName(theDate.getMonth()) +
            ' ' + this.getTimeString(theDate, false);
    }

    public dateOfBirthToFriendlyShortDate(dateOfBirth: string): string {
        const timestamp = Date.parse(dateOfBirth);
        if (isNaN(timestamp)) {
            return '';
        }

        const theDate = this.convertDateOfBirthToComparableDate(new Date(timestamp));

        return this.dayOfWeekToDayName(theDate.getDay()) + ' ' +
            theDate.getDate() + this.getDayNumberSuffix(theDate.getDate()) +
            ' ' + this.monthNumberToMonthName(theDate.getMonth());
    }

    public dayOfWeekToDayName(dayNo: number): string {
        if ((dayNo < 0) && (dayNo > 6)) {
            throw Error('Invalid day number');
        }

        const dayNames = [
            'Sun',
            'Mon',
            'Tue',
            'Wed',
            'Thu',
            'Fri',
            'Sat',
        ];

        return dayNames[dayNo];
    }

    public monthNumberToMonthName(monthNumber: number): string {
        if ((monthNumber < 0) && (monthNumber > 12)) {
            throw Error('Invalid month number!');
        }

        const monthName = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec',
        ];

        return monthName[monthNumber];
    }

    public daysUntilDate(futureDate: Date): number {
        const today = new Date();
        const diff = futureDate.getTime() - today.getTime();
        const diffDays = Math.floor(diff / 86400000) + 1;

        return diffDays;
    }

    public daysUntilDateOfBirth(dateOfBirth: Date): number {
        dateOfBirth = this.convertDateOfBirthToComparableDate(dateOfBirth);
        return this.daysUntilDate(dateOfBirth);
    }

    public getDayNumberSuffix(dayNumber: number) {
        const j = dayNumber % 10;
        const k = dayNumber % 100;

        if (j === 1 && k !== 11) {
            return 'st';
        }

        if (j === 2 && k !== 12) {
            return 'nd';
        }

        if (j === 3 && k !== 13) {
            return 'rd';
        }

        return 'th';
    }

    public unixTimestamp(): number {
        return Math.round((new Date()).getTime() / 1000);
    }

    private convertDateOfBirthToComparableDate(dateOfBirth: Date): Date {
        const today = new Date();

        if (dateOfBirth.getMonth() > today.getMonth()) {
            // Easiest case scenario - the month is in the future.
            // Just set the year to the same as the target.
            dateOfBirth.setFullYear(today.getFullYear());
        } else if (dateOfBirth.getMonth() < today.getMonth()) {
            // The birthday month is in the past, so we need to add a year to
            // calculate how many days it is until this birthday happens next year
            dateOfBirth.setFullYear(today.getFullYear() + 1);
        } else if ((dateOfBirth.getMonth() === today.getMonth()) && (dateOfBirth.getDate() < today.getDate()))  {
            // The month is ths same, but the date is earlier.  i.e. the birthday has past for this year.
            // We need to add another year to be able to calculate how many days until is happens again.
            dateOfBirth.setFullYear(today.getFullYear() + 1);
        } else {
            dateOfBirth.setFullYear(today.getFullYear());
        }

        return dateOfBirth;
    }

    private getTimeString(theDate: Date, includeSeconds: boolean = false): string {
        let meridian = 'a.m.';

        let hours = theDate.getHours();
        const minutes = theDate.getMinutes();
        const seconds = theDate.getSeconds();

        if (hours > 12) {
            hours = hours - 12;
            meridian = 'p.m.';
        } else if (hours === 12) {
            meridian = 'p.m.';
        }

        let result = hours.toString() + ':';

        if (minutes < 10) {
            result += '0';
        }

        result += minutes;

        if (includeSeconds) {
            result += ':';

            if (seconds < 10) {
                result += '0';
            }

            result += seconds;
        }

        result += ' ' + meridian;

        return result;
    }
}
