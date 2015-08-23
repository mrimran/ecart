/**
 * @project: CartMigration
 * @author : LitExtension
 * @url    : http://litextension.com
 * @email  : litextension@gmail.com
 */

Validation.add('lecamg-limit-demo', 'The value is not within the specified range 1 - 10',
    function(v, elm) {
        if (Validation.get('IsEmpty').test(v)) {
            return true;
        }

        var numValue = parseNumber(v);
        if (isNaN(numValue)) {
            return false;
        }

        var reRange = /^number-range-(-?[\d.,]+)?-(-?[\d.,]+)?$/,
            result = true;

        $w(elm.className).each(function(name) {
            var m = reRange.exec(name);
            if (m) {
                result = result
                    && (m[1] == null || m[1] == '' || numValue >= parseNumber(m[1]))
                    && (m[2] == null || m[2] == '' || numValue <= parseNumber(m[2]));
            }
        });

        return result;
    }
);

Validation.add('lecamg-demo-empty', 'The value is empty!',
    function(v, elm) {
        if (v.length == 0) {
            return true;
        } else{
            return false;
        }
    }
);