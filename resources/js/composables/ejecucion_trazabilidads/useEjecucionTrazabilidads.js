import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

export const useEjecucionTrazabilidads = () => {
    const initialState = {
        id: 0,
        activo_id: "",
        estado: "",
        trazabilidad: "",
        user_id: "",
        fecha: "",
        hora: "",
        ejecucion_archivos: [],
        eliminados_archivos: [],
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setEjecucionTrazabilidad = (item = null, ver = false) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarEjecucionTrazabilidad = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    onMounted(() => {});

    return {
        form,
        setEjecucionTrazabilidad,
        limpiarEjecucionTrazabilidad,
    };
};
