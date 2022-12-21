$('#btn-logout').click(function () {
    formBuilder("post", "/admin", {
        "action": "logout"
    });
});

$('#btn-settings').click(function () {
    window.location.href = "/admin/settings";
})


function toggleEditorOverlay(objectID, isEnabled) {
    let editor = document.getElementById(objectID);
    editor.style.display = isEnabled ? "block" : '';
}

function isInOneColumnMode() {
    return window.innerWidth < 768;
}

function formBuilder(method, action, attributes) {
    let form = $('<form style="display: none"></form>');

    form.attr("method", method);
    form.attr("action", action);

    for (const [key, value] of Object.entries(attributes)) {
        let field = $('<input></input>');
        field.attr("name", key);
        field.attr("value", value);
        form.append(field);
    }
    // The form needs to be a part of the document in
    // order for us to be able to submit it.
    $(document.body).append(form);
    form.submit();
}