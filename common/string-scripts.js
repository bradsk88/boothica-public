/**
 * From http://jsperf.com/html-truncate-by-javascript/3
 */

var Stack = function () {
    this.items = [];
};

Stack.prototype = {
    size: function () {
        return this.items.length;
    },

    push: function (key) {
        this.items.push(key);
    },

    pop: function () {
        return this.items.pop();
    },

    /**
     * dump all close tags and append to truncated content while reaching upperbound
     */
    dumpCloseTag: function () {
        var html = '',
            i, len, tag;

        for(i = 0, len = this.size(); i < len; ++i) {
            tag = this.pop();
            html += '</' + tag.tag + '>';
        }

        return html;
    }
};

var stack = new Stack();


/**
 * HTML-Truncate Utility
 * This utility truncates html text and keep tag safe(close properly)
 */
function truncate(string, maxLength, options) {
    var content = '',       // traced text
        total = 0,          // record how many characters we traced so far
        matches = true,
        result, index, tag, tail;

    while(matches) {
        matches = /<\/?\w+(\s+\w+="[^"]*")*>/g.exec(string);
        if ( ! matches) { break; }
        result = matches[0];
        index = matches.index;

        // overceed, dump everything to clear stack
        if (total + index > maxLength) {
            content += string.substring(0, maxLength - total);
            content += stack.dumpCloseTag();

            break;
        } else {
            total += index;
            content += string.substring(0, index);
        }

        if (-1 === result.indexOf('</')) {
            tail = result.indexOf(' ');
            tail = (-1 === tail) ? result.indexOf('>') : tail;
            stack.push({
                tag: result.substring(1, tail),
                html: result
            });
        } else {
            stack.pop();
        }

        content += result;

        string = string.substring(index + result.length);
    }

    return content;
}