<script setup>
import { onMounted, ref, watch, computed } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";
// import { useAppStore } from "@/stores/aplicacion/appStore";
// const appStore = useAppStore();
const props = defineProps({
    label: {
        type: String,
        default: "Label",
    },
    icon: {
        type: String,
        default: "mdi-home-outline",
    },
    ruta: {
        type: String,
        default: "/",
    },
    method: {
        type: String,
        default: "GET",
    },
    arrayRutaClassActive: {
        type: Array,
        default: [],
    },
});

const route_current = ref("");
const arrRutasActive = ref(props.arrayRutaClassActive);
const link = ref(null);

watch(
    () => props.arrayRutaClassActive,
    (newVal) => {
        arrRutasActive.value = newVal;
    },
);

router.on("navigate", (event) => {
    route_current.value = route().current();
});

const ejecutaPost = () => {
    router.post(route(props.ruta));
};

const classActive = computed(() => {
    if (arrRutasActive.value.length > 0) {
        return arrRutasActive.value.includes(route_current.value)
            ? "active"
            : "";
    }
    return route_current.value == props.ruta ? "active" : "";
});

const emits = defineEmits(["onClick"]);
onMounted(() => {
    route_current.value = route().current();
    link.value.addEventListener("click", function () {
        // appStore.startLoading();
        emits("onClick");
    });
});
</script>
<template>
    <li
        class="nav-item"
        v-if="method.toLowerCase() == 'get'"
        ref="link"
        :class="[$attrs.class]"
    >
        <Link
            v-if="ruta != route_current"
            :href="ruta ? route(ruta) : '/'"
            class="nav-link"
            :class="[classActive ?? '']"
        >
            <i class="nav-icon" :class="icon ? icon : 'fa-th'"></i>
            <p>
                {{ label }}
            </p>
        </Link>
        <div v-else class="nav-link" :class="[classActive ?? '']">
            <i class="nav-icon" :class="icon ? icon : 'fa-th'"></i>
            <p>
                {{ label }}
            </p>
        </div>
    </li>
    <li class="nav-item" v-else>
        <a href="#" class="nav-link" @click.prevent="ejecutaPost()" ref="link">
            <i class="nav-icon" :class="icon ? icon : 'fa-th'"></i>
            <p>
                {{ label }}
            </p>
        </a>
    </li>
</template>
