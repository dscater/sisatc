import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

export const useEntrenamientos = () => {
    const initialState = {
        id: 0,
        activo: "",
        activo_id: "",
        modulo: "",
        tipo_falla: "",
        severidad: "",
        prueba: "",
        resultado: "",
        bug: "",
        estado: "",
        res: "",
        user_id: "",
        fecha: "",
        hora: "",
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setEntrenamiento = (item = null, ver = false) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarEntrenamiento = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    onMounted(() => {});

    return {
        form,
        setEntrenamiento,
        limpiarEntrenamiento,
    };
};
