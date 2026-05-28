import axios from "axios";
import { defineStore } from "pinia";
export const useConfiguracionStore = defineStore("configuracion", {
    state: () => ({
        oConfiguracion: {
            sistema: "SISATC",
            alias: "SISATC",
            actividad: "ACTIVIDAD SISATC",
            url_logo: "",
        },
    }),
    actions: {
        initConfiguracion() {
            axios
                .get(route("configuracions.getConfiguracion"))
                .then((response) => {
                    this.setConfiguracion(response.data.configuracion);
                })
                .catch((error) => {
                    console.log("Error al cargar la configuración");
                })
                .finally(() => {
                    console.log("Configuración cargada");
                });
        },
        setConfiguracion(value) {
            this.oConfiguracion = { ...value };
        },
    },
    getters: {
        getConfiguracion() {
            return this.oConfiguracion;
        },
    },
});
