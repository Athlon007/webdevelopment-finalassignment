let editorTitle = document.getElementById("editor-title");
let editorContent = document.getElementById("editor-content");
let editorSubmit = document.getElementById('btn-edit');

function hideModal() {
    $("#confirm-remove").modal("hide");
}

$('#confirm-remove-close').click(hideModal);
$('#modal-btn-no').click(hideModal);


function deleteOpinionById(id) {
    document.getElementById("confirm-remove-id").innerHTML = id;
    $('#modal-btn-yes').off('click');
    $('#modal-btn-yes').click(function () {
        formBuilder("post", "/admin", {
            "action": "delete-opinion",
            "opinion-id": id
        });
    });

    $("#confirm-remove").modal('show');
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

    if (isInOneColumnMode()) { // the width where col-md ends.
        toggleEditorOverlay('editor', true);
    }
}

function clearEditor() {
    editorTitle.value = "";
    editorContent.value = "";
    editorTitle.disabled = true;
    editorContent.disabled = true;
    editorSubmit.disabled = true;

    toggleEditorOverlay('editor', false);
}