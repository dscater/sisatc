import { onMounted, onUnmounted, ref, nextTick } from "vue";

const ancho_pantalla = ref(window.innerWidth);

export const useSideBar = () => {
    const cambioPantalla = () => {
        initClasesBody();
    };

    const initClasesBody = () => {
        ancho_pantalla.value = window.innerWidth;
        const body = document.querySelector("body");
        if (ancho_pantalla.value <= 990) {
            body.classList.add("sidebar-closed");
            body.classList.remove("sidebar-open");
            body.classList.add("sidebar-collapse");
        } else if (ancho_pantalla.value > 990 && ancho_pantalla.value < 1010) {
            body.classList.add("sidebar-collapse");
        }
    };

    const toggleSidebar = async () => {
        const body = document.querySelector("body");
        if (body) {
            if (ancho_pantalla.value > 1010) {
                if (body.classList.contains("sidebar-collapse")) {
                    // abrir
                    body.classList.remove("sidebar-collapse");
                } else {
                    body.classList.add("sidebar-collapse");
                }
            } else {
                if (body.classList.contains("sidebar-open")) {
                    body.classList.add("sidebar-collapse");
                    body.classList.add("sidebar-closed");
                    body.classList.remove("sidebar-open");
                } else {
                    // abrir
                    body.classList.remove("sidebar-collapse");
                    body.classList.remove("sidebar-closed");
                    body.classList.add("sidebar-open");
                }
            }
        }
    };

    const closeSidebar = () => {
        const body = document.querySelector("body");
        if (body) {
            if (ancho_pantalla.value <= 990) {
                if (body.classList.contains("sidebar-open")) {
                    body.classList.add("sidebar-collapse");
                    body.classList.add("sidebar-closed");
                    body.classList.remove("sidebar-open");
                }
            }

            nextTick(() => {
                verificarSubMenusOpen();
            });
        }
    };

    const verificarSubMenusOpen = () => {
        const subMenus = document.querySelectorAll(".sub-menu");
        subMenus.forEach((elem, index) => {
            if (!elem.classList.contains("active")) {
                elem.classList.remove("menu-is-opening");
                elem.classList.remove("menu-open");
                toggleSubMenuELem(elem, false);
            } else {
                elem.classList.add("menu-is-opening");
                elem.classList.add("menu-open");
                toggleSubMenuELem(elem, true);
            }
        });
    };

    const toggleSubMenuELem = (el, show) => {
        const subMenu = el.nextElementSibling;

        if (!subMenu) return;

        subMenu.style.overflow = "hidden";
        subMenu.style.transition = "max-height 0.3s ease";

        if (show) {
            subMenu.style.display = "block";

            // fuerza recálculo
            subMenu.offsetHeight;

            subMenu.style.maxHeight = subMenu.scrollHeight + "px";
        } else {
            subMenu.style.maxHeight = "0px";
        }
    };

    function handleClickOutside(event) {
        const toggleButton = document.querySelector(".toggleButton");
        const element = document.querySelector(".app-sidebar");
        if (element) {
            if (
                !element.contains(event.target) &&
                !toggleButton.contains(event.target)
            ) {
                closeSidebar();
            }
        }
    }

    onMounted(() => {
        initClasesBody();
        document.addEventListener("click", handleClickOutside);
        window.addEventListener("resize", cambioPantalla);
    });

    onUnmounted(() => {
        window.removeEventListener("resize", cambioPantalla);
        document.removeEventListener("click", handleClickOutside);
    });

    return {
        toggleSidebar,
        closeSidebar,
        toggleSubMenuELem,
    };
};
