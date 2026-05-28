<script setup>
import { onBeforeUnmount, onMounted } from "vue";

onMounted(() => {
    document.getElementsByTagName("body")[0].classList.add("login");
});
onBeforeUnmount(() => {
    document.getElementsByTagName("body")[0].classList.remove("login");
});
</script>
<template>
    <div class="principal_login">
        <div
            class="content-login container w-100 h-100 d-flex items-center align-items-center justify-content-center"
        >
            <slot />
        </div>
    </div>
</template>

<style scoped>
.principal_login {
    width: 100%;
    height: 100%;
    background-color: var(--bg1);
}
.content-login {
    min-height: 100vh;
}
</style>
