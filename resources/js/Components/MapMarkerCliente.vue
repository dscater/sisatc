<script setup>
import L from "leaflet";
import "leaflet/dist/leaflet.css";

import { onMounted, ref } from "vue";

const props = defineProps({
    nombreCliente: {
        type: String,
        default: "",
    },

    latitud: {
        type: Number,
        default: -16.125102,
    },

    longitud: {
        type: Number,
        default: -67.196268,
    },

    areas: {
        type: Array,
        default: () => [],
    },

    zoom: {
        type: Number,
        default: 14,
    },

    readonly: {
        type: Boolean,
        default: false,
    },
    muestraNombre: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["update:latitud", "update:longitud"]);

const mapa = ref(null);

let map = null;
let marker = null;
let areasItems = null;

/**
 * OBTENER UBICACION ACTUAL
 */
const getUbicacion = () => {
    navigator.geolocation.getCurrentPosition(
        (position) => {
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

/**
 * MOVER MAPA
 */
const moverMapa = (lat, lon) => {
    map.setView([lat, lon], props.zoom);
};

/**
 * RESETEAR POSICION
 */
const resetPosicion = () => {
    actualizarUbicacion(-16.125102, -67.196268);

    moverMapa(-16.125102, -67.196268);
};

/**
 * ACTUALIZAR UBICACION
 */
const actualizarUbicacion = (lat, lng) => {
    marker.setLatLng([lat, lng]);

    emit("update:latitud", lat);
    emit("update:longitud", lng);
};

onMounted(() => {
    /**
     * MAPA
     */
    map = L.map(mapa.value).setView(
        [props.latitud, props.longitud],
        props.zoom,
    );

    /**
     * TILES
     */
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap",
    }).addTo(map);

    /**
     * MARCADOR
     */
    marker = L.marker([props.latitud, props.longitud], {
        draggable: !props.readonly,
    }).addTo(map);

    /**
     * POPUP
     */
    marker.bindPopup(props.nombreCliente).openPopup();

    /**
     * SOLO SI ES EDITABLE
     */
    if (!props.readonly) {
        /**
         * CLICK EN MAPA
         */
        map.on("click", (e) => {
            const { lat, lng } = e.latlng;

            actualizarUbicacion(lat, lng);
        });

        /**
         * ARRASTRAR MARCADOR
         */
        marker.on("dragend", () => {
            const position = marker.getLatLng();

            actualizarUbicacion(position.lat, position.lng);
        });
    }

    /**
     * AREAS
     */
    areasItems = new L.FeatureGroup();

    map.addLayer(areasItems);

    if (props.areas.length > 0) {
        props.areas.forEach((area) => {
            // VALIDAR
            if (!area.coordenadas || !Array.isArray(area.coordenadas)) {
                return;
            }

            const coordenadas = area.coordenadas.map((p) => [
                Number(p.lat),
                Number(p.lng),
            ]);

            const polygon = L.polygon(coordenadas, {
                color: area.color || "#3388ff",
                fillColor: area.color || "#3388ff",
                fillOpacity: 0.4,
            });

            // OPCIONAL
            if (area.nombre) {
                polygon.bindTooltip(area.nombre, {
                    permanent: true,
                    direction: "center",
                });
            }

            areasItems.addLayer(polygon);
        });
    }

    /**
     * AJUSTAR VISTA
     */
    const bounds = L.latLngBounds();

    // MARCADOR
    bounds.extend([props.latitud, props.longitud]);

    // AREAS
    if (areasItems.getLayers().length > 0) {
        bounds.extend(areasItems.getBounds());
    }

    map.fitBounds(bounds, {
        padding: [30, 30],
    });
});
</script>

<template>
    <div class="row">
        <div class="col-12">
            <!-- NOMBRE CLIENTE -->
            <div class="mb-2 fw-bold" v-if="muestraNombre">
                Cliente: {{ nombreCliente }}
            </div>

            <!-- BOTONES -->
            <template v-if="!readonly">
                <button
                    type="button"
                    class="btn btn-outline-success btn-sm text-xs"
                    @click.prevent="getUbicacion"
                >
                    Usar mi ubicación
                    <i class="fa fa-map-marker-alt"></i>
                </button>

                <button
                    class="btn btn-light btn-sm text-xs ms-1"
                    type="button"
                    @click.prevent="resetPosicion"
                >
                    <i class="fa fa-sync"></i>
                </button>
            </template>

            <!-- MAPA -->
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
