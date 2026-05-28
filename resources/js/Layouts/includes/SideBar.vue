<script setup>
import { onMounted, onUnmounted, ref, nextTick, reactive } from "vue";
import { router, usePage, Link } from "@inertiajs/vue3";
import ItemMenu from "@/Components/ItemMenu.vue";
import { useSideBar } from "@/composables/useSidebar.js";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useConfiguracionStore } from "@/stores/configuracion/configuracionStore";
const { closeSidebar, toggleSubMenuELem } = useSideBar();
const { auth } = usePage().props;
const configuracionStore = useConfiguracionStore();
const appStore = useAppStore();
const usuario = ref(null);
const permisos = ref([]);
const route_current = ref("");

const toggleSubMenu = (menu) => {
    openMenus[menu] = !openMenus[menu];
};

const sincronizarMenus = () => {
    Object.keys(openMenus).forEach((key) => {
        openMenus[key] = false;
    });

    if (
        route_current.value == "usuarios.index" ||
        route_current.value == "roles.index"
    ) {
        openMenus.usuarios = true;
    }

    if (
        route_current.value == "enfermedads.index" ||
        route_current.value == "categoria_enfermedads.index" ||
        route_current.value == "tipo_transmisions.index"
    ) {
        openMenus.enfermedads = true;
    }
};

const openMenus = reactive({
    usuarios: false,
    enfermedads: false,
});

router.on("navigate", (event) => {
    route_current.value = route().current();
    sincronizarMenus();
    closeSidebar();
});

onMounted(() => {
    usuario.value = appStore.getUsuario;
    permisos.value = auth.user.permisos;
    route_current.value = route().current();
    sincronizarMenus();
});

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

onUnmounted(() => {});
</script>
<template>
    <!-- Main Sidebar Container -->
    <aside class="app-sidebar shadow bg-white">
        <!-- Brand Logo -->
        <div class="sidebar-brand bg1">
            <a
                :href="route('inicio')"
                class="brand-link d-flex justify-content-center align-items-center py-0"
            >
                <img
                    :src="configuracionStore.oConfiguracion.url_logo"
                    alt="Logo"
                    class="rounded-circle"
                    style="max-height: 51px"
                />
                <span class="brand-text font-weight-600 ml-1 text-white">{{
                    configuracionStore.oConfiguracion.nombre_sistema
                }}</span>
            </a>
        </div>

        <!-- Sidebar -->
        <div class="sidebar-wrapper">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-2 d-flex border-bottom">
                <div class="image">
                    <img
                        :src="usuario?.url_foto"
                        class="rounded-circle elevation-2 user-image"
                        alt="User Image"
                    />
                </div>
                <div class="info">
                    <Link
                        :href="route('profile.edit')"
                        class="d-block text-decoration-none"
                    >
                        <div class="nombre">
                            {{ usuario?.nombre }} {{ usuario?.paterno }}
                            {{ usuario?.materno }}
                        </div>
                        <div class="tipo">{{ usuario?.tipo }}</div>
                    </Link>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul
                    class="nav sidebar-menu flex-column"
                    data-lte-toggle="treeview"
                    role="navigation"
                    aria-label="Main navigation"
                    data-accordion="false"
                    id="navigation"
                >
                    <ItemMenu
                        :label="'Inicio'"
                        :ruta="'inicio'"
                        :icon="'fa fa-home'"
                    ></ItemMenu>
                    <li
                        class="nav-header font-weight-bold"
                        v-if="
                            permisos == '*' ||
                            permisos.includes('usuarios.index') ||
                            permisos.includes(
                                'ejecucion_trazabilidads.index',
                            ) ||
                            permisos.includes('incidencias.index') ||
                            permisos.includes('entrenamientos.index') ||
                            permisos.includes('activos.index') ||
                            permisos.includes('tipo_activos.index')
                        "
                    >
                        ADMINISTRACIÓN
                    </li>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('ejecucion_trazabilidads.index')
                        "
                        :label="'Ejecución y Trazabilidad'"
                        :ruta="'ejecucion_trazabilidads.index'"
                        :icon="'fa fa-table'"
                    ></ItemMenu>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('incidencias.index')
                        "
                        :label="'Gestión de Incidencias'"
                        :ruta="'incidencias.index'"
                        :icon="'fa fa-list'"
                    ></ItemMenu>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('entrenamientos.index')
                        "
                        :label="'Motor de Recomendación IA'"
                        :ruta="'entrenamientos.index'"
                        :icon="'fa fa-robot'"
                    ></ItemMenu>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('activos.index')
                        "
                        :label="'Activos y Configuración'"
                        :ruta="'activos.index'"
                        :icon="'fa fa-clipboard-list'"
                    ></ItemMenu>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('tipo_activos.index')
                        "
                        :label="'Tipos de Activos'"
                        :ruta="'tipo_activos.index'"
                        :icon="'fa fa-clipboard-list'"
                    ></ItemMenu>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('usuarios.index')
                        "
                        :label="'Usuarios'"
                        :ruta="'usuarios.index'"
                        :icon="'fa fa-users'"
                    ></ItemMenu>
                    <li
                        class="nav-header font-weight-bold"
                        v-if="
                            permisos == '*' ||
                            permisos.includes('reportes.usuarios') ||
                            permisos.includes('reportes.productos') ||
                            permisos.includes(
                                'reportes.movimiento_inventarios',
                            ) ||
                            permisos.includes('reportes.liquidacion')
                        "
                    >
                        REPORTES
                    </li>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('reportes.usuarios')
                        "
                        :label="'Lista de Usuarios'"
                        :ruta="'reportes.usuarios'"
                        :icon="'fa fa-file-pdf'"
                    ></ItemMenu>

                    <li class="nav-header font-weight-bold">OTROS</li>
                    <ItemMenu
                        v-if="
                            permisos == '*' ||
                            permisos.includes('configuracions.index')
                        "
                        :label="'Configuración Sistema'"
                        :ruta="'configuracions.index'"
                        :icon="'fa fa-cog'"
                    ></ItemMenu>
                    <ItemMenu
                        :label="'Perfil'"
                        :ruta="'profile.edit'"
                        :icon="'fa fa-id-card'"
                    ></ItemMenu>
                    <li class="nav-item">
                        <a
                            href="#"
                            class="nav-link"
                            @click.prevent="salir()"
                            ref="link"
                        >
                            <i class="nav-icon fa fa-power-off"></i>
                            <p>Salir</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</template>
<style scoped></style>
