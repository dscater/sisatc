import "./bootstrap";
// import "../css/app.css";//comentado para que no cargue los estilos por defecto y solo vuetify

// adminlte y bootstrap
import "bootstrap/dist/css/bootstrap.min.css";
import "admin-lte/dist/css/adminlte.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "admin-lte/dist/js/adminlte.min.js";

// css
import "./assets/css/all.min.css";
// import "./assets/css/adminlte.min.css";
import "./assets/css/botones.css";
import "./assets/css/backgroundYcolor.css";
import "./assets/css/fonts.css";
import "./assets/css/config.css";
import "./assets/css/form.css";
import "./assets/css/miTable.css"; // mi-table

// import "./assets/js/adminlte.min.js";

import { createApp, h } from "vue";
import { createInertiaApp, router } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

// sweetalert2
import Swal from "sweetalert2";
window.Swal = Swal;

// Pinia
import { createPinia } from "pinia";
const pinia = createPinia();

// Element-UI plus
// import ElementPlus from "element-plus";
// import "element-plus/dist/index.css";
import {
    ElSelect,
    ElOption,
    ElInput,
    ElSwitch,
    ElTooltip,
    ElCarousel,
    ElCarouselItem,
    ElSkeleton,
    ElSkeletonItem,
    ElRadio,
    ElRadioButton,
    ElRadioGroup,
    ElCheckbox,
} from "element-plus";

// Import CSS de cada componente individual
import "element-plus/es/components/select/style/css";
import "element-plus/es/components/option/style/css";
import "element-plus/es/components/input/style/css";
import "element-plus/es/components/radio/style/css";
import "element-plus/es/components/radio-button/style/css";
import "element-plus/es/components/checkbox/style/css";
import "element-plus/es/components/switch/style/css";
import "element-plus/es/components/tooltip/style/css";
import "element-plus/es/components/carousel/style/css";
import "element-plus/es/components/carousel-item/style/css";
import "element-plus/es/components/skeleton/style/css";
import "element-plus/es/components/skeleton-item/style/css";

// Default Layout
import App from "@/Layouts/App.vue";

// App
const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const page = resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        );
        page.then((module) => {
            module.default.layout = module.default.layout ?? App;
        });
        return page;
    },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(pinia);
        // .use(ElementPlus);

        vueApp.component("ElSelect", ElSelect);
        vueApp.component("ElOption", ElOption);
        vueApp.component("ElInput", ElInput);
        vueApp.component("ElSwitch", ElSwitch);
        vueApp.component("ElTooltip", ElTooltip);
        vueApp.component("ElCarousel", ElCarousel);
        vueApp.component("ElCarouselItem", ElCarouselItem);
        vueApp.component("ElSkeleton", ElSkeleton);
        vueApp.component("ElSkeletonItem", ElSkeletonItem);
        vueApp.component("ElRadio", ElRadio);
        vueApp.component("ElRadioButton", ElRadioButton);
        vueApp.component("ElRadioGroup", ElRadioGroup);
        vueApp.component("ElCheckbox", ElCheckbox);

        vueApp.mount(el);
        return vueApp;
    },
    progress: {
        color: "#aac13f",
    },
});
