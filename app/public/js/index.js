// OPINION CREATOR
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

// Find all overlays and add the "close" action to closing button.
let overlays = document.getElementsByClassName("overlay");
for (let overlay of overlays) {
    try {
        let overlayContent = overlay.getElementsByClassName('overlay-content')[0];
        if (overlayContent == null) {
            throw new Error("Overlay content is missing");
        }

        let btnClose = overlayContent.getElementsByClassName('btn-overlay-close')[0];
        if (btnClose == null) {
            throw new Error('Close Button is missing');
        }

        // Make button close the overlay.
        btnClose.onclick = function () {
            overlay.style.display = 'none';
        };

        // And same for the on-click outside of the content bounds.
        $(document).mouseup(function (e) {
            if (!$(overlayContent).is(e.target) && $(overlayContent).has(e.target).length === 0) {
                overlay.style.display = 'none';
            }
        });
    } catch (ex) {
        console.log("Eror while hooking overlay " + overlay + ": " + ex);
    }
}

// Now do the similar thing for all popups (minus the close button, as popups aren't meant to have close button).
let popups = document.getElementsByClassName('popup');
for (let popup of popups) {
    $(document).mouseup(function (e) {
        if (!$(popup).is(e.target) && $(popup).has(e.target).length === 0) {
            popup.style.display = 'none';
        }
    })
}

// Ran when "create new opinion" panel is shown.
function showOpinionPanel() {
    let opinionPanel = document.getElementById('input-opinion-panel');
    let btnSubmit = document.getElementById('btn-submit-opinion');
    let mdiv = document.createElement("div");
    mdiv.innerHTML = "Send " + getNewSendButtonEntity();
    btnSubmit.value = (mdiv.textContent || mdiv.innerHTML);
    opinionPanel.style.display = 'block';
    cleanOpinionPanelForm();
}

// Changes the emoji in the 'Send' opinion button :D
function getNewSendButtonEntity() {
    let entity;
    do {
        entity = allowedSendEntities[Math.floor(Math.random() * allowedSendEntities.length)];
    } while (entity == lastSendEntity);
    lastSendEntity = entity;
    return entity;
}

// Resets the form used for creating the opinion.
function cleanOpinionPanelForm() {
    titleInput.value = '';
    contentInput.value = "";
    validateTitleInput();
    validateContentInput();
}

// Basically, updates the input counter for the Title of the opinion.
function validateTitleInput() {
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
    let form = $('<form></form>');

    form.attr("method", "post");
    form.attr("action", "/");

    let actionTypeField = $('<input></input>');
    actionTypeField.attr("name", "actionType");
    actionTypeField.attr("value", "reaction");
    form.append(actionTypeField);

    let opinionIDField = $('<input></input>');
    opinionIDField.attr("name", "opinionID");
    opinionIDField.attr("value", currentlyReactingToOpinion);
    form.append(opinionIDField);

    let reactionIDField = $('<input></input>');
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
    let form = $("<form style='display: none'></form>");

    form.attr("method", "get");
    form.attr("action", "/");

    if (window.location.search.includes("sortby=")) {
        let urlParams = new URLSearchParams(window.location.search);

        let sortbyField = $('<input></input>');
        sortbyField.attr("name", "sortby");
        sortbyField.attr("value", urlParams.get("sortby"));
        form.append(sortbyField);
    }

    let pageNumberField = $('<input></input>');
    pageNumberField.attr("name", "page");
    pageNumberField.attr("value", pageNumber);
    form.append(pageNumberField);

    $(document.body).append(form);

    form.submit();
}