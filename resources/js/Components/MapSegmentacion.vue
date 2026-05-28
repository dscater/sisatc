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

import "leaflet-draw/dist/leaflet.draw.css";
import "leaflet-draw";

import { onMounted, ref, watch } from "vue";

const props = defineProps({
    /**
     * AREAS EDITABLES
     */
    areas: {
        type: Array,
        default: () => [],
    },

    /**
     * SEGMENTACIONES BLOQUEADAS
     * OBJETO COMPLETO
     */
    areasBloqueadas: {
        type: Array,
        default: () => [],
    },

    color: {
        type: String,
        default: "#3388ff",
    },

    zoom: {
        type: Number,
        default: 16,
    },

    latitud: {
        type: Number,
        default: null,
    },

    longitud: {
        type: Number,
        default: null,
    },
    editable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["update:areas"]);

const mapa = ref(null);

let map = null;

// EDITABLES
let drawnItems = null;

// SOLO VISUALIZACION
let blockedItems = null;

let drawControl = null;

/**
 * OBTENER DATOS
 */
const obtenerDatos = () => {
    const nuevasAreas = [];

    drawnItems.eachLayer((layer) => {
        if (layer instanceof L.Polygon) {
            const coordenadas = layer.getLatLngs()[0].map((p) => ({
                lat: p.lat,
                lng: p.lng,
            }));

            nuevasAreas.push({
                color: layer.options.color,
                coordenadas,
            });
        }
    });

    emit("update:areas", nuevasAreas);
};

/**
 * CONTROLES
 */
const crearControles = () => {
    if (drawControl) {
        map.removeControl(drawControl);
    }

    drawControl = new L.Control.Draw({
        edit: {
            featureGroup: drawnItems,
        },

        draw: {
            polygon: {
                shapeOptions: {
                    color: props.color,
                    fillColor: props.color,
                    fillOpacity: 0.4,
                },
            },

            rectangle: false,
            circle: false,
            marker: false,
            polyline: false,
            circlemarker: false,
        },
    });

    map.addControl(drawControl);
};

/**
 * ACTUALIZAR COLOR
 */
const actualizarColorDrawer = () => {
    if (!drawControl) return;

    const polygonHandler = drawControl._toolbars.draw._modes.polygon.handler;

    polygonHandler.setOptions({
        shapeOptions: {
            color: props.color,
            fillColor: props.color,
            fillOpacity: 0.4,
        },
    });
};

watch(
    () => props.color,
    () => {
        actualizarColorDrawer();

        drawnItems.eachLayer((layer) => {
            layer.setStyle({
                color: props.color,
                fillColor: props.color,
            });
        });

        obtenerDatos();
    },
);

const iniciarMapa = (lat, lng) => {
    map = L.map(mapa.value).setView([lat, lng], props.zoom);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap",
    }).addTo(map);

    /**
     * TILES
     */
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap",
    }).addTo(map);

    /**
     * GRUPOS
     */
    drawnItems = new L.FeatureGroup();

    blockedItems = new L.LayerGroup();

    map.addLayer(blockedItems);

    map.addLayer(drawnItems);

    /**
     * AREAS BLOQUEADAS
     */
    props.areasBloqueadas.forEach((segmentacion) => {
        if (!segmentacion.segmentacion) return;

        segmentacion.segmentacion.forEach((area) => {
            const polygon = L.polygon(
                area.coordenadas.map((p) => [Number(p.lat), Number(p.lng)]),
                {
                    color: segmentacion.color || "#999999",
                    fillColor: segmentacion.color || "#999999",

                    fillOpacity: 0.2,

                    // interactive: false,
                },
            );

            /**
             * TOOLTIP
             */
            polygon.bindTooltip(segmentacion.zona || "Zona", {
                permanent: true,
                direction: "center",
                className: "tooltip-zona",
            });

            /**
             * POPUP
             */
            polygon.bindPopup(`
                <div>
                    <strong>${segmentacion.zona}</strong>
                </div>
            `);

            blockedItems.addLayer(polygon);
        });
    });

    /**
     * AREAS EDITABLES
     */
    props.areas.forEach((area) => {
        const polygon = L.polygon(
            area.coordenadas.map((p) => [Number(p.lat), Number(p.lng)]),
            {
                color: area.color || props.color,
                fillColor: area.color || props.color,
                fillOpacity: 0.4,
            },
        );
        drawnItems.addLayer(polygon);
    });
    // ir a la ubicación del area
    if (drawnItems.getLayers().length > 0) {
        map.fitBounds(drawnItems.getBounds(), {
            padding: [20, 20],
        });
    }

    /**
     * CONTROLES
     */
    if (props.editable) {
        crearControles();
    }

    /**
     * CREAR
     */
    if (props.editable) {
        map.on(L.Draw.Event.CREATED, (event) => {
            const layer = event.layer;

            layer.setStyle({
                color: props.color,
                fillColor: props.color,
                fillOpacity: 0.4,
            });

            drawnItems.addLayer(layer);

            obtenerDatos();
        });

        /**
         * EDITAR
         */
        map.on(L.Draw.Event.EDITED, () => {
            obtenerDatos();
        });

        /**
         * ELIMINAR
         */
        map.on(L.Draw.Event.DELETED, () => {
            obtenerDatos();
        });
    }
};

onMounted(() => {
    // SI VIENEN COORDENADAS
    if (props.latitud && props.longitud) {
        iniciarMapa(props.latitud, props.longitud);
        return;
    }

    // GEOLOCALIZACION
    navigator.geolocation.getCurrentPosition(
        (position) => {
            iniciarMapa(position.coords.latitude, position.coords.longitude);
        },

        () => {
            // FALLBACK
            iniciarMapa(-16.491933, -68.143987);
        },
    );
});
</script>

<template>
    <div ref="mapa" class="mapa"></div>
</template>

<style scoped>
.mapa {
    width: 100%;
    height: 330px;
}

:deep(.tooltip-zona) {
    background: white;
    border: 1px solid #ccc;
    color: #333;
    font-weight: bold;
    box-shadow: none;
}
</style>
