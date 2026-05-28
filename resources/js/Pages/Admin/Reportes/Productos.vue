<script setup>
import Content from "@/Components/Content.vue";
import { computed, onBeforeMount, onMounted, ref } from "vue";
import { Head, usePage, Link } from "@inertiajs/vue3";
import { useAppStore } from "@/stores/aplicacion/appStore";
const appStore = useAppStore();

onBeforeMount(() => {
    appStore.startLoading();
});

const cargarListas = () => {
    cargarCategoriaProductos();
};

const listSucursals = ref([]);

onMounted(() => {
    cargarListas();
    appStore.stopLoading();
});

const listFormatos = ref([
    {
        icon: "fa fa-file-pdf",
        value: "pdf",
        label: "PDF",
    },
    {
        icon: "fa fa-file-excel",
        value: "excel",
        label: "EXCEL",
    },
]);

const form = ref({
    categoria_producto_id: "todos",
    formato: "pdf",
});

const generando = ref(false);
const txtBtn = computed(() => {
    if (generando.value) {
        return "Generando Reporte...";
    }
    return "Generar Reporte";
});

const listCategoriaProductos = ref([]);

const generarReporte = () => {
    generando.value = true;
    const url = route("reportes.r_productos", form.value);
    window.open(url, "_blank");
    setTimeout(() => {
        generando.value = false;
    }, 500);
};

const cargarCategoriaProductos = () => {
    axios.get(route("categoria_productos.listado")).then((response) => {
        listCategoriaProductos.value = response.data.categoria_productos;

        listCategoriaProductos.value.unshift({
            id: "todos",
            nombre: "TODOS",
        });
    });
};
</script>
<template>
    <Head title="Reporte Lista de Productos"></Head>
    <Content>
        <template #header>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Lista de Productos</h4>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">
                            Reportes - Lista de Productos
                        </li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="generarReporte">
                            <div class="row">
                                <div class="col-md-12">
                                    <label
                                        >Seleccionar Categoría de
                                        producto*</label
                                    >
                                    <select
                                        v-model="form.categoria_producto_id"
                                        class="form-control"
                                    >
                                        <option
                                            v-for="item in listCategoriaProductos"
                                            :value="item.id"
                                        >
                                            {{ item.nombre }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-12 text-center mt-2">
                                    <el-radio-group v-model="form.formato">
                                        <el-radio
                                            v-for="item in listFormatos"
                                            :value="item.value"
                                            size="large"
                                            ><i :class="item.icon"></i>
                                            {{ item.label }}</el-radio
                                        >
                                    </el-radio-group>
                                </div>
                                <div class="col-md-12 text-center mt-3">
                                    <button
                                        class="btn btn-primary"
                                        block
                                        @click="generarReporte"
                                        :disabled="generando"
                                        v-text="txtBtn"
                                    ></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </Content>
</template>
