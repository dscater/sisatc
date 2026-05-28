<script setup>
import { ref, onMounted, onBeforeMount } from "vue";
import { router, useForm, usePage, Head, Link } from "@inertiajs/vue3";
import { useUser } from "@/composables/useUser";
import Content from "@/Components/Content.vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
const appStore = useAppStore();

onBeforeMount(() => {
    appStore.startLoading();
});

onMounted(() => {
    appStore.stopLoading();
});

const props = defineProps({
    user: {
        type: Object,
    },
});

const url_aux = ref(props.user.url_foto);

const foto = ref(null);
const imagen_cargada = ref(false);

function cargaImagen(e) {
    foto.value = e.target.files[0];
    props.user.url_foto = URL.createObjectURL(foto.value);
    imagen_cargada.value = true;
}

const { getUser } = useUser();

function guardarImagen() {
    router.post(
        route("profile.update_foto"),
        { foto: foto.value, _method: "patch" },
        {
            forceFormData: true,
            onSuccess: () => {
                getUser();
                Swal.fire({
                    icon: "success",
                    title: "Correcto",
                    text: `${flash.bien ? flash.bien : "Proceso realizado"}`,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: `Aceptar`,
                });
                imagen_cargada.value = false;
            },
            onError: (err) => {
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    text: `${
                        flash.error
                            ? flash.error
                            : "Tienes errores en el formulario"
                    }`,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: `Aceptar`,
                });
                console.log(err);
            },
        },
    );
}

function cancelarImagen() {
    imagen_cargada.value = false;
    props.user.url_foto = url_aux.value;
}

const form = useForm({
    password_actual: "",
    password: "",
    password_confirmation: "",
    _method: "patch",
});

const { flash } = usePage().props;

const enviaFormulario = () => {
    form.errors = {};
    form.post(route("profile.update"), {
        preserveScroll: true,
        onSuccess: () => {
            form.clearErrors();
            Swal.fire({
                icon: "success",
                title: "Correcto",
                text: `${flash.bien ? flash.bien : "Proceso realizado"}`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            form.reset();
        },
        onError: (err) => {
            Swal.fire({
                icon: "info",
                title: "Error",
                text: `${
                    flash.error
                        ? flash.error
                        : "Tienes errores en el formulario"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            console.log(err);
        },
    });
};
</script>

<template>
    <Head title="Perfil"></Head>

    <Content>
        <template #header>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Perfil</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">Perfil</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="info_foto w-100 text-center">
                                <img class="image" :src="user.url_foto" />
                                <h4 class="mt-1 mb-1">
                                    {{ user.tipo }}
                                </h4>
                                <label
                                    v-if="!imagen_cargada"
                                    class="btn btn-outline-success text-success"
                                    id="labelFoto"
                                    for="file_foto"
                                    ><b>Cambiar foto</b
                                    ><input
                                        type="file"
                                        id="file_foto"
                                        accept="image/png, image/gif, image/jpeg"
                                        hidden
                                        @change="cargaImagen"
                                /></label>
                                <button
                                    v-if="imagen_cargada"
                                    class="w-50 mb-1 btn btn-primary btn-sm"
                                    @click="guardarImagen"
                                >
                                    Guardar cambios
                                </button>
                                <button
                                    v-if="imagen_cargada"
                                    class="w-50 mb-1 btn btn-light"
                                    @click="cancelarImagen"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </div>
                        <div cols="d-flex w-100">
                            <div class="row">
                                <div class="text-right bold col-4">
                                    Usuario:
                                </div>
                                <div class="col-8">
                                    {{ user.usuario }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-right bold col-4">Nombre:</div>
                                <div class="col-8">
                                    {{ user.full_name }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-right bold col-4">
                                    Fecha Registro:
                                </div>
                                <div class="col-8">
                                    {{ user.fecha_registro_t }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card card-inverse pa-3">
                    <div class="card-header">
                        <h4 class="mb-0">Cambiar contraseña</h4>
                    </div>
                    <div class="card-body pt-0">
                        <form>
                            <div class="row">
                                <div class="col-12 mb-2 text-danger">
                                    <i
                                        ><small
                                            >Debes ingresar al menos
                                            <span class="bold"
                                                >8 caracteres con una
                                                combinación de al menos: 1
                                                mayúscula, 1 número y un
                                                caracter especial(@#$.&)</span
                                            ></small
                                        ></i
                                    >
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="mb-0"
                                        >Contraseña actual</label
                                    >
                                    <input
                                        placeholder="Contraseña actual"
                                        autocomplete="false"
                                        v-model="form.password_actual"
                                        type="password"
                                        class="form-control"
                                        :class="{
                                            'parsley-error':
                                                form.errors?.password_actual,
                                        }"
                                    />
                                    <ul
                                        v-if="form.errors?.password_actual"
                                        class="list-unstyled text-danger"
                                    >
                                        <li class="parsley-required">
                                            {{ form.errors?.password_actual }}
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="mb-0"
                                        >Ingresa la nueva contraseña</label
                                    >
                                    <input
                                        placeholder="Ingresa la nueva contraseña"
                                        prepend-inner-icon="mdi-lock-outline"
                                        autocomplete="false"
                                        v-model="form.password"
                                        type="password"
                                        class="form-control mt-2"
                                        :class="{
                                            'parsley-error':
                                                form.errors?.password,
                                        }"
                                    />
                                    <ul
                                        v-if="form.errors?.password"
                                        class="list-unstyled text-danger"
                                    >
                                        <li class="parsley-required">
                                            {{ form.errors?.password }}
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="mb-0"
                                        >Repite la nueva contraseña</label
                                    >
                                    <input
                                        placeholder="Repite la nueva contraseña"
                                        autocomplete="false"
                                        v-model="form.password_confirmation"
                                        type="password"
                                        class="form-control mt-2 mb-3"
                                    />
                                </div>
                                <div class="col-md-4">
                                    <button
                                        type="button"
                                        class="btn btn-primary w-100"
                                        @click="enviaFormulario"
                                    >
                                        Guardar cambios
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </Content>
</template>

<style scoped>
label.btn.btn-principal {
    color: white !important;
}
.info_foto {
    display: flex;
    width: 100%;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.info_foto img.image {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
    border: solid 1px gray;
}

#labelFoto b,
.text-success {
    color: green !important;
}

#labelFoto:hover b {
    color: white !important;
}
</style>
