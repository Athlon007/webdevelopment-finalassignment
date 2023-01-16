// OPINION CREATOR
const opinionPanel = document.getElementById('input-opinion-panel');
const warningOpinion = document.getElementById('warning-opinion');
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
reactionPanel.style.display = "block";
let boundsReactionPanel = reactionPanel.getBoundingClientRect();
reactionPanel.style.display = "none";
let currentlyReactingToOpinion = -1;

// List of emojis displayed next to "Send" button.
let allowedSendEntities = [
    "&#128563;", "&#128561;", "&#129300;", "&#129763;",
    "&#129325;", "&#129323;", "&#128558;", "&#129396;", "&#128569;", "&#128576;",
    "&#129299;", "&#129312;", "&#129315;", "&#129316;", "&#129322;", "&#129488;"
]
let lastSendEntity = '';

// Find all overlays and add the "close" action to closing button.
let overlays = document.querySelectorAll(".overlay,.dismisable");
for (let overlay of overlays) {
    try {
        let overlayContent = overlay.getElementsByClassName('overlay-content')[0];

        let btnClose = overlayContent != null
            ? overlayContent.getElementsByClassName('btn-overlay-close')[0]
            : overlay.getElementsByClassName('btn-overlay-close')[0];

        if (btnClose == null) {
            throw new Error('Close Button is missing');
        }

        // Make button close the overlay.
        btnClose.onclick = function () {
            overlay.style.display = 'none';
        };

        // And same for the on-click outside of the content bounds.
        document.addEventListener("mouseup", function (e) {
            if (e.target !== overlayContent && !overlayContent.contains(e.target) && overlay.style.display != 'none') {
                overlay.style.display = 'none';
            }
        });

        // On ESC key press, hide it too
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                overlay.style.display = 'none';
            }
        });
    } catch (ex) {
        console.log("Error while hooking overlay " + overlay + ": " + ex);
    }
}

// Now do the similar thing for all popups (minus the close button, as popups aren't meant to have close button).
let popups = document.getElementsByClassName('popup');
for (let popup of popups) {
    document.addEventListener("mouseup", function (e) {
        if (e.target !== popup && !popup.contains(e.target) && popup.style.disabled != 'none') {
            popup.style.display = 'none';
        }
    });
}

