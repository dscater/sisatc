<script setup>
// includes
import { ref, onMounted, onBeforeMount, nextTick } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import Footer from "./includes/Footer.vue";
import NavBar from "./includes/NavBar.vue";
import SideBar from "./includes/SideBar.vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useConfiguracionStore } from "@/stores/configuracion/configuracionStore";
const configuracionStore = useConfiguracionStore();
const appStore = useAppStore();
const { auth } = usePage().props;

let inactivityTimer = null;
// const INACTIVITY_LIMIT = 60 * 1000; // 1 minuto en ms
let INACTIVITY_LIMIT = 60 * 5000; // 5 minuto en ms

// Función para cerrar sesión
const logout = () => {
    axios
        .post(route("logout"))
        .then((response) => {})
        .finally(() => {
            window.location.href = "/";
        });
};

// Resetear temporizador
const resetTimer = () => {
    if (inactivityTimer) clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(logout, INACTIVITY_LIMIT);
};

onMounted(async () => {
    // Inicializar info de usuario
    appStore.initUserInfo();

    // await nextTick();

    // // reinicializa treeview
    // document.querySelectorAll('[data-lte-toggle="treeview"]');

    // Eventos que consideran actividad del usuario
    // window.addEventListener("mousemove", resetTimer);
    // window.addEventListener("keydown", resetTimer);
    // window.addEventListener("click", resetTimer);

    // resetTimer();
});

onBeforeMount(async () => {
    configuracionStore.initConfiguracion();
    appStore.initUserInfo();

    await nextTick();

    window.dispatchEvent(new Event("load"));

    // window.removeEventListener("mousemove", resetTimer);
    // window.removeEventListener("keydown", resetTimer);
    // window.removeEventListener("click", resetTimer);
    // if (inactivityTimer) clearTimeout(inactivityTimer);
});
</script>
<template>
    <div class="loading" :class="[appStore.getLoading == true ? 'show' : '']">
        <!-- <div class="loading show"> -->
        <template v-if="$slots.loading">
            <slot name="loading"></slot>
        </template>
        <template v-else>
            <i class="fa fa-spin fa-spinner fa-4x text1"></i>
            <br />
            <span class="font-weight-bold text1 h4">
                {{ configuracionStore.oConfiguracion.nombre_sistema }}
            </span>
        </template>
    </div>
    <div class="app-wrapper" v-if="auth.user.tipo != 'POSTULANTE'">
        <NavBar></NavBar>
        <SideBar></SideBar>
        <slot />
        <Footer></Footer>
    </div>
    <div v-else>
        <slot />
    </div>
</template>
