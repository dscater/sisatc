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
    /**
     * CLIENTES
     */
    clientes: {
        type: Array,
        default: () => [],
    },

    /**
     * ZONAS
     */
    zonas: {
        type: Array,
        default: () => [],
    },

    zoom: {
        type: Number,
        default: 13,
    },
});

const mapa = ref(null);

let map = null;

const iniciarMapa = () => {
    /**
     * MAPA
     */
    map = L.map(mapa.value).setView([-16.4957, -68.1336], props.zoom);

    /**
     * TILES
     */
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap",
    }).addTo(map);

    /**
     * BOUNDS
     */
    const bounds = L.latLngBounds();

    /**
     * =========================================
     * ZONAS
     * =========================================
     */
    props.zonas.forEach((zona) => {
        // VALIDAR
        if (!zona.segmentacion || !Array.isArray(zona.segmentacion)) {
            return;
        }

        /**
         * UNA ZONA PUEDE TENER MUCHOS POLIGONOS
         */
        zona.segmentacion.forEach((segmento) => {
            if (!segmento.coordenadas || !Array.isArray(segmento.coordenadas)) {
                return;
            }

            const coordenadas = segmento.coordenadas.map((p) => [
                Number(p.lat),
                Number(p.lng),
            ]);

            /**
             * POLIGONO
             */
            const polygon = L.polygon(coordenadas, {
                color: zona.color || "#3388ff",
                fillColor: zona.color || "#3388ff",
                fillOpacity: 0.35,
                weight: 2,
            }).addTo(map);

            /**
             * TOOLTIP
             */
            polygon.bindTooltip(zona.zona || "Zona", {
                permanent: true,
                direction: "center",
                className: "tooltip-zona",
            });

            /**
             * POPUP
             */
            polygon.bindPopup(`
                <div>
                    <strong>${zona.zona}</strong>
                </div>
            `);

            /**
             * BOUNDS
             */
            bounds.extend(polygon.getBounds());
        });
    });

    /**
     * =========================================
     * CLIENTES
     * =========================================
     */
    props.clientes.forEach((cliente) => {
        if (!cliente.latitud || !cliente.longitud) {
            return;
        }

        const marker = L.marker([
            Number(cliente.latitud),
            Number(cliente.longitud),
        ]).addTo(map);

        /**
         * TOOLTIP
         */
        marker.bindTooltip(cliente.nombre || "Cliente", {
            direction: "top",
        });

        /**
         * POPUP
         */
        marker.bindPopup(`
            <div>
                <strong>${cliente.nombre || "Cliente"}</strong>
            </div>
        `);

        /**
         * BOUNDS
         */
        bounds.extend([Number(cliente.latitud), Number(cliente.longitud)]);
    });

    /**
     * AJUSTAR MAPA
     */
    if (bounds.isValid()) {
        map.fitBounds(bounds, {
            padding: [40, 40],
        });
    }
};

onMounted(() => {
    iniciarMapa();
});
</script>

<template>
    <div ref="mapa" class="mapa"></div>
</template>

<style scoped>
.mapa {
    width: 100%;
    height: 40vh;
}

:deep(.tooltip-zona) {
    background: white;
    border: 1px solid #ccc;
    color: #333;
    font-weight: bold;
    box-shadow: none;
}
</style>
