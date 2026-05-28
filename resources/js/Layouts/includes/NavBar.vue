<script setup>
// Composables
import { usePage, Link } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref } from "vue";
import { useSideBar } from "@/composables/useSidebar.js";
import { useConfiguracionStore } from "@/stores/configuracion/configuracionStore";
const configuracionStore = useConfiguracionStore();
const muestra_login_user = ref(false);
const accion_login_user = ref(0);

const { props } = usePage();
const permisos = ref([]);
const { toggleSidebar } = useSideBar();

const salir = () => {
    Swal.fire({
        icon: "question",
        title: "Cerrar sesión",
        html: `¿Esta seguro(a) de cerrar sesión?`,
        showCancelButton: true,
        confirmButtonText: "Si, salir",
        cancelButtonText: "Cancelar",
        denyButtonText: `Cancelar`,
        customClass: {
            confirmButton: "btn-success",
        },
    }).then(async (result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            axios
                .post(route("logout"))
                .then((response) => {})
                .finally(() => {
                    window.location.href = "/";
                });
        }
    });
};
const recargarPagina = () => {
    window.location.reload();
};

onMounted(() => {
    permisos.value = props.auth.user.permisos;
});

onUnmounted(() => {});
</script>
<template>
    <!-- Navbar -->
    <nav class="app-header navbar navbar-expand navbar-dark bg-principal">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a
                    class="nav-link toggleButton"
                    data-lte-toggle="sidebar"
                    href="#"
                    role="button"
                    @click="toggleSidebar"
                    ><i class="fas fa-bars"></i
                ></a>
            </li>
            <!-- <li
                class="nav-item d-sm-inline-block"
                v-if="permisos == '*' || permisos.includes('pedidos.index')"
            >
                <Link :href="route('pedidos.index')" class="nav-link"
                    >Pedidos</Link
                >
            </li> -->
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item d-sm-none">
                <a href="#" class="nav-link" @click="recargarPagina"
                    ><i class="fa fa-sync"></i
                ></a>
            </li>
            <li class="nav-item">
                <a
                    class="nav-link"
                    data-widget="fullscreen"
                    href="#"
                    role="button"
                    @click.prevent="salir"
                >
                    <i class="fas fa-power-off"></i>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->
</template>