// Ran when "create new opinion" panel is shown.
document.getElementById('btn-show-opinion-panel').onclick = function () {
    let btnSubmit = document.getElementById('btn-submit-opinion');
    let mdiv = document.createElement("div");
    mdiv.innerHTML = "Send " + getNewSendButtonEntity();
    btnSubmit.value = (mdiv.textContent || mdiv.innerHTML);
    warningOpinion.innerHTML = '';
    document.getElementById('not-a-robot').checked = false;
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
function showReactionPanel(sender, opinionID) {
    let boundsButtonSender = sender.getBoundingClientRect();

    let x = boundsButtonSender.left - boundsReactionPanel.width / 2 + boundsReactionPanel.width / 8 + window.scrollX;
    let y = boundsButtonSender.top - boundsReactionPanel.height - boundsButtonSender.height * 0.2 + window.scrollY;

    reactionPanel.style.left = x + "px";
    reactionPanel.style.top = y + "px";
    reactionPanel.style.display = 'block';

    currentlyReactingToOpinion = opinionID;
}

async function addNewReactionToOpinion(reactionID) {
    let data = {
        'opinion_id': currentlyReactingToOpinion,
        'reaction_id': reactionID
    };

    await fetch('/api/react-to-opinion', {
        method: 'POST',
        body: JSON.stringify(data)
    })
        .then(res => res.json())
        .then(data => {
            let opinion = document.getElementById('opinion-' + currentlyReactingToOpinion);
            const newOpinion = opinionBuilder(data.opinion);
            opinion.replaceWith(newOpinion);
            reactionPanel.style.display = 'none';
        });
}

function increaseExistingOpinionCount(opinionID, reactionID) {
    currentlyReactingToOpinion = opinionID;
    addNewReactionToOpinion(reactionID);
}

function setGETParam(name, value) {
    let get = getGET();
    let entries = Object.entries(get);

    let str = '/?';
    let isSet = false;
    for (let entry of entries) {
        if (entry[0] == name) {
            str += entry[0] + '=' + value;
            isSet = true;
        } else {
            str += entry[0] + '=' + entry[1];
        }

        if (entries.indexOf(entry) + 1 < entries.length) {
            str += "&";
        }
    }

    if (!isSet) {
        str += (str == '/?' ? "" : "&") + name + "=" + value;
    }

    window.history.pushState(null, "Get", str);
}

function changePage(pageNumber) {
    setGETParam('page', pageNumber);
    loadOpinions();
}

let reportPanel = document.getElementById('input-report-panel');
let reportTypes = document.getElementById('report-types');
let warningReport = document.getElementById('warning-report');
let btnSubmitReport = document.getElementById('btn-submit-report');

function showReport(opinionID) {
    reportPanel.style.display = 'block';
    warningReport.style.display = 'none';
    btnSubmitReport.disabled = false;

    // Unselect all options.
    for (let i = 0; i < reportTypes.childElementCount; ++i) {
        reportTypes.children[i].checked = false;
    }

    btnSubmitReport.onclick = function () {
        btnSubmitReport.disabled = true;
        // Get selected item.
        let selected = -1;
        let counter = 0;
        for (let i = 0; i < reportTypes.childElementCount; ++i) {
            if (reportTypes.children[i].type == 'radio') {
                if (reportTypes.children[i].checked) {
                    selected = counter;
                    break;
                }
                counter++;
            }
        }

        // None selected? Show warning.
        if (selected == -1) {
            warningReport.innerHTML = "Select report type first.";
            warningReport.style.display = 'block';
            return;
        }

        let data = {
            'opinion_id': opinionID,
            'report_type': selected
        };

        let response;
        fetch('/api/report-opinion', {
            method: 'POST',
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(data => {
                if (data.error_message != null) {
                    // Something went wrong.
                    warningReport.innerHTML = "Something went wrong while reporting the opinion:<br>" + response.error_message;
                    warningReport.style.display = 'block';
                } else {
                    createAlert(data.message);
                    reportPanel.style.display = 'none';
                }
            });
    }
}

function createAlert(mainText) {
    let popup = document.createElement('div');
    popup.classList.add('popup');
    popup.classList.add('dismisable');
    popup.classList.add('popup-success');

    let buttonClose = document.createElement('button');
    buttonClose.classList.add('btn-overlay-close');
    buttonClose.innerHTML = 'X';
    buttonClose.onclick = function () {
        popup.remove();
    };
    popup.appendChild(buttonClose);

    let header = document.createElement('header');
    header.innerHTML = 'Alert';
    popup.appendChild(header);

    let main = document.createElement('main');
    main.innerHTML = mainText;
    popup.appendChild(main);

    document.body.prepend(popup);
}

document.getElementById('btn-submit-opinion').onclick = async function () {
    if (!document.getElementById('not-a-robot').checked) {
        warningOpinion.innerHTML = 'Confirm that you are not a robot.';
        warningOpinion.style.display = 'block';
        return;
    }

    let title = titleInput.value;
    let content = contentInput.value;

    let data = {
        "title": title,
        "content": content
    };

    await fetch('/api/send-opinion', {
        method: 'POST',
        body: JSON.stringify(data)
    })
        .then(res => res.json())
        .then(data => {
            if (data.error_message != null) {
                // Something went wrong.
                warningOpinion.innerHTML = "Something went wrong while sending the opinion:<br>" + data.error_message;
                warningOpinion.style.display = 'block';
            } else {
                createAlert(data.message + "<br><br>Come back tomorrow for the next topic!");
                cleanOpinionPanelForm();
                opinionPanel.style.display = 'none';
                loadOpinions();
            }
        });
}

async function loadOpinions(doNotScrollToTop = false) {
    let parent = document.getElementById('opinions');

    let sortByNew = document.getElementById('sort-by-new').checked;
    let apiRequest = sortByNew ? "/api/opinions-new" : "/api/opinions";

    let pageCount = getGET()["page"];
    if (pageCount != undefined) {
        apiRequest += "?page=" + pageCount;
    } else {
        pageCount = 1;
    }

    let response;
    await fetch(apiRequest, {
        method: 'GET'
    })
        .then(res => res.json())
        .then(data => {
            response = data;
        });

    parent.innerHTML = '';

    document.getElementById('topic').innerHTML = response.topic.name;

    if (response.opinions.length == 0) {
        // Show 'no opinions' message.
        let opinion = document.createElement('article');
        opinion.classList.add('opinion');

        let header = document.createElement('header');
        header.innerHTML = 'No opinions on that topic just yet!<p class="emoji">&#128576;</p>';
        opinion.appendChild(header);

        let main = document.createElement('main');
        main.innerHTML = 'Time to create a new one?';
        opinion.appendChild(main);

        parent.appendChild(opinion);
    } else {
        for (const element of response.opinions) {
            parent.appendChild(opinionBuilder(element));
        }
    }

    // And now, load pages button...
    let pages = document.getElementById('pages');
    pages.innerHTML = '';
    for (let i = 1; i <= response.pages; ++i) {
        let pageButton = document.createElement('button');
        pageButton.classList.add(pageCount == i ? 'btn' : 'btn-secondary');
        pageButton.innerHTML = i;
        pageButton.onclick = function () { changePage(i); };

        pages.appendChild(pageButton);
    }

    if (!doNotScrollToTop) {
        document.body.scrollTop = document.documentElement.scrollTop = 0;
    }
}

function getGET() {
    var get = [];
    window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
        function (a, name, value) {
            get[name] = value;
        });
    return get;
}

/** Creates a new opinion object from API response. */
function opinionBuilder(element) {
    let opinionObject = element;
    let opinion = document.createElement('article');
    opinion.classList.add('opinion');
    opinion.id = "opinion-" + opinionObject.id;

    let header = document.createElement('header');
    header.innerHTML = opinionObject.title;
    opinion.appendChild(header);

    let main = document.createElement('main');
    main.innerHTML = opinionObject.content;
    opinion.appendChild(main);

    let reactions = document.createElement('section');
    reactions.classList.add('reactions');
    opinion.appendChild(reactions);

    // Add reactions
    for (const reaction of element.reactions) {
        let reactionBtn = document.createElement('button');
        reactionBtn.classList.add('reaction');
        reactionBtn.onclick = function () { increaseExistingOpinionCount(opinionObject.id, reaction.reaction_entity.id); };
        reactions.appendChild(reactionBtn);

        let emoji = document.createElement('p');
        emoji.classList.add('emoji');
        emoji.innerHTML = ' ' + reaction.reaction_entity.htmlEntity + ' ';
        reactionBtn.appendChild(emoji);

        let count = document.createElement('p');
        count.innerHTML = ' ' + reaction.count;
        reactionBtn.appendChild(count);
    }

    // Add new reaction button
    let addNewReaction = document.createElement('button');
    addNewReaction.classList.add('reaction');
    addNewReaction.classList.add('btn-secondary');
    addNewReaction.id = "button-add-reaction-" + opinionObject.id;
    addNewReaction.onclick = function () { showReactionPanel(addNewReaction, opinionObject.id); };
    addNewReaction.innerHTML = "+";
    reactions.appendChild(addNewReaction);

    // Finally, report button.
    let report = document.createElement('a');
    report.classList.add('report-issue');
    report.onclick = function () { showReport(opinionObject.id); };
    report.innerHTML = 'Report...';
    opinion.appendChild(report);

    return opinion;
}

loadOpinions();

/** Load reaction entities. */
async function loadReactions() {
    let reactionsParent = document.getElementById('reactions');
    await fetch('/api/reaction-entities', {
        method: 'GET'
    })
        .then(res => res.json())
        .then(data => {
            for (const entry of data) {
                let btnReaction = document.createElement('button');
                btnReaction.classList.add('reaction');
                btnReaction.classList.add('emoji');
                btnReaction.onclick = function () { addNewReactionToOpinion(entry.id); }
                btnReaction.innerHTML = entry.htmlEntity;

                reactionsParent.appendChild(btnReaction);
            }
        });
}

loadReactions();

/** Load all available report types. */
async function loadReportTypes() {
    await fetch('/api/report-types', {
        method: 'GET'
    })
        .then(res => res.json())
        .then(data => {
            let reportsParent = document.getElementById('report-types');
            for (const entry of data) {
                let lblReport = document.createElement('label');
                lblReport.htmlFor = 'report-type-' + data.indexOf(entry);
                lblReport.innerHTML = entry;

                let inputReport = document.createElement('input');
                inputReport.type = 'radio';
                inputReport.id = 'report-type-' + data.indexOf(entry);
                inputReport.name = 'reportType';
                inputReport.value = entry;
                reportsParent.appendChild(inputReport);

                reportsParent.appendChild(lblReport);
            }
        });
}

loadReportTypes();

// Sorting option
if (getGET()["sortby"] == 'new') {
    document.getElementById('sort-by-new').checked = true;
} else {
    document.getElementById('sort-by-popular').checked = true;
}

document.getElementById('sort-by-new').onclick = function () {
    setGETParam('sortby', 'new');
    setGETParam('page', 1);
    loadOpinions();
}
document.getElementById('sort-by-popular').onclick = function () {
    setGETParam('sortby', 'popular');
    setGETParam('page', 1);
    loadOpinions();
}