<script setup>
import MiModal from "@/Components/MiModal.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { watch, ref, computed } from "vue";
const props = defineProps({
    muestra_formulario: {
        type: Boolean,
        default: false,
    },
    formUser: {
        type: Object,
    },
});

const muestra_form = ref(props.muestra_formulario);
const enviando = ref(false);
const form = useForm({
    password: "",
});
const { flash } = usePage().props;
const tituloDialog = computed(() => {
    return form.id == 0
        ? `<i class="fa fa-plus"></i>`
        : `<i class="fa fa-key"></i> Cambiar Contraseña`;
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
    let url = route("usuarios.password", props.formUser.id);

    form.put(url, {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Correcto",
                text: `${flash.bien ? flash.bien : "Proceso realizado"}`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            form.password = "";

            document
                .getElementsByTagName("body")[0]
                .classList.remove("modal-open");
            emits("envio-formulario");
        },
        onError: (err) => {
            Swal.fire({
                icon: "info",
                title: "Error",
                text: `${
                    flash.error
                        ? flash.error
                        : err.error
                          ? err.error
                          : "Hay errores en el formulario"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
        },
    });
};

const emits = defineEmits(["cerrar-formulario", "envio-formulario"]);

watch(muestra_form, (newVal) => {
    if (!newVal) {
        emits("cerrar-formulario");
    }
});

const verPassword = ref(false);

const cerrarFormulario = () => {
    muestra_form.value = false;
};
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
            <form @submit.prevent="enviarFormulario()">
                <div class="row">
                    <div class="px-4 text-center col-md-12">
                        <span class="text-body-2 h3"
                            >{{ formUser.nombre }}
                            {{ formUser.apellido }}
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Ingresa la nueva contraseña:</label>
                        <div class="input-group">
                            <input
                                placeholder="Ingresa la nueva contraseña"
                                class="form-control"
                                autocomplete="false"
                                v-model="form.password"
                                :type="verPassword ? 'text' : 'password'"
                            />
                            <button
                                class="btn btn-ligth"
                                @click.prevent="verPassword = !verPassword"
                                type="button"
                            >
                                <i
                                    class="fa"
                                    :class="[
                                        verPassword
                                            ? 'fa fa-eye'
                                            : 'fa fa-eye-slash',
                                    ]"
                                ></i>
                            </button>
                        </div>
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
