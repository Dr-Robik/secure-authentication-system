import { initRouter } from "./utils/router";

import { loadUser } from "./store/authStore";

async function bootstrap() {

    try {

        const token =
            localStorage.getItem("token");

        if (token) {

            await loadUser();
        }

    } catch (err) {

        console.error(
            "User loading failed:",
            err
        );
    }

    initRouter();
}

document.addEventListener(
    "DOMContentLoaded",
    bootstrap
);