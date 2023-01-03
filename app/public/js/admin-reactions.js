let editorTitle = document.getElementById('editor-emoji');
let editorIsNegative = document.getElementById('editor-is-negative');
let btnEdit = document.getElementById('btn-edit');
let actionType = document.getElementById('reaction-action');

$('#btn-create-reaction-editor').click(function () {
    actionType.value = 'add-reaction';
    editorTitle.value = '';
    editorIsNegative.checked = false;
    btnEdit.value = "Add";

    editorTitle.disabled = false;
    editorIsNegative.disabled = false;
    btnEdit.disabled = false;

    if (isInOneColumnMode()) {
        toggleEditorOverlay('editor', true);
    }
});

$('#btn-cancel-edit').click(function () {
    editorTitle.value = '';
    document.getElementById('reaction-id').value = -1;
    btnEdit.disabled = true;
    editorTitle.disabled = true;
    editorIsNegative.checked = false;

    toggleEditorOverlay('editor', false);
});

// Modal
function hideModal() {
    $("#confirm-remove").modal("hide");
}

$('#confirm-remove-close').click(hideModal);
$('#modal-btn-no').click(hideModal);

function deleteReactionById(id) {
    document.getElementById("confirm-remove-id").innerHTML = id;
    $('#modal-btn-yes').off('click');
    $('#modal-btn-yes').click(function () {
        formBuilder("post", "/admin/reactions", {
            "action": "delete-reaction",
            "reaction-id": id
        });
    });

    $("#confirm-remove").modal('show');
}

function startEditorReaction(reactionID) {
    document.getElementById('reaction-id').value = reactionID;
    actionType.value = 'edit-reaction';

    let reactionTitle = document.getElementById('reaction-' + reactionID).children[1].innerHTML;
    editorTitle.value = reactionTitle;
    editorTitle.disabled = false;

    let reactionIsNegative = document.getElementById('reaction-' + reactionID).children[2].innerHTML == "Yes";
    editorIsNegative.checked = reactionIsNegative;
    editorIsNegative.disabled = false;

    btnEdit.disabled = false;
    btnEdit.value = "Update";

    if (isInOneColumnMode()) {
        toggleEditorOverlay('editor', true);
    }
}