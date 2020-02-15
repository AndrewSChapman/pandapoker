export default class StringHelper {
    /**
     * Searches for the given keywords within the specified searchText.
     * All keywords must be present (at least as a substring) within the searchText.
     * Returns true if all keywords are matched, false otherwise.
     */
    public static searchInText(searchKeywords: string[], searchText: string): boolean {
        if ((searchKeywords.length === 0) || (searchText === '')) {
            return false;
        }

        searchKeywords = searchKeywords.map((item) => {
            return item.toLowerCase();
        });

        const searchTextItems = searchText.split(' ').map((item) => {
            return item.toLowerCase();
        });

        let allKeywordsFound = true;

        for (const index in searchKeywords) {
            if (searchKeywords.hasOwnProperty(index)) {
                const searchKeyword = searchKeywords[index].trim();

                if (searchKeyword === '') {
                    continue;
                }

                if (searchTextItems.indexOf(searchKeyword) >= 0) {
                    continue;
                } else {
                    // Is the search word a subset of another word?
                    let foundSubstring = false;

                    for (const searchTextIndex in searchTextItems) {
                        if (searchTextItems.hasOwnProperty(searchTextIndex)) {
                            const searchTextWord = searchTextItems[searchTextIndex];

                            if (searchTextWord.indexOf(searchKeyword) >= 0) {
                                foundSubstring = true;
                            }
                        }
                    }

                    if (!foundSubstring) {
                        allKeywordsFound = false;
                        break;
                    }
                }
            }
        }

        return allKeywordsFound;
    }

    public static getFileNameExtension(filename: string): string {
        const dotPos = filename.indexOf('.');
        if (dotPos <= 0) {
            throw Error('Cannot get filename extension file name has not full stop!');
        }

        return filename.substring(dotPos + 1);
    }

    public static filenameHasValidImageExtension(filename: string): boolean {
        const extension = StringHelper.getFileNameExtension(filename).toUpperCase();
        const validExtensions = ['JPG', 'JPEG', 'PNG', 'GIF'];

        return validExtensions.indexOf(extension) >= 0;
    }

    public static paddISODate(value: string): string {
        if ((!value) || (value === '')) {
           return '';
        }

        const parts = value.split('-');
        if (parts.length !== 3) {
            return '';
        }

        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10);
        const day = parseInt(parts[2], 10);

        let result = year + '-';

        if (month < 10) {
            result += '0';
        }

        result += month + '-';

        if (day < 10) {
            result += '0';
        }
        result += day;

        return result;
    }

    public static toInt(value: any, defaultValue: number = 0): number {
        const result = parseInt(value, 10);
        if (isNaN(result)) {
            return defaultValue;
        }

        return result;
    }

    public static toUriString(params: any): string {
        let result = '';

        for (const key in params) {
            if (!params.hasOwnProperty(key)) {
                continue;
            }

            const value = params[key];

            if ((value === null) || (value === '')) {
                continue;
            }

            if (result.length > 0) {
                result += '&';
            }

            result += key + '=';

            if (isNaN(value)) {
                result = result + encodeURI(value);
            } else {
                result = result + value;
            }
        }

        return result;
    }

    public static shorten(value: string, maxLength: number, appendSuffix = '...'): string {
        if (value.length <= maxLength) {
            return value;
        }

        return value.substr(0, maxLength) + appendSuffix;
    }

    public static removeSpaces(value: string): string {
        return value.replace(' ', '');
    }
}
