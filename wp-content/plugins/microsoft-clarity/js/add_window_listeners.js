const MessageOperation = {
	PROJECT_ID_CHANGE: 1,
};

const isValidProjectId = (id) => {
    if (id === null || id === undefined || typeof id !== 'string') {
        return false;
    }
    const pattern = /^[a-zA-Z0-9]*$/;
    return pattern.test(id);
}
  
const projectActionCallback = (event) => {
	if (event.origin !== "https://clarity.microsoft.com") return;
	const postedMessage = event?.data;
	if (
        postedMessage?.operation !== MessageOperation.PROJECT_ID_CHANGE ||
		!isValidProjectId(postedMessage?.id)
        ) {
            return;
        }
	const isRemoveRequest = postedMessage?.id === "";
	jQuery
		.ajax({
			method: "POST",
			url: ajaxurl,
			data: {
				action: "edit_clarity_project_id",
				new_value: isRemoveRequest ? "" : postedMessage?.id,
				user_must_be_admin: postedMessage?.userMustBeAdmin,
                nonce: postedMessage?.nonce,
			},
			dataType: "json",
		})
		.done(function (json) {
			if (!json.success) {
				console.log(
					`Failed to ${isRemoveRequest ? "remove" : "add"} Clarity snippet${
						isRemoveRequest ? "." : ` for project ${postedMessage?.id}.`
					}`
				);
			} else {
				console.log(
					`${isRemoveRequest ? "Removed" : "Added"} Clarity snippet${
						isRemoveRequest ? "." : ` for project ${postedMessage?.id}.`
					}`
				);
			}
		})
		.fail(function () {
			console.log(
				`Failed to ${isRemoveRequest ? "remove" : "add"} Clarity snippet${
					isRemoveRequest ? "." : ` for project ${postedMessage?.id}.`
				}`
			);
		});
};

window.addEventListener("message", projectActionCallback, false);
