<script setup>
import { watch, ref, onMounted, onBeforeUnmount } from "vue";
const props = defineProps({
    open_modal: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: "",
    },
    headerClass: {
        type: String,
        default: "",
    },
    bodyClass: {
        type: String,
        default: "",
    },
    footerClass: {
        type: String,
        default: "",
    },
    closeEsc: {
        type: Boolean,
        default: false,
    },
    header: {
        type: Boolean,
        default: true,
    },
    maxHeightBody: {
        type: String,
        default: "auto",
    },
    footer: {
        type: Boolean,
        default: true,
    },
});
const emits = defineEmits(["close"]);
const show = ref(props.open_modal);
watch(
    () => props.open_modal,
    (newValue) => {
        show.value = newValue;
        if (show.value) {
            document
                .getElementsByTagName("body")[0]
                .classList.add("modal-open");
            window.addEventListener("keyup", handleKeyup);
        } else {
            document
                .getElementsByTagName("body")[0]
                .classList.remove("modal-open");
            window.removeEventListener("keyup", handleKeyup);
        }
    },
);

const miModal = ref(null);

const handleKeyup = (e) => {
    if (e.key === "Escape" && show.value) {
        if (props.closeEsc) {
            emits("close");
        } else {
            triggerShake();
        }
    }
};

const isShaking = ref(false);

const clickModal = (e) => {
    if (e.target == miModal.value) {
        if (props.closeEsc) {
            emits("close");
        } else {
            triggerShake();
        }
    }
};

const triggerShake = () => {
    if (!props.closeEsc) {
        if (!isShaking.value) {
            isShaking.value = true;
            setTimeout(() => {
                isShaking.value = false;
            }, 500);
        }
    }
};

onMounted(() => {
    if (show.value) {
        document.getElementsByTagName("body")[0].classList.add("modal-open");
        window.addEventListener("keyup", handleKeyup);
    } else {
        document.getElementsByTagName("body")[0].classList.remove("modal-open");
        window.removeEventListener("keyup", handleKeyup);
    }
});

onBeforeUnmount(() => {
    window.removeEventListener("keyup", handleKeyup);
});
</script>

<template>
    <div
        class="modal fade"
        :class="[show ? 'show' : '', $attrs.class]"
        :style="{
            display: show ? 'block' : 'none',
        }"
        ref="miModal"
        @click="clickModal"
    >
        <div class="modal-dialog" :class="[size]">
            <div class="modal-content" :class="{ shake: isShaking }">
                <div v-if="header" class="modal-header" :class="[headerClass]">
                    <slot name="header"></slot>
                </div>
                <div
                    class="modal-body"
                    :class="[bodyClass]"
                    :style="{ maxHeight: maxHeightBody }"
                >
                    <slot name="body"></slot>
                </div>
                <div v-if="footer" class="modal-footer" :class="[footerClass]">
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
@keyframes shake {
    0% {
        transform: translateX(0);
    }
    25% {
        transform: translateX(-10px);
    }
    50% {
        transform: translateX(10px);
    }
    75% {
        transform: translateX(-10px);
    }
    100% {
        transform: translateX(0);
    }
}

.shake {
    animation: shake 0.5s ease-in-out;
}

.modal-body {
    overflow: auto;
}
</style>
