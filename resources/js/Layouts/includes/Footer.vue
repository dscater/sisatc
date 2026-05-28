<script setup>
import { onMounted, onBeforeUnmount } from "vue";
import { useConfiguracionStore } from "@/stores/configuracion/configuracionStore";
const configuracionStore = useConfiguracionStore();
onMounted(() => {
    document.getElementsByTagName("body")[0].classList.add("login");
});
onBeforeUnmount(() => {
    document.getElementsByTagName("body")[0].classList.remove("login");
});
</script>

<template>
    <!-- Main Footer -->
    <footer class="app-footer">
        <!-- To the right -->
        <!-- <div class="float-right d-none d-sm-inline">
            <a href="https://github.com/dscater" target="_blank"
                >Desarrollado por Dscater</a
            >
        </div> -->
        <!-- Default to the left -->
        <strong
            >Copyright &copy; {{ new Date().getFullYear() }}
            <a href="/" class="text-principal">{{
                configuracionStore.oConfiguracion.nombre_sistema
            }}</a
            >.</strong
        >
        Todos los derechos reservados.
    </footer>
</template>
