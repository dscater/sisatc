<script setup>
/**
 * TENER INSTALADO
 * npm install hls.js
 */
import Hls from "hls.js";
import { onBeforeUnmount, onMounted, ref, watch } from "vue";
const props = defineProps({
    url_video: {
        type: String,
        default: "",
    },
});

const videoRef = ref(null);

watch(
    () => props.url_video,
    (newValue) => {
        console.log(newValue);
        videoSrc.value = newValue + "?v=" + Date.now();
        initHls(videoSrc.value);
    },
);
let hls;
// Ruta del video .m3u8
const videoSrc = ref(props.url_video);
onMounted(() => {
    initHls(videoSrc.value + "?v=" + Date.now());
});
function initHls(src) {
    const video = videoRef.value;
    if (!video) return;

    if (hls) {
        hls.destroy();
        hls = null;
    }

    if (Hls.isSupported()) {
        hls = new Hls();
        hls.loadSource(src);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => video.play().catch(() => {}));
    } else if (video.canPlayType("application/vnd.apple.mpegurl")) {
        video.src = src;
        video.addEventListener("loadedmetadata", () =>
            video.play().catch(() => {}),
        );
    }
}

onBeforeUnmount(() => {
    if (hls) {
        hls.destroy();
        hls = null;
    }
});
</script>
<template>
    <video
        ref="videoRef"
        autoplay
        muted
        loop
        playsinline
        preload="metadata"
    ></video>
</template>
