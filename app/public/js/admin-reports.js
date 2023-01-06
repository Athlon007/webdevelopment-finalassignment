function dismissReport(opinionID) {
    formBuilder("POST", "/admin/reports", {
        "action": "dismiss-report",
        "opinion-id": opinionID
    });
}

function deleteOpinion(opinionID) {
    formBuilder("POST", "/admin/reports", {
        "action": "delete-opinion",
        "opinion-id": opinionID
    });
}