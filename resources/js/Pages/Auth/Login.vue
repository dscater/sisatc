<script>
import "@/assets/css/variables.css";
import Login from "@/Layouts/Login.vue";
import { computed, onMounted, ref } from "vue";
export default {
    layout: Login,
};
</script>
<script setup>
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";

import { useConfiguracion } from "@/composables/configuracion/useConfiguracion";
const { props } = usePage();
const { oConfiguracion } = useConfiguracion();
const form = useForm({
    usuario: "",
    password: "",
});

var url_assets = "";
const enviando = ref(false);
const errors = ref([]);
var url_principal = "";
const muestra_password = ref(false);
const enviarFormulario = () => {
    enviando.value = true;
    axios
        .post(route("login"), form)
        .then((response) => {
            form.usuario = "";
            form.password = "";
            if (response.data.codigo == true) {
                Swal.fire({
                    icon: "info",
                    title: "Atención",
                    html: `Te enviamos un código de verificación a tu correo para que puedas iniciar sesión`,
                    confirmButtonText: `Aceptar`,
                    customClass: {
                        confirmButton: "btn-alert-primary",
                    },
                });
                // ABRIR MODAL CÓDIGO
                oUserVerificado.value = response.data.user;
                muestraFormCodigo.value = true;
                emits("cerrar-formulario");
            } else {
                // ENVIAR AL INICIO
                Swal.fire({
                    icon: "success",
                    title: "Correcto",
                    html: `<strong>Sesión iniciada correctamente</strong>`,
                    showConfirmButton: false,
                    timer: 1500,
                });
                setTimeout(() => {
                    window.location.href = route("inicio");
                });
            }
        })
        .catch((error) => {
            if (error.response?.status === 422) {
                errors.value = error.response.data.errors || {};
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    html: `<strong>${error.message}</strong>`,
                    confirmButtonText: `Aceptar`,
                    customClass: {
                        confirmButton: "btn-error",
                    },
                });
            }
        })
        .finally(() => {
            enviando.value = false;
        });
};
const textBtn = computed(() => {
    if (enviando.value) {
        return `<i class="fa fa-spin fa-spinner"></i> Ingresando...`;
    }
    return `<i class="fa fa-sign-in-alt"></i> Ingresar`;
});

onMounted(() => {
    url_assets = props.url_assets;
    url_principal = props.url_principal;
});
</script>

<template>
    <Head title="Login"></Head>
    <!-- BEGIN login -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 offset-lg-4 col-md-8 offset-md-2 text-center">
                <div class="card">
                    <div class="card-body">
                        <img
                            :src="oConfiguracion.url_logo"
                            alt="Logo"
                            height="150px"
                            class="logoPortada rounded"
                            lazy
                        />
                        <h4 class="mt-2 fw-bold">
                            {{ oConfiguracion.nombre_sistema }}
                        </h4>

                        <form @submit.prevent="enviarFormulario()">
                            <h5 class="w-100 text-center h5 mt-2">
                                Iniciar Sesión
                            </h5>
                            <div class="row">
                                <div class="col-12">
                                    <label
                                        for="usuario"
                                        class="d-flex text-dark fw-bold text-lg"
                                        style="z-index: 100"
                                        >Usuario</label
                                    >
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">
                                            <i class="fa fa-user"></i>
                                        </span>
                                        <input
                                            type="text"
                                            name="usuario"
                                            id="usuario"
                                            class="form-control"
                                            placeholder="Usuario"
                                            v-model="form.usuario"
                                            autofocus
                                            ref="inputUsuario"
                                            @keypress.enter="enviarFormulario"
                                        />
                                    </div>
                                    <ul
                                        v-if="errors?.usuario"
                                        class="alert alert-danger text-white text-xs p-1 list-unstyled mb-0"
                                    >
                                        <li class="parsley-required">
                                            {{ errors?.usuario[0] }}
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-12 mt-2">
                                    <label
                                        for="password"
                                        class="d-flex text-dark fw-bold text-lg"
                                        style="z-index: 100"
                                        >Contraseña</label
                                    >
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">
                                            <i class="fa fa-key"></i>
                                        </span>
                                        <input
                                            :type="
                                                muestra_password
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            name="password"
                                            id="password"
                                            class="form-control"
                                            v-model="form.password"
                                            autocomplete="false"
                                            placeholder="Contraseña"
                                            @keypress.enter="enviarFormulario"
                                        />
                                        <button
                                            class="btn btn-default bg-white border"
                                            type="button"
                                            @click="
                                                muestra_password =
                                                    !muestra_password
                                            "
                                        >
                                            <i
                                                class="fa"
                                                :class="[
                                                    muestra_password
                                                        ? 'fa-eye'
                                                        : 'fa-eye-slash',
                                                ]"
                                            ></i>
                                        </button>
                                    </div>
                                    <ul
                                        v-if="errors?.password"
                                        class="alert alert-danger text-white text-xs p-1 list-unstyled"
                                    >
                                        <li class="parsley-required">
                                            {{ errors?.password[0] }}
                                        </li>
                                    </ul>
                                </div>
                                <div class="w-100" v-if="form.errors?.usuario">
                                    <span
                                        class="invalid-feedback alert alert-danger"
                                        style="display: block"
                                        role="alert"
                                    >
                                        <strong>{{ errors.usuario }}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row my-3">
                                <div class="col-12">
                                    <button
                                        type="button"
                                        class="btn btn-primary w-100"
                                        @click.prevent="enviarFormulario"
                                        :disabled="enviando"
                                        v-html="textBtn"
                                    ></button>
                                </div>
                            </div>
                        </form>
                        <!-- END login-content -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- END login -->
</template>

<style scoped>
.row.login {
    background-color: white;
}
.row.login > .col-md-6:nth-child(1) img {
    height: 100%;
    object-fit: cover;
}

.row.login > .col-md-6:nth-child(2) {
    text-align: center;
}

.nombre_sistema:hover {
    color: var(--text-white1);
}

.logoPortada {
    margin: auto;
    margin-top: 10px;
    max-width: 70%;
}
</style>
