<script setup>
import Content from "@/Components/Content.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { useEntrenamientos } from "@/composables/entrenamientos/useEntrenamientos";
import { ref, onMounted, onBeforeMount } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useAxios } from "@/composables/axios/useAxios";
const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

onBeforeMount(() => {
    appStore.startLoading();
});

const { setEntrenamiento, limpiarEntrenamiento, form } = useEntrenamientos();

const inputFile = ref(null);
const archivo = ref(null);
const cargarArchivo = (e) => {
    archivo.value = e.target.files[0];
};

const obteniendoResultado = ref(false);
const generado = ref(false);

const getResultado = () => {
    generado.value = false;
    if (inputFile.value && archivo.value) {
        obteniendoResultado.value = true;
        const formData = new FormData();
        formData.append("archivo", archivo.value);
        axios
            // .post(route("diagnosticos.archivo"), formData, {
            .post(route("entrenamientos.store"), formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
            .then((response) => {
                generado.value = true;
                archivo.value.value = null;
                form.errors = null;
            })
            .catch((err) => {
                if (err.response && err.response.data) {
                    form.errors = err.response.data.errors.archivo
                        ? {
                              archivo: err.response.data.errors.archivo[0],
                          }
                        : [];
                    console.log(form.errors);
                }
                generado.value = false;
                console.error(err);
            })
            .finally(() => {
                obteniendoResultado.value = false;
            });
    } else {
        Swal.fire({
            icon: "info",
            title: "Error",
            text: `Debes cargar un archivo`,
            confirmButtonColor: "#3085d6",
            confirmButtonText: `Aceptar`,
        });
    }
};

onMounted(async () => {
    appStore.stopLoading();
});
</script>
<template>
    <Head title="Motor de Recomendación IA"></Head>

    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">
                        <i class="fa fa-robot"></i> Motor de Recomendación IA
                    </h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">
                            Motor de Recomendación IA
                        </li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6 mt-3">
                        <label class="required"
                            >Cargar archivo de entrenamiento</label
                        >
                        <input
                            type="file"
                            @change="cargarArchivo($event)"
                            ref="inputFile"
                        />
                        <ul
                            v-if="form.errors?.archivo"
                            class="parsley-errors-list filled"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.archivo }}
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row" v-show="!obteniendoResultado">
                    <div class="col-12 text-center mt-3">
                        <button
                            class="btn btn-outline-success"
                            type="button"
                            @click.prevent="getResultado"
                        >
                            Generar <i class="fa fa-sync"></i>
                        </button>
                    </div>
                    <div class="col-12 mt-3 text-center mb-2" v-if="generado">
                        <label class="h4">Resultado</label>
                        <br />
                        <div class="text-md alert alert-info font-weight-bold">
                            Entrenamiento completado!!!
                        </div>
                    </div>
                </div>
                <div
                    class="row contenedor_loading"
                    v-show="obteniendoResultado"
                >
                    <div class="h5 w-100 text-center text-white">
                        ENTRENANDO...
                    </div>
                    <div class="loader">
                        <div class="book-wrapper">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="white"
                                viewBox="0 0 126 75"
                                class="book"
                            >
                                <rect
                                    stroke-width="5"
                                    stroke="#e05452"
                                    rx="7.5"
                                    height="70"
                                    width="121"
                                    y="2.5"
                                    x="2.5"
                                ></rect>
                                <line
                                    stroke-width="5"
                                    stroke="#e05452"
                                    y2="75"
                                    x2="63.5"
                                    x1="63.5"
                                ></line>
                                <path
                                    stroke-linecap="round"
                                    stroke-width="4"
                                    stroke="#c18949"
                                    d="M25 20H50"
                                ></path>
                                <path
                                    stroke-linecap="round"
                                    stroke-width="4"
                                    stroke="#c18949"
                                    d="M101 20H76"
                                ></path>
                                <path
                                    stroke-linecap="round"
                                    stroke-width="4"
                                    stroke="#c18949"
                                    d="M16 30L50 30"
                                ></path>
                                <path
                                    stroke-linecap="round"
                                    stroke-width="4"
                                    stroke="#c18949"
                                    d="M110 30L76 30"
                                ></path>
                            </svg>

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="#ffffff74"
                                viewBox="0 0 65 75"
                                class="book-page"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-width="4"
                                    stroke="#c18949"
                                    d="M40 20H15"
                                ></path>
                                <path
                                    stroke-linecap="round"
                                    stroke-width="4"
                                    stroke="#c18949"
                                    d="M49 30L15 30"
                                ></path>
                                <path
                                    stroke-width="5"
                                    stroke="#e05452"
                                    d="M2.5 2.5H55C59.1421 2.5 62.5 5.85786 62.5 10V65C62.5 69.1421 59.1421 72.5 55 72.5H2.5V2.5Z"
                                ></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Content>
</template>
<style scoped>
.contenedor_loading {
    margin: 20px 0px 5px 5px;
    background-color: var(--bg1);
    padding: 20px 0px;
}

.loader {
    display: flex;
    align-items: center;
    justify-content: center;
}
.book-wrapper {
    width: 150px;
    height: fit-content;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    position: relative;
}
.book {
    width: 100%;
    height: auto;
    filter: drop-shadow(10px 10px 5px rgba(0, 0, 0, 0.137));
}
.book-wrapper .book-page {
    width: 50%;
    height: auto;
    position: absolute;
    animation: paging 0.3s linear infinite;
    transform-origin: left;
}
@keyframes paging {
    0% {
        transform: rotateY(0deg) skewY(0deg);
    }
    50% {
        transform: rotateY(90deg) skewY(-20deg);
    }
    100% {
        transform: rotateY(180deg) skewY(0deg);
    }
}
</style>
