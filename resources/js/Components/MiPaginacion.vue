<script setup>
import { watch, ref, onMounted } from "vue";

const props = defineProps({
    paginationClass: {
        type: String,
        default: "",
    },
    totalData: {
        type: Number,
        default: 14,
    },
    currentPage: {
        type: Number,
        default: 1,
    },
    perPage: {
        type: Number,
        default: 5,
    },
    numPages: {
        type: Number,
        default: 7,
    },
});

const listPaginas = ref([]);
const total = ref(props.totalData);
const num_pages = ref(props.numPages);
const per_page = ref(props.perPage);

const getTotalPages = () => {
    return Math.ceil(total.value / per_page.value);
};

const totalPages = ref(getTotalPages());

watch(
    () => props.currentPage,
    (newVal) => {
        current_page.value = newVal;
    },
);

watch(
    () => props.totalData,
    (newVal) => {
        total.value = newVal;
        totalPages.value = getTotalPages();
        generarPaginas();
    },
);

watch(
    () => props.perPage,
    (newVal) => {
        per_page.value = newVal;
        totalPages.value = getTotalPages();
        generarPaginas();
    },
);

watch(
    () => props.numPages,
    (newVal) => {
        num_pages.value = newVal;
        generarPaginas();
    },
);

const generarPaginas = () => {
    listPaginas.value = [];
    const maximoPaginas = num_pages.value;
    const rango_lados = parseInt(maximoPaginas / 2);
    if (totalPages.value > maximoPaginas) {
        let inicio = current_page.value - rango_lados;
        let fin = current_page.value + rango_lados;
        if (inicio < 1) {
            inicio = 1;
            fin = inicio + rango_lados * 2;
        }
        if (fin > totalPages.value) {
            fin = totalPages.value;
            let restante = totalPages.value - current_page.value;
            inicio = inicio - rango_lados + restante;
        }
        for (let i = inicio; i <= current_page.value - 1; i++) {
            listPaginas.value.push(i);
        }
        listPaginas.value.push(current_page.value);

        for (let i = current_page.value + 1; i <= fin; i++) {
            listPaginas.value.push(i);
        }
    } else {
        for (let i = 1; i <= totalPages.value; i++) {
            listPaginas.value.push(i);
        }
    }
};

const current_page = ref(props.currentPage);

const emits = defineEmits(["updatePage"]);

const irPagina = async (value) => {
    current_page.value = value;
    emits("updatePage", current_page.value);
    generarPaginas();
};
const cambiaPagina = async (value) => {
    const nueva_pagina = current_page.value + value;
    emits("updatePage", nueva_pagina);
    generarPaginas();
};

onMounted(() => {
    generarPaginas();
});
</script>
<template>
    <ul class="pagination float-right" :class="[paginationClass, $attrs.class]">
        <li
            class="page-item"
            :class="{
                disabled: current_page == 1,
            }"
        >
            <button
                class="page-link"
                :disabled="current_page == 1"
                @click.prevent="irPagina(1)"
            >
                <i class="fa fa-angle-double-left"></i>
            </button>
        </li>
        <li
            class="page-item"
            :class="{
                disabled: current_page == 1,
            }"
        >
            <button
                class="page-link"
                @click.prevent="cambiaPagina(-1)"
                :disabled="current_page == 1"
            >
                <i class="fa fa-angle-left"></i>
            </button>
        </li>
        <li
            class="page-item"
            v-for="item in listPaginas"
            :class="{
                active: current_page == item,
            }"
        >
            <a class="page-link" href="#" @click.prevent="irPagina(item)">{{
                item
            }}</a>
        </li>
        <li
            class="page-item"
            :class="{
                disabled: current_page == totalPages,
            }"
        >
            <button
                class="page-link"
                @click.prevent="cambiaPagina(1)"
                :disabled="current_page == totalPages"
            >
                <i class="fa fa-angle-right"></i>
            </button>
        </li>
        <li
            class="page-item"
            :class="{
                disabled: current_page == totalPages,
            }"
        >
            <button
                class="page-link"
                @click.prevent="irPagina(totalPages)"
                :disabled="current_page == totalPages"
            >
                <i class="fa fa-angle-double-right"></i>
            </button>
        </li>
    </ul>
</template>

<style scoped>
.pagination {
    flex-wrap: wrap;
}
.page-item.disabled,
.page-item.disabled,
.page-item a:disabled,
.page-item button:disabled {
    cursor: not-allowed;
    opacity: 70%;
    font-weight: 500;
    color: rgb(70, 70, 70);
}

.page-item a,
.page-item button {
    box-shadow: none;
    font-weight: 500;
    color: rgb(70, 70, 70);
}

.page-item button.page-link {
    border: none;
}
</style>
