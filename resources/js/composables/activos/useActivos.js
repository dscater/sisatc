import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

export const useActivos = () => {
    const initialState = {
        id: 0,
        codigo: "",
        nombre: "",
        descripcion: "",
        tipo_activo_id: "",
        version: "",
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setActivo = (item = null, ver = false) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarActivo = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    onMounted(() => {});

    return {
        form,
        setActivo,
        limpiarActivo,
    };
};
