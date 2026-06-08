import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

export const useActivoPruebas = () => {
    const initialState = {
        id: 0,
        activo_id: "",
        descripcion: "",
        modulo: "",
        prueba: "",
        user_id: "",
        fecha: "",
        hora: "",
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setActivoPrueba = (item = null, ver = false) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarActivoPrueba = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    onMounted(() => {});

    return {
        form,
        setActivoPrueba,
        limpiarActivoPrueba,
    };
};
