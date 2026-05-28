import { defineStore } from "pinia";

export const useMenuStore = defineStore("menu", {
    state: () => ({
        url_actual: "/",
    }),
    actions: {
        setMobile(value) {
        },
        setMenuOpen(value) {
        },
    },
});
