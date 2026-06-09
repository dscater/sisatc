<script setup>
import Content from "@/Components/Content.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { useEntrenamientos } from "@/composables/entrenamientos/useEntrenamientos";
import { ref, onMounted, onBeforeMount } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import { useAxios } from "@/composables/axios/useAxios";
const { props: props_page } = usePage();
const appStore = useAppStore();
const { axiosDelete } = useAxios();

onBeforeMount(() => {
    appStore.startLoading();
});

const { setEntrenamiento, limpiarEntrenamiento, form } = useEntrenamientos();

onMounted(async () => {
    appStore.stopLoading();
});
</script>
<template>
    <Head title="Motor de Recomendación IA"></Head>

    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">
                        <i class="fa fa-robot"></i> Motor de Recomendación IA
                    </h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">
                            Motor de Recomendación IA
                        </li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>

        <div class="row">
            <div class="col-md-12">
                <div class="row"></div>
            </div>
        </div>
    </Content>
</template>
