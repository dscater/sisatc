import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

export const useIncidencias = () => {
    const initialState = {
        id: 0,
        tipo_activo_id: "",
        modulo: "",
        tipo_falla: "",
        severidad: "",
        prueba: "",
        resultado: "",
        bug: "",
        estado: "",
        user_id: "",
        fecha: "",
        hora: "",
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setIncidencia = (item = null, ver = false) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarIncidencia = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    onMounted(() => {});

    return {
        form,
        setIncidencia,
        limpiarIncidencia,
    };
};
