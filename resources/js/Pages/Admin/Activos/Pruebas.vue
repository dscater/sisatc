<script setup>
import MiModal from "@/Components/MiModal.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
import { useActivoPruebas } from "@/composables/activo_pruebas/useActivoPruebas";
import { useAxios } from "@/composables/axios/useAxios";
const { props: props_page } = usePage();
const { axiosDelete } = useAxios();
const {
    setActivoPrueba,
    limpiarActivoPrueba,
    form: formPrueba,
} = useActivoPruebas();

const props = defineProps({
    muestra_formulario: {
        type: Boolean,
        default: false,
    },
    form: {
        type: Object,
    },
});

const muestra_form = ref(props.muestra_formulario);
const muestra_campos = ref(false);
const enviando = ref(false);
const form = props.form;

const tituloDialog = computed(() => {
    return `<i class="fa fa-clipboard-check"></i> Guíones de Pruebas`;
});

const textBtn = computed(() => {
    if (enviando.value) {
        return `<i class="fa fa-spin fa-spinner"></i> Enviando...`;
    }
    if (formPrueba.id == 0) {
        return `<i class="fa fa-save"></i> Guardar`;
    }
    return `<i class="fa fa-edit"></i> Actualizar`;
});

const enviarFormulario = () => {
    formPrueba.activo_id = form.id;
    enviando.value = true;
    let url =
        formPrueba["_method"] == "POST"
            ? route("activo_pruebas.store")
            : route("activo_pruebas.update", formPrueba.id);

    axios
        .post(url, formPrueba.data())
        .then((response) => {
            console.log("correcto");
            const success = "Registro correcto";
            Swal.fire({
                icon: "success",
                title: "Correcto",
                html: `<strong>${success}</strong>`,
                confirmButtonText: `Aceptar`,
                customClass: {
                    confirmButton: "btn-alert-success",
                },
            });

            muestra_campos.value = false;
            limpiarActivoPrueba();
            cargarPruebas();
            // emits("envio-formulario");
        })
        .catch((error) => {
            if (error.response.status === 422) {
                formPrueba.errors = error.response.data.errors;
                errors.value = error.response.data.errors;
                console.log(error.response.data.errors);
            }

            Swal.fire({
                icon: "error",
                title: "Error",
                html: `<strong>Ocurrió un error</strong>`,
            });
        })
        .finally(() => {
            enviando.value = false;
        });
};

const emits = defineEmits(["cerrar-formulario", "envio-formulario"]);

watch(muestra_form, (newVal) => {
    if (!newVal) {
        emits("cerrar-formulario");
    }
});

const cerrarFormulario = () => {
    muestra_form.value = false;
    document.getElementsByTagName("body")[0].classList.remove("modal-open");
};

const listPruebas = ref([]);
const cargarPruebas = () => {
    axios
        .get(route("activo_pruebas.listado"), {
            params: {
                activo_id: form.id,
            },
        })
        .then((response) => {
            listPruebas.value = response.data.activo_pruebas;
        });
};

const eliminarActivoPrueba = (item) => {
    Swal.fire({
        title: "¿Quierés eliminar este registro?",
        html: `<strong>${item.descripcion}</strong>`,
        showCancelButton: true,
        confirmButtonText: "Si, eliminar",
        cancelButtonText: "No, cancelar",
        denyButtonText: `No, cancelar`,
        customClass: {
            confirmButton: "bg-danger",
            cancelButton: "bg-light text-dark border border-secondary",
        },
    }).then(async (result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            let respuesta = await axiosDelete(
                route("activo_pruebas.destroy", item.id),
            );
            if (respuesta && respuesta.sw) {
                cargarPruebas();
            }
        }
    });
};

const cargarListas = () => {
    cargarPruebas();
};

onMounted(() => {
    cargarListas();
    limpiarActivoPrueba();
    formPrueba.activo_id = form.id;
});
</script>

