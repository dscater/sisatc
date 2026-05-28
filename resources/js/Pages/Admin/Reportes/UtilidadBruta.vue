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
    cargarProductos();
    cargarCategoriaProductos();
};
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

const getFechaActual = () => {
    const fecha = new Date();
    const dia = String(fecha.getDate()).padStart(2, "0");
    const mes = String(fecha.getMonth() + 1).padStart(2, "0");
    const anio = fecha.getFullYear();
    return `${anio}-${mes}-${dia}`;
};
const form = ref({
    categoria_producto_id: "todos",
    producto_id: "todos",
    fecha_ini: getFechaActual(),
    fecha_fin: getFechaActual(),
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
const listProductos = ref([]);

const generarReporte = () => {
    generando.value = true;
    const url = route("reportes.r_utilidad_bruta", form.value);
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

const cargarProductos = () => {
    axios.get(route("productos.listado")).then((response) => {
        listProductos.value = response.data.productos;

        listProductos.value.unshift({
            id: "todos",
            nombre: "TODOS",
        });
    });
};
</script>
<template>
    <Head title="Reporte Utilidad bruta por Producto"></Head>
    <Content>
        <template #header>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Utilidad bruta por Producto</h4>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">
                            Reportes - Utilidad bruta por Producto
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
                                    <el-select
                                        v-model="form.categoria_producto_id"
                                        filterable
                                        no-data-text="Sin datos"
                                        no-match-text="Sin resultados"
                                    >
                                        <el-option
                                            v-for="item in listCategoriaProductos"
                                            :key="item.id"
                                            :value="item.id"
                                            :label="item.nombre"
                                        >
                                        </el-option>
                                    </el-select>
                                </div>
                                <div class="col-md-12">
                                    <label>Seleccionar producto*</label>
                                    <el-select
                                        v-model="form.producto_id"
                                        filterable
                                        no-data-text="Sin datos"
                                        no-match-text="Sin resultados"
                                    >
                                        <el-option
                                            v-for="item in listProductos"
                                            :key="item.id"
                                            :value="item.id"
                                            :label="item.nombre"
                                        >
                                        </el-option>
                                    </el-select>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label>Rango de Fechas</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input
                                                type="date"
                                                v-model="form.fecha_ini"
                                                class="form-control"
                                            />
                                        </div>
                                        <div class="col-md-6">
                                            <input
                                                type="date"
                                                v-model="form.fecha_fin"
                                                class="form-control"
                                            />
                                        </div>
                                    </div>
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
