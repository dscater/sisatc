import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

export const useCategoriaProductos = () => {
    const initialState = {
        id: 0,
        nombre: "",
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setCategoriaProducto = (item = null, ver = false) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarCategoriaProducto = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    onMounted(() => {});

    return {
        form,
        setCategoriaProducto,
        limpiarCategoriaProducto,
    };
};
