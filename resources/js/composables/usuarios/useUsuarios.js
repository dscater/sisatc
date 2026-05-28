import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

export const useUsuarios = () => {
    const initialState = {
        id: 0,
        usuario: "",
        nombre: "",
        apellido: "",
        email: "",
        fono: "",
        password: "",
        foto: "",
        acceso: 1,
        tipo: "",
        fecha_registro: "",
        status: "",
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setUsuario = (item = null, ver = false) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarUsuario = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    onMounted(() => {});

    return {
        form,
        setUsuario,
        limpiarUsuario,
    };
};
