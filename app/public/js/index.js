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
    opinionPanel.style.display = 'block';
    cleanOpinionPanelForm();
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

    reactionPanel.style.left = x;
    reactionPanel.style.top = y;
    reactionPanel.style.display = 'block';

    document.getElementById('opinion-reaction').value = opinionID;
}