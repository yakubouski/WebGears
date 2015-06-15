/**
 * генерация пароля
 * @param {int} nMin
 * @param {int} nMax
 * @param {int} sSpecial
 * @param {string} sChars
 * @returns {String}
 */
var Password = function (nMin, nMax, sSpecial, sChars) {
    var chars = '23456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
    var specail = '~!@#$%^&*';
    (nMin === undefined) && (nMin = 8);
    (nMax === undefined) && (nMax = nMin);
    (sChars === undefined) && (sChars = chars);
    sChars += ((sSpecial !== undefined) ? (sSpecial === true ? specail : sSpecial) : '');
    var length = sChars.length;
    var count = Math.floor(((nMin === nMax ? nMax : ((parseInt(Math.random() * Math.random() * 100000) % (nMax - nMin)) + nMin) + 3) / 4)) * 4;
    var password = '';
    for (var i = 0; i < count; i++) {
	password += sChars[((parseInt(Math.random() * 1000) % length))];
    }
    return password;
};
/**
 * Шаблонизация строки
 * @param {string} tpl шаблон, пременне вставляются в виде "<div class='{$ClassName}'>Шаблон</div>"
 * @returns {Template}
 */
var Template = function (tpl) {
    var rePattern = /\{\$(\w+)\}/;
    return {
	Exec: function (args) {
	    var result = tpl;
	    while (match = rePattern.exec(result)) {
		result = result.replace(match[0], args[match[1]] !== undefined ? (typeof iterator === 'function' ? args[match[1]]() : args[match[1]]) : '');
	    }
	    return result;
	}
    }
};
