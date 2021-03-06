export default class ObjectHelper {
    public static cloneObject(obj: any) {
        if (null == obj || 'object' !== typeof obj) {
            return obj;
        }

        const copy = obj.constructor();
        for (const attr in obj) {
            if (obj.hasOwnProperty(attr)) {
                copy[attr] = obj[attr];
            }
        }

        return copy;
    }
}
