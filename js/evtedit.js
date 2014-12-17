/**
 * check content of input
 * @param {input element} tag
 * @returns {ret|Boolean}
 */
function checkTag(tag) {
    ret = true;
    if (tag.value === "")
    {
        tag.style.backgroundColor = '#fcc';
        ret = false;
    }
    else
    {
        tag.style.backgroundColor = '#fff';
    }
    return ret;
}

/**
 * reset input box' backgroud color
 * @param {this pointer} what
 */
function resetColor(what) {
    f = what.form;
    f.date.style.backgroundColor = "white";
    f.what.style.backgroundColor = "white";
}

/**
 * check all input fields of content
 * @param {this pointer} what
 * @param {error message} msg
 * @returns {submit|Boolean}
 */
function checkForm(what, msg) {
    f      = what.form;
    submit = true;

    if (!checkTag(f.date))
        submit = false;

    if (!checkTag(f.what))
        submit = false;

    if (!submit)
    {
        alert(msg);
    }
    return submit;
}
