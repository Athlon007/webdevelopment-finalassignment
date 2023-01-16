let editorTitle = document.getElementById('editor-title');
let btnEdit = document.getElementById('btn-edit');
let actionType = document.getElementById('topic-action');

function startEditorTopic(topicID) {
    document.getElementById('topic-id').value = topicID;
    actionType.value = 'edit-topic';

    let topicTitle = document.getElementById('topic-' + topicID).children[1].innerHTML;
    editorTitle.value = topicTitle;
    editorTitle.disabled = false;
    btnEdit.disabled = false;
    btnEdit.value = "Update";

    if (isInOneColumnMode()) {
        toggleEditorOverlay('editor', true);
    }
}

$('#btn-create-topic-editor').click(function () {
    actionType.value = 'add-topic';
    editorTitle.value = '';
    btnEdit.value = "Add";

    editorTitle.disabled = false;
    btnEdit.disabled = false;

    if (isInOneColumnMode()) {
        toggleEditorOverlay('editor', true);
    }
});

$('#btn-cancel-edit').click(function () {
    editorTitle.value = '';
    document.getElementById('topic-id').value = -1;
    btnEdit.disabled = true;
    editorTitle.disabled = true;

    toggleEditorOverlay('editor', false);
});

// Modal
function hideModal() {
    $("#confirm-remove").modal("hide");
}

$('#confirm-remove-close').click(hideModal);
$('#modal-btn-no').click(hideModal);


function deleteTopicById(id) {
    document.getElementById("confirm-remove-id").innerHTML = id;
    $('#modal-btn-yes').off('click');
    $('#modal-btn-yes').click(function () {
        formBuilder("post", "/admin/topics", {
            "action": "delete-topic",
            "topic-id": id
        });
    });

    $("#confirm-remove").modal('show');
}

document.getElementById('btn-force-next-topic').onclick = function () {
    formBuilder('POST', '/admin/topics', {
        "action": "force-next-topic"
    });
}