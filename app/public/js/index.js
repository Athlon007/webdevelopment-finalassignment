let opinionPanel = document.getElementById('input-opinion-panel');
let opinionPanelJQ = $('#input-opinion-panel-content');

// Title
let titleInput = document.getElementById('title-input');
let titleCharsetCounter = document.getElementById('title-char-counter');
let titleMaxChars = titleInput.maxLength;

// Content
let contentInput = document.getElementById('content-input');
let contentCharsetCounter = document.getElementById('content-char-counter');
let contentMaxChars = contentInput.maxLength;

// REACTION PANEL
let reactionPanel = document.getElementById('reaction-panel');
let reactionPanelJQ = $('#reaction-panel');
reactionPanel.style.display = "block";
let boundsReactionPanel = reactionPanel.getBoundingClientRect();
reactionPanel.style.display = "none";
let currentlyReactingToOpinion = -1;

// List of emojis displayed next to "Send" button.
let allowedSendEntities = ["&#128563;", "&#128561;", "&#129300;", "&#129763;",
    "&#129325;", "&#129323;", "&#128558;", "&#129396;", "&#128569;", "&#128576;",
    "&#129299;", "&#129312;", "&#129315;", "&#129316;", "&#129322;", "&#129488;"]
let lastSendEntity = '';


$(document).mouseup(function (e) {
    // Hide reaction panel on click outside of it.
    if (!reactionPanelJQ.is(e.target) && reactionPanelJQ.has(e.target).length === 0) {
        reactionPanel.style.display = 'none';
    }

    // When clicking outside of the bounds of the "create new opinion" panel.
    if (!opinionPanelJQ.is(e.target) && opinionPanelJQ.has(e.target).length === 0) {
        hideOpinionPanel();
    }
})

// Ran when "create new opinion" panel is shown.
function showOpinionPanel() {
    let btnSubmit = document.getElementById('btn-submit-opinion');
    var mdiv = document.createElement("div");
    mdiv.innerHTML = "Send " + getNewSendButtonEntity();
    btnSubmit.value = (mdiv.textContent || mdiv.innerHTML);
    opinionPanel.style.display = 'block';
    cleanOpinionPanelForm();
}

function getNewSendButtonEntity() {
    let entity;
    do {
        entity = allowedSendEntities[Math.floor(Math.random() * allowedSendEntities.length)];
    } while (entity == lastSendEntity);
    lastSendEntity = entity;
    return entity;
}

// Ran when hiding the "create new opinion" panel.
function hideOpinionPanel() {
    opinionPanel.style.display = 'none';
}

// Resets the form used for creating the opinion.
function cleanOpinionPanelForm() {
    titleInput.value = '';
    titleCharsetCounter.innerHTML = "0/" + titleMaxChars;

    contentInput.value = "";
    contentCharsetCounter.innerHTML = "0/" + contentMaxChars;
}

// Basically, updates the input counter for the Title of the opinion.
function validateTitleInput() {
    // Get the length of the field.
    let charsCount = titleInput.value.length;
    titleCharsetCounter.innerHTML = charsCount + "/" + titleMaxChars;
}

// Updates the input counter for the content of the opinion.
function validateContentInput() {
    let charsCount = contentInput.value.length;
    contentCharsetCounter.innerHTML = charsCount + "/" + contentMaxChars;
}

// Show reaction panel, upon clicking the the "+" button in the opinion.
function showReactionPanel(opinionID) {
    let buttonSender = document.getElementById('button-add-reaction-' + opinionID);
    let boundsButtonSender = buttonSender.getBoundingClientRect();

    let x = boundsButtonSender.left - boundsReactionPanel.width / 2 + boundsReactionPanel.width / 8 + window.scrollX;
    let y = boundsButtonSender.top - boundsReactionPanel.height - boundsButtonSender.height * 0.2 + window.scrollY;

    reactionPanel.style.left = x + "px";
    reactionPanel.style.top = y + "px";
    reactionPanel.style.display = 'block';

    currentlyReactingToOpinion = opinionID;
}

function addNewReactionToOpinion(reactionID) {
    var form = $('<form></form>');

    form.attr("method", "post");
    form.attr("action", "/");

    var actionTypeField = $('<input></input>');
    actionTypeField.attr("name", "actionType");
    actionTypeField.attr("value", "reaction");
    form.append(actionTypeField);

    var opinionIDField = $('<input></input>');
    opinionIDField.attr("name", "opinionID");
    opinionIDField.attr("value", currentlyReactingToOpinion);
    form.append(opinionIDField);

    var reactionIDField = $('<input></input>');
    reactionIDField.attr("name", "reactionID");
    reactionIDField.attr("value", reactionID);
    form.append(reactionIDField);

    // The form needs to be a part of the document in
    // order for us to be able to submit it.
    $(document.body).append(form);
    form.submit();
}

function increaseExistingOpinionCount(opinionID, reactionID) {
    currentlyReactingToOpinion = opinionID;
    addNewReactionToOpinion(reactionID);
}

function changePage(pageNumber) {
    var form = $("<form style='display: none'></form>");

    form.attr("method", "get");
    form.attr("action", "/");

    if (window.location.search.includes("sortby=")) {
        let urlParams = new URLSearchParams(window.location.search);

        var sortbyField = $('<input></input>');
        sortbyField.attr("name", "sortby");
        sortbyField.attr("value", urlParams.get("sortby"));
        form.append(sortbyField);
    }

    var pageNumberField = $('<input></input>');
    pageNumberField.attr("name", "page");
    pageNumberField.attr("value", pageNumber);
    form.append(pageNumberField);

    $(document.body).append(form);

    form.submit();
}