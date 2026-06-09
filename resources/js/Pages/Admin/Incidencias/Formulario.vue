<script setup>
import MiModal from "@/Components/MiModal.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
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
        ? `<i class="fa fa-plus"></i> Nueva Incidencia`
        : `<i class="fa fa-edit"></i> Editar Incidencia`;
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
            ? route("incidencias.store")
            : route("incidencias.update", form.id);

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

const listTipoActivos = ref([]);
const cargarTipoActivos = () => {
    axios.get(route("tipo_activos.listado")).then((response) => {
        listTipoActivos.value = response.data.tipo_activos;
    });
};

const listEstados = ref(["ABIERTO", "EN CORRECCIÓN", "RE-TEST", "CERRADO"]);

const cargarListas = () => {
    cargarTipoActivos();
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
                        <label class="required">Tipo de Activo</label>
                        <el-select
                            v-model="form.tipo_activo_id"
                            placeholder="- Seleccione -"
                            no-data-text="Sin datos"
                            no-match-text="Sin resultados"
                            filterable
                        >
                            <el-option
                                v-for="item in listTipoActivos"
                                :key="item.id"
                                :value="item.id"
                                :label="item.nombre"
                            ></el-option>
                        </el-select>
                        <ul
                            v-if="form.errors?.tipo_activo_id"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.tipo_activo_id }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label class="required">Módulo</label>
                        <input
                            type="text"
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.modulo,
                            }"
                            v-model="form.modulo"
                        />
                        <ul
                            v-if="form.errors?.modulo"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.modulo }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label class="required">Tipo de falla</label>
                        <input
                            type="text"
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.tipo_falla,
                            }"
                            v-model="form.tipo_falla"
                        />
                        <ul
                            v-if="form.errors?.tipo_falla"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.tipo_falla }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label class="required">Severidad</label>
                        <input
                            type="text"
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.severidad,
                            }"
                            v-model="form.severidad"
                        />
                        <ul
                            v-if="form.errors?.severidad"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.severidad }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label class="required">Prueba Ejecutada</label>
                        <input
                            type="text"
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.prueba,
                            }"
                            v-model="form.prueba"
                        />
                        <ul
                            v-if="form.errors?.prueba"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.prueba }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label class="required">Resultado</label>
                        <input
                            type="text"
                            class="form-control"
                            :class="{
                                'parsley-error': form.errors?.resultado,
                            }"
                            v-model="form.resultado"
                        />
                        <ul
                            v-if="form.errors?.resultado"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.resultado }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label class="required">Bug</label>
                        <el-select v-model="form.bug" placeholder="Seleccione">
                            <el-option
                                v-for="item in ['SI', 'NO']"
                                :key="item"
                                :value="item"
                                :label="item"
                            ></el-option>
                        </el-select>
                        <ul
                            v-if="form.errors?.bug"
                            class="list-unstyled text-danger"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.bug }}
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
