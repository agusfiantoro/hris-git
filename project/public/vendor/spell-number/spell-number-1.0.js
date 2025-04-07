/**
 * 
 * @param int number 
 * @param string custom_join_character 
 * @returns 
 * @author McShaman (http://stackoverflow.com/users/788657/mcshaman) (Original Author)
 * @author Faisal   (github.com/faisal01h) (Indonesian Transliteration)
 */
function spellNumber(number, custom_join_character) {
    n = Math.abs(number);
    var string = n.toString(),
        units, tens, scales, start, end, chunks, chunksLen, chunk, ints, i, word, words;

    var and = custom_join_character || '';

    /* Is number zero? */
    if (parseInt(string) === 0) {
        return 'nol';
    }

    /* Array of units as words */
    units = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas', 'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'];

    /* Array of tens as words */
    tens = ['', '', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'];

    /* Array of scales as words */
    scales = ['', 'ribu', 'juta', 'miliar', 'triliun', 'kuadriliun', 'kuintiliun', 'sekstiliun', 'septiliun', 'oktiliun', 'noniliun', 'desiliun'];

    /* Split user arguemnt into 3 digit chunks from right to left */
    start = string.length;
    chunks = [];
    while (start > 0) {
        end = start;
        chunks.push(string.slice((start = Math.max(0, start - 3)), end));
    }

    /* Check if function has enough scale words to be able to stringify the user argument */
    chunksLen = chunks.length;
    if (chunksLen > scales.length) {
        return '';
    }

    /* Stringify each integer in each chunk */
    words = [];
    for (i = 0; i < chunksLen; i++) {

        chunk = parseInt(chunks[i]);

        if (chunk) {

            /* Split chunk into array of individual integers */
            ints = chunks[i].split('').reverse().map(parseFloat);

            /* If tens integer is 1, i.e. 10, then add 10 to units integer */
            if (ints[1] === 1) {
                ints[0] += 10;
            }

            /* Add scale word if chunk is not zero and array item exists */
            if ((word = scales[i])) {
                words.push(word);
            }

            /* Add unit word if array item exists */
            if ((word = units[ints[0]])) {
                words.push(word);
            }

            /* Add tens word if array item exists */
            if ((word = tens[ints[1]])) {
                words.push(word);
            }

            /* Add 'and' string after units or tens integer if: */
            if (ints[0] || ints[1]) {

                /* Chunk has a hundreds integer or chunk is the first of multiple chunks */
                if (ints[2] || !i && chunksLen) {
                    words.push(and);
                }

            }

            /* Add hundreds word if array item exists */
            if ((word = units[ints[2]])) {
                if(units[ints[2]] == 'satu') {
                    words.push('seratus');
                } else {
                    words.push(word + ' ratus');
                }
            }

        }

    }

    words = words.reverse().join(' ').replaceAll("  ", " ").replaceAll("satu ribu", "seribu").replaceAll("ratus seribu", "ratus satu ribu").replaceAll("puluh seribu", "puluh satu ribu");
    if(number < 0) {
        words = 'minus '+words;
    }
    return words;
}

/**
 * Extend Number Prototype with spellNumber() function
 * 
 * IMPORTANT: This function will override Number.prototype.spell(), before running this function, make sure that there is no conflict with existing prototype method!
 * @author Faisal (github.com/faisal01h)
 */
function extendNumberProtoSpell() {
    function spellNumberProtoExtension(custom_join_character) {
        return spellNumber(this.valueOf(), custom_join_character);
    }
    Number.prototype.spell=spellNumberProtoExtension;
}