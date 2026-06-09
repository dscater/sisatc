<script setup>
import MiModal from "@/Components/MiModal.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
import MiDropZone from "@/Components/MiDropZone.vue";
import axios from "axios";
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
const enviando = ref(false);
const form = props.form;

const tituloDialog = computed(() => {
    return form.id == 0
        ? `<i class="fa fa-plus"></i> Nueva Ejecución de Trazabilidad`
        : `<i class="fa fa-edit"></i> Editar Ejecución de Trazabilidad`;
});

const textBtn = computed(() => {
    if (enviando.value) {
        return `<i class="fa fa-spin fa-spinner"></i> Enviando...`;
    }
    if (form.id == 0) {
        return `<i class="fa fa-save"></i> Guardar`;
    }
    return `<i class="fa fa-edit"></i> Actualizar`;
});

const enviarFormulario = () => {
    enviando.value = true;
    let url =
        form["_method"] == "POST"
            ? route("ejecucion_trazabilidads.store")
            : route("ejecucion_trazabilidads.update", form.id);

    form.post(url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: (response) => {
            console.log("correcto");
            const success =
                response.props.flash.success ?? "Proceso realizado con éxito";
            Swal.fire({
                icon: "success",
                title: "Correcto",
                html: `<strong>${success}</strong>`,
                confirmButtonText: `Aceptar`,
                customClass: {
                    confirmButton: "btn-alert-success",
                },
            });
            document
                .getElementsByTagName("body")[0]
                .classList.remove("modal-open");
            emits("envio-formulario");
        },
        onError: (err, code) => {
            console.log(code ?? "");
            console.log(form.errors);
            if (form.errors) {
                const error =
                    "Existen errores en el formulario, por favor verifique";
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    html: `<strong>${error}</strong>`,
                    confirmButtonText: `Aceptar`,
                    customClass: {
                        confirmButton: "btn-error",
                    },
                });
            } else {
                const error =
                    "Ocurrió un error inesperado contactese con el Administrador";
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    html: `<strong>${error}</strong>`,
                    confirmButtonText: `Aceptar`,
                    customClass: {
                        confirmButton: "btn-error",
                    },
                });
            }
            console.log("error: " + err.error);
        },
        onFinish: () => {
            enviando.value = false;
        },
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

const listActivos = ref([]);
const cargarActivos = () => {
    axios.get(route("activos.listado")).then((response) => {
        listActivos.value = response.data.activos;

        if (form.id != 0) {
            cargarRecomendaciones();
        }
    });
};

const listEstados = ref(["INICIO", "ACEPTADO", "RECHAZADO"]);

const cargarListas = () => {
    cargarActivos();
};

const detectaArchivos = (files) => {
    form.ejecucion_archivos = files;
};

watch(
    () => form.activo_id,
    () => {
        console.log("ASDasd");
        // cargarActivos();
    },
);

const detectaEliminados = (eliminados) => {
    form.eliminados_archivos = eliminados;
};

const listRecomendacions = ref([]);
const listUsuarios = ref([]);

const cargarRecomendaciones = () => {
    console.log("AA");
    if (!form.activo_id) return;

    const activo = listActivos.value.filter(
        (elem) => elem.id == form.activo_id,
    )[0];
    const tipo_activo_id = activo.tipo_activo_id;
    axios
        .get(route("tipo_activos.recomendacion"), {
            params: {
                tipo_activo_id: tipo_activo_id,
            },
        })
        .then((response) => {
            listRecomendacions.value = response.data;
            cargarUsersTrazabilidad();
        });
};

const cargarUsersTrazabilidad = () => {
    console.log("BB");
    if (!form.activo_id) return;

    const activo = listActivos.value.filter(
        (elem) => elem.id == form.activo_id,
    )[0];
    const tipo_activo_id = activo.tipo_activo_id;
    axios
        .get(route("tipo_activos.usuarios"), {
            params: {
                tipo_activo_id: tipo_activo_id,
            },
        })
        .then((response) => {
            listUsuarios.value = response.data;
        });
};

onMounted(() => {
    cargarListas();
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
            <form @submit.prevent="enviarFormulario()" class="container-fluid">
                <p class="text-muted text-xs mb-0">
                    Todos los campos con
                    <span class="text-danger">(*)</span> son obligatorios.
                </p>
                <div class="row">
                    <div class="col-md-4 mt-2">
                        <label class="required">Activo</label>
                        <el-select
                            v-model="form.activo_id"
                            placeholder="- Seleccione -"
                            no-data-text="Sin datos"
                            no-match-text="Sin resultados"
                            filterable
                            @change="
                                cargarRecomendaciones();
                                cargarUsersTrazabilidad();
                            "
                        >
                            <el-option
                                v-for="item in listActivos"
                                :key="item.id"
                                :value="item.id"
                                :label="`${item.codigo} - ${item.nombre}`"
                            ></el-option>
                        </el-select>
                        <ul
                            v-if="form.errors?.activo_id"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.activo_id }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label class="required">Estado</label>
                        <el-select
                            v-model="form.estado"
                            placeholder="Seleccione"
                        >
                            <el-option
                                v-for="item in listEstados"
                                :key="item"
                                :value="item"
                                :label="item"
                            ></el-option>
                        </el-select>
                        <ul
                            v-if="form.errors?.estado"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.estado }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-12 mt-2">
                        <label class="required">Cargar Evidencias</label>
                        <MiDropZone
                            :files="form.ejecucion_archivos"
                            :maximo="100"
                            @UpdateFiles="detectaArchivos"
                            @addEliminados="detectaEliminados"
                        ></MiDropZone>
                        <ul
                            v-if="form.errors?.ejecucion_archivos"
                            class="d-block text-danger list-unstyled"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.ejecucion_archivos }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-12">
                        <hr />
                        <h4 class="text-md text-center">
                            Pruebas Recomendadas
                        </h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Módulo</th>
                                    <th>% Recomendación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in listRecomendacions">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.modulo }}</td>
                                    <td>{{ item.porcentaje }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12">
                        <hr />
                        <h4 class="text-md text-center">Trazabilidad</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Usuario</th>
                                    <th>Módulo</th>
                                    <th>Fecha Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in listUsuarios">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.usuario }}</td>
                                    <td>{{ item.modulo }}</td>
                                    <td>{{ item.fecha }} {{ item.hora }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <button
                type="button"
                class="btn btn-light"
                @click.prevent="cerrarFormulario()"
            >
                Cerrar
            </button>
            <button
                type="button"
                class="btn btn-primary"
                :disabled="enviando"
                @click.prevent="enviarFormulario"
                v-html="textBtn"
            ></button>
        </template>
    </MiModal>
</template>
