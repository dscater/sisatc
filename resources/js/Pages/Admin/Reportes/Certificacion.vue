<script setup>
import Content from "@/Components/Content.vue";
import { computed, onBeforeMount, onMounted, ref } from "vue";
import { Head, usePage, Link } from "@inertiajs/vue3";
import { useAppStore } from "@/stores/aplicacion/appStore";
const appStore = useAppStore();

const cargarListas = () => {
    cargarActivos();
};

onBeforeMount(() => {
    appStore.startLoading();
});

onMounted(() => {
    cargarListas();
    appStore.stopLoading();
});

const obtenerFechaActual = () => {
    const fecha = new Date();
    const anio = fecha.getFullYear();
    const mes = String(fecha.getMonth() + 1).padStart(2, "0"); // Mes empieza desde 0
    const dia = String(fecha.getDate()).padStart(2, "0"); // Día del mes
    return `${anio}-${mes}-${dia}`;
};

const form = ref({
    activo_id: "todos",
    estado: "TODOS",
    fecha_ini: obtenerFechaActual(),
    fecha_fin: obtenerFechaActual(),
    formato: "pdf",
});

const generando = ref(false);
const txtBtn = computed(() => {
    if (generando.value) {
        return "Generando Reporte...";
    }
    return "Generar Reporte";
});

const listActivos = ref([]);
const cargarActivos = () => {
    axios.get(route("activos.listado")).then((response) => {
        listActivos.value = response.data.activos;
        listActivos.value.unshift({
            id: "todos",
            nombre: "TODOS",
        });
    });
};

const listEstados = ref(["TODOS", "INICIO", "ACEPTADO", "RECHAZADO"]);

const listTipoReporte = ref([
    {
        value: "pdf",
        label: "PDF",
    },
    {
        value: "excel",
        label: "EXCEL",
    },
]);

const generarReporte = () => {
    generando.value = true;
    const url = route("reportes.r_certificacion", form.value);
    window.open(url, "_blank");
    setTimeout(() => {
        generando.value = false;
    }, 500);
};
</script>
<template>
    <Head title="Reporte Certificación"></Head>
    <Content>
        <template #header>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Reporte Certificación</h4>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">
                            Reporte Certificación
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
                                    <label class="mb-0"
                                        >Seleccionar Activo*</label
                                    >
                                    <el-select
                                        v-model="form.activo_id"
                                        filterable
                                    >
                                        <el-option
                                            v-for="item in listActivos"
                                            :key="item.id"
                                            :value="item.id"
                                            :label="item.nombre"
                                        >
                                        </el-option>
                                    </el-select>
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-0">Estado*</label>
                                    <select
                                        v-model="form.estado"
                                        class="form-control"
                                    >
                                        <option
                                            v-for="item in listEstados"
                                            :value="item"
                                        >
                                            {{ item }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label class="mb-0">Rango de Fechas</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input
                                                type="date"
                                                class="form-control"
                                                v-model="form.fecha_ini"
                                            />
                                        </div>
                                        <div class="col-md-6">
                                            <input
                                                type="date"
                                                class="form-control"
                                                v-model="form.fecha_fin"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="text-muted w-100 text-center text-xs"
                                    >
                                        Dejar vacío para listar todos los
                                        registros
                                    </div>
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
