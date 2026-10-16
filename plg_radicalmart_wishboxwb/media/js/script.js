"use strict";

document.addEventListener("DOMContentLoaded", () => {
	document.querySelectorAll("[data-radicalmart-wishboxwb-form]").forEach((button) => {
		button.addEventListener("click", () => {
			const targetForm = document.getElementById(button.dataset.radicalmartWishboxwbForm);

			if (!targetForm) {
				return;
			}

			if (button.hasAttribute("data-radicalmart-wishboxwb-copy-selection")) {
				targetForm.querySelectorAll('input[name="cid[]"]').forEach((input) => input.remove());

				document.querySelectorAll('#adminForm input[name="cid[]"]:checked').forEach((checkbox) => {
					const input = document.createElement("input");
					input.type = "hidden";
					input.name = "cid[]";
					input.value = checkbox.value;
					targetForm.appendChild(input);
				});
			}

			targetForm.requestSubmit();
		});
	});
});