<template>
    <MiModal
        :open_modal="muestra_form"
        @close="cerrarFormulario"
        :size="'modal-xl'"
        :header-class="'bg-principal'"
        :footer-class="'justify-content-end'"
    >
        <template #header>
            <h4 class="modal-title text-white" v-html="tituloDialog"></h4>
            <button
                type="button"
                class="btn-close btn-close-white"
                @click.prevent="cerrarFormulario()"
            ></button>
        </template>

        <template #body>
            <div class="row">
                <div class="col-6">
                    <p><strong>Código Técnico:</strong> {{ form.codigo }}</p>
                </div>
                <div class="col-6">
                    <p><strong>Nombre Real:</strong> {{ form.nombre }}</p>
                </div>
                <div class="col-6">
                    <p><strong>Descripción:</strong> {{ form.descripcion }}</p>
                </div>
                <div class="col-6">
                    <p>
                        <strong>Tipo de Activo:</strong>
                        {{ form.tipo_activo.nombre }}
                    </p>
                </div>
                <div class="col-6">
                    <p>
                        <strong>Versión:</strong>
                        {{ form.version }}
                    </p>
                </div>
            </div>
            <div class="row" v-if="muestra_campos">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form
                                @submit.prevent="enviarFormulario()"
                                class="container-fluid"
                            >
                                <div class="row">
                                    <div class="col-md-4 mt-2">
                                        <label class="required"
                                            >Descripción</label
                                        >
                                        <el-input
                                            type="textarea"
                                            v-model="formPrueba.descripcion"
                                            autosize
                                        ></el-input>
                                        <ul
                                            v-if="
                                                formPrueba.errors?.descripcion
                                            "
                                            class="list-unstyled text-danger"
                                        >
                                            <li class="parsley-required">
                                                {{
                                                    formPrueba.errors
                                                        ?.descripcion[0]
                                                }}
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4 mt-2">
                                        <label class="required">Módulo</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="formPrueba.modulo"
                                        />
                                        <ul
                                            v-if="formPrueba.errors?.modulo"
                                            class="list-unstyled text-danger"
                                        >
                                            <li class="parsley-required">
                                                {{
                                                    formPrueba.errors?.modulo[0]
                                                }}
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4 mt-2">
                                        <label class="required"
                                            >Prueba a Ejecutar</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="formPrueba.prueba"
                                        />
                                        <ul
                                            v-if="formPrueba.errors?.prueba"
                                            class="list-unstyled text-danger"
                                        >
                                            <li class="parsley-required">
                                                {{
                                                    formPrueba.errors?.prueba[0]
                                                }}
                                            </li>
                                        </ul>
                                    </div>
                                    <div
                                        class="col-md-12 d-flex justify-content-end mt-2"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-light mx-1"
                                            @click.prevent="
                                                limpiarActivoPrueba();
                                                muestra_campos = false;
                                            "
                                        >
                                            Cancelar
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-primary mx-1"
                                            :disabled="enviando"
                                            @click.prevent="enviarFormulario"
                                            v-html="textBtn"
                                        ></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 overflow-auto">
                    <button
                        type="button"
                        class="btn btn-primary my-1"
                        @click="muestra_campos = true"
                        v-if="!muestra_campos"
                    >
                        <i class="fa fa-plus"></i> Nuevo
                    </button>
                    <table class="table table-bordered" v-if="listPruebas">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Descripción</th>
                                <th>Módulo</th>
                                <th>Prueba a Ejecutar</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="listPruebas.length > 0">
                                <tr v-for="(item, index) in listPruebas">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.descripcion }}</td>
                                    <td>{{ item.modulo }}</td>
                                    <td>{{ item.prueba }}</td>
                                    <td>
                                        <template
                                            v-if="
                                                props_page.auth?.user
                                                    .permisos == '*' ||
                                                props_page.auth?.user.permisos.includes(
                                                    'activo_pruebas.edit',
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
                                                        setActivoPrueba(item);
                                                        muestra_campos = true;
                                                    "
                                                >
                                                    <i
                                                        class="fa fa-pen"
                                                    ></i></button
                                            ></el-tooltip>
                                        </template>
                                        <template
                                            v-if="
                                                props_page.auth?.user
                                                    .permisos == '*' ||
                                                props_page.auth?.user.permisos.includes(
                                                    'activo_pruebas.destroy',
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
                                                    @click="
                                                        eliminarActivoPrueba(
                                                            item,
                                                        )
                                                    "
                                                >
                                                    <i
                                                        class="fa fa-trash-alt"
                                                    ></i></button
                                            ></el-tooltip>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr>
                                    <td colspan="4" class="text-center">
                                        Sin registros
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
        <template #footer>
            <button
                type="button"
                class="btn btn-light"
                @click.prevent="cerrarFormulario()"
            >
                Cerrar
            </button>
        </template>
    </MiModal>
</template>
