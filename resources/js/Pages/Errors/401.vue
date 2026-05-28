<script setup>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Content from "@/Components/Content.vue";
const { props: props_page } = usePage();
</script>
<template>
    <Head title="Acceso no autorizado"></Head>
    <Content>
        <template #header>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Inicio</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">401</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN card -->
                <div class="card card-inverse">
                    <!-- BEGIN card-body -->
                    <div class="card-body text-center pb-5 pt-3">
                        <h4 class="w-100 text-center">
                            No tienes autorización para ver esta página
                        </h4>
                        <Link :href="route('inicio')" class="btn btn-default"
                            >Volver al inicio</Link
                        >
                    </div>
                    <!-- END card-body -->
                </div>
                <!-- END card -->
            </div>
        </div>
    </Content>
</template>
<style></style>
