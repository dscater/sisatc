<script setup>
import { onBeforeUnmount, onMounted, ref } from "vue";
const props = defineProps({
    imagenes: {
        type: Array,
        default: [],
    },
    muestra_pc: {
        type: Boolean,
        default: true,
    },
    height: {
        type: String,
        default: "200px",
    },
    interval: {
        type: Number,
        default: -1,
    },
});

const index_img = ref(0);

const actualizaImagen = (mueve) => {
    index_img.value = index_img.value + mueve;
    if (index_img.value < 0) {
        index_img.value = props.imagenes.length - 1;
    }
    if (index_img.value > props.imagenes.length - 1) {
        index_img.value = 0;
    }
    verificaInterval();
};

const irImagen = (index) => {
    index_img.value = index;
    verificaInterval();
};

const verificaInterval = () => {
    if (intervalSlider.value && props.interval > 1000) {
        clearInterval(intervalSlider.value);
        iniciaInterval();
    }
};
const detieneInterval = () => {
    clearInterval(intervalSlider.value);
};

const contenedorSlider = ref(null);
const isFullscreen = ref(false);
const pantallaCompleta = () => {
    // pantalla completa
    const contenedor = contenedorSlider.value;
    // Verificamos si la API de pantalla completa está disponible
    if (contenedor) {
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else {
            contenedor.requestFullscreen().catch((err) => {
                console.log(
                    `Error al intentar entrar en pantalla completa: ${err.message}`
                );
            });
        }
    }
};

// 🔹 Detectar cambios de pantalla completa
const handleFullscreenChange = () => {
    isFullscreen.value = !!document.fullscreenElement;
};

const intervalSlider = ref(null);
const iniciaInterval = () => {
    intervalSlider.value = setInterval(() => {
        index_img.value = (index_img.value + 1) % props.imagenes.length;
    }, props.interval);
};
onMounted(() => {
    document.addEventListener("fullscreenchange", handleFullscreenChange);
    if (props.interval > 1000) {
        iniciaInterval();
    }
});

onBeforeUnmount(() => {
    document.removeEventListener("fullscreenchange", handleFullscreenChange);
});
</script>
<template>
    <div class="contenendor_principal_slider" ref="contenedorSlider">
        <span
            class="icon-left"
            v-if="imagenes.length > 1"
            @click="actualizaImagen(-1)"
            ><i class="fa fa-angle-left"></i
        ></span>
        <span
            class="icon-right"
            v-if="imagenes.length > 1"
            @click="actualizaImagen(1)"
            ><i class="fa fa-angle-right"></i
        ></span>
        <span
            class="pantalla_completa"
            @click="pantallaCompleta"
            v-if="props.muestra_pc"
            ><i class="fa fa-expand"></i
        ></span>
        <div class="contenedor_sliders">
            <transition name="fade" mode="out-in">
                <div
                    class="slider"
                    :key="index_img"
                    :style="{
                        backgroundImage: `url('${imagenes[index_img].url_imagen}')`,
                        height: isFullscreen ? '100vh' : height,
                    }"
                    @mouseleave="verificaInterval"
                    @mouseenter="detieneInterval"
                >
                    <img :src="imagenes[index_img].url_imagen" alt="Imagen" />
                    <div
                        class="descripcion"
                        v-if="imagenes[index_img].html"
                        v-html="imagenes[index_img].html"
                    ></div>
                </div>
            </transition>
        </div>
        <div class="contenedor_puntos" v-if="imagenes.length > 1">
            <span
                class="punto"
                v-for="(item, index) in imagenes"
                @click="irImagen(index)"
                ><i
                    class="fa-circle"
                    :class="[index == index_img ? 'fa active' : 'far']"
                ></i
            ></span>
        </div>
    </div>
</template>
<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.contenendor_principal_slider {
    overflow: hidden;
    width: 100%;
    margin: auto;
    position: relative;
    background-color: black;
}

.contenendor_principal_slider .icon-left,
.contenendor_principal_slider .icon-right {
    cursor: pointer;
    position: absolute;
    top: 50%;
    background-color: rgba(219, 219, 219, 0.616);
    padding: 8px 13px;
    border-radius: 50%;
    transition: all 0.3s;
    z-index: 100;
}

.contenendor_principal_slider .icon-left:hover,
.contenendor_principal_slider .icon-right:hover {
    background-color: rgba(255, 255, 255, 0.767);
    color: gray;
}
.contenedor_sliders .slider {
    width: 100%;
    display: flex;
}

.contenedor_sliders .slider .descripcion {
    color: white;
    position: absolute;
    bottom: 23px;
    background-color: rgba(0, 0, 0, 0.753);
    z-index: 20;
    width: 100%;
    font-size: 0.9rem;
}

.contenedor_puntos {
    position: absolute;
    bottom: 0px;
    width: 100%;
    text-align: center;
    z-index: 11;
}

.contenedor_puntos .punto {
    cursor: pointer;
    margin: 0px 3px;
}

.contenendor_principal_slider .icon-left {
    left: 10px;
}

.contenendor_principal_slider .icon-right {
    right: 10px;
}

.contenendor_principal_slider .pantalla_completa {
    z-index: 20;
    cursor: pointer;
    position: absolute;
    top: 0px;
    right: 0px;
    background-color: rgba(219, 219, 219, 0.616);
    padding: 5px;
    font-size: 1rem;
}

.fa-circle {
    z-index: 1000;
}
.fa-circle.active {
    color: rgb(255, 255, 255) !important;
}

/* Animaciones de la transición */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease-in-out;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}

.contenendor_principal_slider {
    border-radius: 6px 6px 0px 0px;
}

.contenedor_sliders .slider::before {
    content: "";
    z-index: -1;
    position: absolute;
    inset: 0;
    background: inherit; /* usa la misma imagen de fondo */
    filter: blur(20px) brightness(0.6); /* efecto difuminado y oscurecido */
    transform: scale(1.2); /* expande un poco para cubrir bordes */
    z-index: 0;
}
.contenedor_sliders .slider img,
.contenedor_sliders .slider video {
    z-index: 1;
    margin: 0 auto;
    border-radius: 0;
}

/* Estilo para el contenedor en pantalla completa */
.contenendor_principal_slider:fullscreen .contenedor_sliders {
    height: 100%;
}
.contenendor_principal_slider:fullscreen .contenedor_sliders .slider {
    display: block;
    background-position: center;
    background-size: contain;
    background-repeat: no-repeat;
}

.contenendor_principal_slider:fullscreen .contenedor_sliders .slider img {
    display: none;
}

.contenendor_principal_slider:fullscreen .contenedor_sliders .slider::before {
    filter: none; /* efecto difuminado y oscurecido */
    z-index: 3;
}

/* Estilo para los botones cuando estamos en pantalla completa */
.contenendor_principal_slider:fullscreen .icon-left,
.contenendor_principal_slider:fullscreen .icon-right,
.contenendor_principal_slider:fullscreen .pantalla_completa {
    background-color: rgba(
        0,
        0,
        0,
        0.651
    ); /* Fondo más oscuro para los controles */
    color: white;
}
</style>
