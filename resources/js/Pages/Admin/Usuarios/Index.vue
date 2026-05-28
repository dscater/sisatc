<script setup>
import Content from "@/Components/Content.vue";
import MiTable from "@/Components/MiTable.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { useUsuarios } from "@/composables/usuarios/useUsuarios";
import { ref, onMounted, onBeforeMount } from "vue";
import Formulario from "./Formulario.vue";
import FormPassword from "./FormPassword.vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useAxios } from "@/composables/axios/useAxios";
const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

onBeforeMount(() => {
    appStore.startLoading();
});

const { setUsuario, limpiarUsuario, form } = useUsuarios();

const miTable = ref(null);
const headers = [
    {
        label: "",
        key: "id",
        sortable: true,
        width: "3%",
    },
    {
        label: "NOMBRE COMPLETO",
        key: "full_name",
        sortable: true,
    },
    {
        label: "EMAIL",
        key: "email",
        sortable: true,
    },
    {
        label: "TELÉFONO/CELULAR",
        key: "fono",
        sortable: true,
    },
    {
        label: "TIPO",
        key: "tipo",
        sortable: true,
    },
    {
        label: "FOTO",
        key: "foto",
        sortable: true,
    },
    {
        label: "ACCESO",
        key: "acceso",
        sortable: true,
    },
    {
        label: "ACCIÓN",
        key: "accion",
        fixed: "right",
        width: "4%",
    },
];

const multiSearch = ref({
    search: "",
    filtro: [],
});

const muestra_formulario = ref(false);
const muestra_formulario_pass = ref(false);

const agregarRegistro = () => {
    limpiarUsuario();
    muestra_formulario.value = true;
};

const updateDatatable = async () => {
    if (miTable.value) {
        await miTable.value.cargarDatos();
        limpiarUsuario();
        muestra_formulario.value = false;
    }
};

const eliminarUsuario = (item) => {
    Swal.fire({
        title: "¿Quierés eliminar este registro?",
        html: `<strong>${item.usuario}</strong>`,
        showCancelButton: true,
        confirmButtonText: "Si, eliminar",
        cancelButtonText: "No, cancelar",
        denyButtonText: `No, cancelar`,
        customClass: {
            confirmButton: "btn-danger",
        },
    }).then(async (result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            let respuesta = await axiosDelete(
                route("usuarios.destroy", item.id),
            );
            if (respuesta && respuesta.sw) {
                updateDatatable();
            }
        }
    });
};

onMounted(async () => {
    appStore.stopLoading();
});
</script>
<template>
    <Head title="Usuarios"></Head>

    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0"><i class="fa fa-users"></i> Usuarios</h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">Usuarios</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <button
                            v-if="
                                props_page.auth?.user.permisos == '*' ||
                                props_page.auth?.user.permisos.includes(
                                    'usuarios.create',
                                )
                            "
                            type="button"
                            class="btn btn-primary text-sm"
                            @click="agregarRegistro"
                        >
                            <i class="fa fa-plus"></i> Nuevo Usuario
                        </button>
                    </div>
                    <div class="col-md-8 my-1">
                        <div class="row justify-content-end">
                            <div class="col-md-5">
                                <div
                                    class="input-group"
                                    style="align-items: end"
                                >
                                    <input
                                        v-model="multiSearch.search"
                                        placeholder="Buscar"
                                        class="form-control border-1 border-right-0"
                                    />
                                    <div class="input-append">
                                        <button
                                            class="btn btn-default rounded-0 border-left-0"
                                            @click="updateDatos"
                                        >
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <MiTable
                            :tableClass="'bg-white mitabla'"
                            ref="miTable"
                            :cols="headers"
                            :api="true"
                            :url="route('usuarios.paginado')"
                            :numPages="5"
                            :multiSearch="multiSearch"
                            :syncOrderBy="'id'"
                            :syncOrderAsc="'DESC'"
                            table-responsive
                            :header-class="'bg__primary'"
                            fixed-header
                        >
                            <template #foto="{ item }">
                                <img
                                    class="direct-chat-img"
                                    :src="item.url_foto"
                                    alt="Foto"
                                />
                            </template>

                            <template #acceso="{ item }">
                                <div
                                    class="badge text-sm"
                                    :class="[
                                        item.acceso == 1
                                            ? 'bg-success'
                                            : 'bg-danger',
                                    ]"
                                >
                                    {{
                                        item.acceso == 1
                                            ? "HABILITADO"
                                            : "DESHABILITADO"
                                    }}
                                </div>
                            </template>

                            <template #bloqueo="{ item }">
                                <div
                                    class="badge text-sm"
                                    :class="[
                                        item.bloqueo == 1
                                            ? 'bg-success'
                                            : 'bg-danger',
                                    ]"
                                >
                                    {{
                                        item.bloqueo == 1
                                            ? "HABILITADO"
                                            : "DESHABILITADO"
                                    }}
                                </div>
                            </template>
                            <template #accion="{ item }">
                                <template
                                    v-if="
                                        props_page.auth?.user.permisos == '*' ||
                                        props_page.auth?.user.permisos.includes(
                                            'usuarios.password',
                                        )
                                    "
                                >
                                    <el-tooltip
                                        class="box-item"
                                        effect="dark"
                                        content="Cambiar contraseña"
                                        placement="left-start"
                                    >
                                        <button
                                            class="btn btn-info"
                                            @click="
                                                setUsuario(item);
                                                muestra_formulario_pass = true;
                                            "
                                        >
                                            <i class="fa fa-key"></i></button
                                    ></el-tooltip>
                                </template>
                                <template
                                    v-if="
                                        props_page.auth?.user.permisos == '*' ||
                                        props_page.auth?.user.permisos.includes(
                                            'usuarios.edit',
                                        )
                                    "
                                >
                                    <el-tooltip
                                        class="box-item"
                                        effect="dark"
                                        content="Editar"
                                        placement="left-start"
                                    >
                                        <button
                                            class="btn btn-warning"
                                            @click="
                                                setUsuario(item);
                                                muestra_formulario = true;
                                            "
                                        >
                                            <i class="fa fa-pen"></i></button
                                    ></el-tooltip>
                                </template>
                                <template
                                    v-if="
                                        props_page.auth?.user.permisos == '*' ||
                                        props_page.auth?.user.permisos.includes(
                                            'usuarios.destroy',
                                        )
                                    "
                                >
                                    <el-tooltip
                                        class="box-item"
                                        effect="dark"
                                        content="Eliminar"
                                        placement="left-start"
                                    >
                                        <button
                                            class="btn btn-danger"
                                            @click="eliminarUsuario(item)"
                                        >
                                            <i
                                                class="fa fa-trash-alt"
                                            ></i></button
                                    ></el-tooltip>
                                </template>
                            </template>
                        </MiTable>
                    </div>
                </div>
            </div>
        </div>
    </Content>

    <Formulario
        v-if="muestra_formulario"
        :muestra_formulario="muestra_formulario"
        :form="form"
        @envio-formulario="updateDatatable"
        @cerrar-formulario="muestra_formulario = false"
    ></Formulario>
    <FormPassword
        v-if="muestra_formulario_pass"
        :muestra_formulario="muestra_formulario_pass"
        :formUser="form"
        @envio-formulario="muestra_formulario_pass = false"
        @cerrar-formulario="muestra_formulario_pass = false"
    ></FormPassword>
</template>
