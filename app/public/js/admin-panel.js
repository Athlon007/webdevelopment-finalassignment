let editorTitle = document.getElementById("editor-title");
let editorContent = document.getElementById("editor-content");
let editorSubmit = document.getElementById('btn-edit');

function hideModal() {
    $("#confirm-remove").modal("hide");
}

$('#confirm-remove-close').click(hideModal);
$('#modal-btn-no').click(hideModal);

function logout() {
    formBuilder("post", {
        "action": "logout"
    });
}

function deleteOpinionById(id) {
    document.getElementById("confirm-remove-id").innerHTML = id;
    $('#modal-btn-yes').off('click');
    $('#modal-btn-yes').click(function () {
        formBuilder("post", {
            "action": "delete-opinion",
            "opinion-id": id
        });
    });

    $("#confirm-remove").modal('show');
}

function formBuilder(method, attributes) {
    let form = $('<form style="display: none"></form>');

    form.attr("method", method);
    form.attr("action", "/admin");

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

function startEditorForOpinion(id) {
    let row = document.getElementById('opinion-' + id);
    let title = row.children[1].innerHTML;
    let content = row.children[2].innerHTML;

    document.getElementById('editor-id').value = id;
    editorTitle.value = title;
    editorContent.value = content;

    editorTitle.disabled = false;
    editorContent.disabled = false;
    editorSubmit.disabled = false;
}

function clearEditor() {
    editorTitle.value = "";
    editorContent.value = "";
    editorTitle.disabled = true;
    editorContent.disabled = true;
    editorSubmit.disabled = true;
}