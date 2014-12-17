/**
 * enable / disable month input
 * @param {this pointer} what
 */
function enaDisa(what) {
    f = what.form;
    if (f.type.value === 'fix')
    {
        f.month.disabled = false;
        f.month.style.backgroundColor = "white";
    }
    else
    {
        f.month.disabled = true;
        f.month.style.backgroundColor = "#eaeaea";
    }
}

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
    f.day.style.backgroundColor = "white";
    f.name.style.backgroundColor = "white";
    f.month.style.backgroundColor = "white";
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

    if (!checkTag(f.name))
        submit = false;

    if (!checkTag(f.day))
        submit = false;

    if (!checkTag(f.day))
        submit = false;

    if (f.type.value === 'fix')
        if (!checkTag(f.month))
            submit = false;

    if (!submit)
    {
        alert(msg);
    }
    return submit;
}
