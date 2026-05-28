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

import * as turf from "@turf/turf";

import { onMounted, ref } from "vue";

const props = defineProps({
    latitud: {
        type: Number,
        default: null,
    },

    longitud: {
        type: Number,
        default: null,
    },

    zoom: {
        type: Number,
        default: 16,
    },
    segmentacion_zona_id: {
        type: Number,
    },
    /**
     * ZONAS ASIGNADAS
     */
    segmentaciones: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits([
    "update:latitud",
    "update:longitud",
    "update:segmentacion_zona_id",
]);
console.log(props.segmentacion_zona_id);

const mapa = ref(null);

const dentroZona = ref(true);

let map = null;

let marker = null;

let zonasLayer = null;

let ultimaPosicionValida = null;

/**
 * DIBUJAR SEGMENTACIONES
 */
const dibujarSegmentaciones = () => {
    zonasLayer = new L.FeatureGroup();

    map.addLayer(zonasLayer);

    props.segmentaciones.forEach((segmentacion) => {
        if (!segmentacion.segmentacion) return;

        segmentacion.segmentacion.forEach((area) => {
            const polygon = L.polygon(
                area.coordenadas.map((p) => [Number(p.lat), Number(p.lng)]),
                {
                    color: segmentacion.color || "#3388ff",

                    fillColor: segmentacion.color || "#3388ff",

                    fillOpacity: 0.2,
                },
            );

            const bounds = polygon.getBounds();

            const topCenter = bounds.getNorth();

            const tooltipMarker = L.marker(
                [topCenter, bounds.getCenter().lng],
                {
                    opacity: 0,
                },
            ).addTo(zonasLayer);

            tooltipMarker.bindTooltip(segmentacion.zona || "Zona", {
                permanent: true,
                direction: "top",
                offset: [0, 40],
                className: "tooltip-zona",
            });

            tooltipMarker.openTooltip();

            zonasLayer.addLayer(polygon);
        });
    });
};

/**
 * VALIDAR SI ESTA DENTRO
 */
const validarUbicacion = (lat, lng) => {
    const point = turf.point([lng, lat]);

    let resultado = {
        valido: false,
        segmentacion_zona_id: null,
        zona: null,
    };

    props.segmentaciones.forEach((segmentacion) => {
        if (!segmentacion.segmentacion) return;
        segmentacion.segmentacion.forEach((area) => {
            const coords = area.coordenadas.map((p) => [
                Number(p.lng),
                Number(p.lat),
            ]);

            coords.push(coords[0]);

            const polygon = turf.polygon([coords]);

            // BUFFER 30 METROS
            const bufferedPolygon = turf.buffer(polygon, 0.03, {
                units: "kilometers",
            });

            if (turf.booleanPointInPolygon(point, bufferedPolygon)) {
                resultado = {
                    valido: true,
                    segmentacion_zona_id: segmentacion.id,
                    zona: segmentacion.zona,
                };
            }
        });
    });

    dentroZona.value = resultado.valido;

    console.log("RESS");
    console.log(resultado);
    return resultado;
};

/**
 * MOVER MAPA
 */
const moverMapa = (lat, lng) => {
    map.setView([lat, lng], props.zoom);
};

/**
 * ACTUALIZAR UBICACION
 */
const actualizarUbicacion = (lat, lng) => {
    const resultado = validarUbicacion(lat, lng);

    // SIN ZONAS
    if (props.segmentaciones.length <= 0) {
        dentroZona.value = true;
    }

    // FUERA DE ZONA
    if (!resultado.valido && props.segmentaciones.length > 0) {
        if (ultimaPosicionValida) {
            marker.setLatLng(ultimaPosicionValida);
        }

        dentroZona.value = false;

        return;
    }

    dentroZona.value = true;

    ultimaPosicionValida = [lat, lng];

    crearMarker(lat, lng);

    emit("update:latitud", lat);

    emit("update:longitud", lng);

    emit("update:segmentacion_zona_id", resultado.segmentacion_zona_id);

    console.log("Zona:", resultado.zona);
};

/**
 * GEOLOCALIZACION
 */
const getUbicacion = () => {
    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;

            const lng = position.coords.longitude;

            actualizarUbicacion(lat, lng);

            moverMapa(lat, lng);
        },

        (error) => {
            console.log(error);

            // FALLBACK
            const lat = props.latitud ?? null;

            const lng = props.longitud ?? null;

            actualizarUbicacion(lat, lng);

            moverMapa(lat, lng);
        },
    );
};

/**
 * RESET
 */
const resetPosicion = () => {
    getUbicacion();
};

const crearMarker = (lat, lng) => {
    if (marker) {
        marker.setLatLng([lat, lng]);

        return;
    }

    marker = L.marker([lat, lng], {
        draggable: true,
    }).addTo(map);

    /**
     * DRAG MARKER
     */
    marker.on("dragend", () => {
        const position = marker.getLatLng();

        actualizarUbicacion(position.lat, position.lng);
    });
};

onMounted(() => {
    /**
     * COORDENADAS INICIALES
     */
    const lat = props.latitud ?? -16.491539;

    const lng = props.longitud ?? -68.144165;

    /**
     * MAPA
     */
    map = L.map(mapa.value).setView([lat, lng], props.zoom);

    /**
     * TILES
     */
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap",
    }).addTo(map);

    /**
     * DIBUJAR ZONAS
     */
    dibujarSegmentaciones();

    /**
     * SI HAY LAT/LNG
     * MODO EDICION
     */
    if (props.latitud !== null && props.longitud !== null) {
        actualizarUbicacion(props.latitud, props.longitud);

        moverMapa(props.latitud, props.longitud);
    } else {
        /**
         * NUEVO REGISTRO
         * GPS AUTOMATICO
         */
        getUbicacion();
    }

    /**
     * CLICK MAPA
     */
    map.on("click", (e) => {
        const { lat, lng } = e.latlng;
        actualizarUbicacion(lat, lng);
    });
});
</script>

<template>
    <div class="row">
        <div class="col-12">
            <div v-if="!dentroZona" class="alert alert-danger py-2 text-white">
                La ubicación está fuera de la zona permitida.
            </div>

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
            <div ref="mapa" class="mapa"></div>
        </div>
    </div>
</template>

<style scoped>
.mapa {
    width: 100%;
    height: 400px;
}

:deep(.tooltip-zona) {
    background: rgba(255, 255, 255, 0.95);
    border: none;
    border-radius: 8px;

    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);

    color: #222;

    font-size: 12px;
    font-weight: 700;

    padding: 4px 10px;

    text-transform: uppercase;

    letter-spacing: 0.5px;
}

:deep(.tooltip-zona::before) {
    display: none;
}
</style>
