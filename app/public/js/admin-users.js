let actionType = document.getElementById('action');

let btnEdit = document.getElementById('btn-edit');
let editorUsername = document.getElementById('editor-username');
let editorEmail = document.getElementById('editor-email');
let editorPassword = document.getElementById('editor-password');
let editorType = document.getElementById('editor-type');

$('#btn-create-editor').click(function () {
    actionType.value = 'add-account';
    editorUsername.value = '';
    editorEmail.value = '';
    editorPassword.value = '';
    editorType.selectedIndex = 0;

    btnEdit.value = "Add";

    editorUsername.disabled = false;
    editorEmail.disabled = false;
    editorPassword.disabled = false;
    editorType.disabled = false;

    btnEdit.disabled = false;

    if (isInOneColumnMode()) {
        toggleEditorOverlay('editor', true);
    }
});

$('#btn-cancel-edit').click(function () {
    editorUsername.value = '';
    editorEmail.value = '';
    editorPassword.value = '';
    editorType.selectedIndex = 0;

    document.getElementById('account-id').value = -1;
    btnEdit.disabled = true;
    editorUsername.disabled = true;
    editorEmail.disabled = true;
    editorPassword.disabled = true;
    editorType.disabled = true;

    toggleEditorOverlay('editor', false);
});

// Modal
function hideModal() {
    $("#confirm-remove").modal("hide");
}

$('#confirm-remove-close').click(hideModal);
$('#modal-btn-no').click(hideModal);

function deleteById(id) {
    document.getElementById("confirm-remove-id").innerHTML = id;
    $('#modal-btn-yes').off('click');
    $('#modal-btn-yes').click(function () {
        formBuilder("post", "/admin/users", {
            "action": "delete-account",
            "account-id": id
        });
    });

    $("#confirm-remove").modal('show');
}

function startEditor(id) {
    document.getElementById('account-id').value = id;
    actionType.value = 'edit-account';

    editorUsername.value = document.getElementById('account-' + id).children[1].innerHTML;
    editorUsername.disabled = false;

    editorEmail.value = document.getElementById('account-' + id).children[2].innerHTML;
    editorEmail.disabled = false;

    editorType.value = document.getElementById('account-' + id).children[3].innerHTML;
    editorType.disabled = false;

    editorPassword.value = '';
    editorPassword.disabled = false;

    btnEdit.disabled = false;
    btnEdit.value = "Update";

    if (isInOneColumnMode()) {
        toggleEditorOverlay('editor', true);
    }
}