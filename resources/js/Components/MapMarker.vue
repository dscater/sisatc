<script setup>
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import markerIcon2x from "leaflet/dist/images/marker-icon-2x.png";
import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";

delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

import { onMounted, ref } from "vue";

const props = defineProps({
    latitud: {
        type: Number,
        default: -16.125102,
    },
    longitud: {
        type: Number,
        default: -67.196268,
    },
    zoom: {
        type: Number,
        default: 14,
    },
});

const emit = defineEmits(["update:latitud", "update:longitud"]);

const mapa = ref(null);

let map = null;
let marker = null;

const getUbicacion = () => {
    navigator.geolocation.getCurrentPosition(
        (position) => {
            // console.log(position);
            actualizarUbicacion(
                position.coords.latitude,
                position.coords.longitude,
            );

            moverMapa(position.coords.latitude, position.coords.longitude);
        },

        (error) => {
            if (error.code == 1) {
                alert("Primero debe dar permisos para acceder a su ubicación");
            }
            console.log(error);
        },
    );
};

const moverMapa = (lat, lon) => {
    map.setView([lat, lon], props.zoom);
};

const resetPosicion = () => {
    actualizarUbicacion(-16.125102, -67.196268);
    moverMapa(-16.125102, -67.196268);
};

const actualizarUbicacion = (lat, lng) => {
    marker.setLatLng([lat, lng]);

    emit("update:latitud", lat);
    emit("update:longitud", lng);
};

onMounted(() => {
    map = L.map(mapa.value).setView(
        [props.latitud, props.longitud],
        props.zoom,
    );

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap",
    }).addTo(map);

    marker = L.marker([props.latitud, props.longitud], {
        draggable: true,
    }).addTo(map);

    // CLICK EN MAPA
    map.on("click", (e) => {
        const { lat, lng } = e.latlng;

        actualizarUbicacion(lat, lng);
    });

    // ARRASTRAR MARCADOR
    marker.on("dragend", (e) => {
        const position = marker.getLatLng();

        actualizarUbicacion(position.lat, position.lng);
    });
});
</script>

<template>
    <div class="row">
        <div class="col-12">
            <button
                type="button"
                class="btn btn-outline-success btn-sm text-xs"
                @click.prevent="getUbicacion"
            >
                Usar mi ubicación <i class="fa fa-map-marker-alt"></i>
            </button>
            <button
                class="btn btn-light btn-sm text-xs ms-1"
                type="button"
                @click.prevent="resetPosicion"
            >
                <i class="fa fa-sync"></i>
            </button>
            <div ref="mapa" class="mapa"></div>
        </div>
    </div>
</template>

<style scoped>
.mapa {
    width: 100%;
    height: 400px;
}
</style>
