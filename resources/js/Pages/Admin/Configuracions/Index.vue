<script setup>
import App from "@/Layouts/App.vue";
defineOptions({
    layout: App,
});
import Content from "@/Components/Content.vue";
import { onMounted, ref, computed, onBeforeMount } from "vue";
import { usePage, Head, useForm, Link } from "@inertiajs/vue3";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useConfiguracionStore } from "@/stores/configuracion/configuracionStore";
const appStore = useAppStore();
const configuracionStore = useConfiguracionStore();

onBeforeMount(() => {
    appStore.startLoading();
});

const props_page = defineProps({
    configuracion: {
        type: Object,
        default: null,
    },
});
const { props } = usePage();
const enviando = ref(false);

let form = null;
if (props_page.configuracion != null) {
    props_page.configuracion["_method"] = "put";
    form = useForm(props_page.configuracion);
} else {
    form = useForm({
        _method: "put",
        id: 0,
        nombre_sistema: "",
        alias: "",
        url_logo: "",
        logo: "",
    });
}

const enviarFormulario = () => {
    enviando.value = true;
    form.post(route("configuracions.update", form.id), {
        onSuccess: (response) => {
            // Mostrar mensaje de éxito
            console.log("correcto");
            const success =
                response.props.flash.success ?? "Proceso realizado con éxito";
            limpiaRefs();
            Swal.fire({
                icon: "success",
                title: "Correcto",
                html: `<strong>${success}</strong>`,
                showCancelButton: false,
                confirmButtonText: "Aceptar",
                customClass: {
                    confirmButton: "btn-alert-success",
                },
            });
        },
        onError: (err, code) => {
            console.log(code ?? "");
            const error =
                "Ocurrió un error inesperado contactese con el Administrador";
            Swal.fire({
                icon: "error",
                title: "Error",
                html: `<strong>${error}</strong>`,
                showCancelButton: false,
                confirmButtonText: "Aceptar",
                customClass: {
                    confirmButton: "btn-error",
                },
            });
            console.log("error: " + err.error);
        },
        onFinish: () => {
            enviando.value = false;
            configuracionStore.initConfiguracion();
        },
    });
};
const logo = ref(null);
function cargaArchivo(e, key) {
    form[key] = null;
    form[key] = e.target.files[0];

    // Generar la URL del archivo cargado
    const fileUrl = URL.createObjectURL(form[key]);
    form["url_" + key] = fileUrl;
}
function limpiaRefs() {
    logo.value = null;
}

const btnGuardar = computed(() => {
    if (enviando.value) {
        return `Guardando... <i class="fa fa-spinner fa-spin"></i>`;
    }

    return `Guardar cambios <i class="fa fa-save"></i>`;
});

onMounted(() => {
    appStore.stopLoading();
});
</script>
<template>
    <Head title="Configuración"></Head>
    <Content>
        <template #header>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 class="m-0"><i class="fa fa-cog"></i> Configuración</h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">Configuración</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>
        <form @submit.prevent="enviarFormulario()">
            <div class="row">
                <h5><i class="fa fa-list"></i> Datos del Sistema</h5>
                <div class="col-md-4 form-group mb-3">
                    <label class="required">Nombre del sistema</label>
                    <input
                        type="text"
                        class="form-control"
                        v-model="form.nombre_sistema"
                    />
                    <span
                        class="text-danger"
                        v-if="form.errors?.nombre_sistema"
                        >{{ form.errors.nombre_sistema }}</span
                    >
                </div>
                <div class="col-md-4 form-group mb-3">
                    <label class="required">Alias</label>
                    <input
                        type="text"
                        class="form-control"
                        v-model="form.alias"
                    />
                    <span class="text-danger" v-if="form.errors?.alias">{{
                        form.errors.alias
                    }}</span>
                </div>
                <div class="col-md-4 form-group mb-3">
                    <label class="required">Logo</label>
                    <input
                        type="file"
                        class="form-control"
                        @change="cargaArchivo($event, 'logo')"
                        ref="logo"
                    />
                    <div class="logo_muestra w-100 text-center">
                        <img
                            :src="form.url_logo"
                            alt=""
                            v-if="form.url_logo"
                            width="50%"
                        />
                    </div>
                    <span class="text-danger" v-if="form.errors?.logo">{{
                        form.errors.logo
                    }}</span>
                </div>
            </div>
            <div class="row pb-5">
                <div
                    class="col-12"
                    v-if="
                        props.auth.user.permisos == '*' ||
                        props.auth.user.permisos.includes('configuracions.edit')
                    "
                >
                    <button
                        type="submit"
                        class="btn btn-primary"
                        v-html="btnGuardar"
                        :disabled="enviando"
                    ></button>
                </div>
            </div>
        </form>
    </Content>
</template>
<style scoped>
.logo_muestra {
    margin-top: 10px;
    width: 100%;
    text-align: center;
}
.logo_muestra img {
    max-width: 100%;
}
</style>
