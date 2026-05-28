<script setup>
import axios from "axios";
import MiPaginacion from "@/Components/MiPaginacion.vue";
import {
    ref,
    onMounted,
    onUpdated,
    onUnmounted,
    watch,
    nextTick,
    useSlots,
} from "vue";
import { debounce } from "lodash";
const props = defineProps({
    cols: {
        type: Array,
        default: [],
    },
    data: {
        type: Array,
        default: [],
    },
    conPaginacion: {
        type: Boolean,
        default: true,
    },
    api: {
        type: Boolean,
        default: false,
    },
    url: {
        type: String,
        default: "",
    },
    sinRegistros: {
        type: String,
        default: "No se encontrarón registros",
    },
    tableResponsive: {
        type: Boolean,
        default: false,
    },
    tableHeight: {
        type: [String, Number],
        default: null,
    },
    tableWidth: {
        type: [String, Number],
        default: null,
    },
    fixedHeader: {
        type: Boolean,
        default: false,
    },
    tableClass: {
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
    paginationClass: {
        type: String,
        default: "",
    },
    perPage: {
        type: Number,
        default: 5,
    },
    filterPage: {
        type: Array,
        default: [5, 10, 20, 30, 50, 100],
    },
    syncOrderBy: {
        default: null,
    },
    syncOrderAsc: {
        default: null,
    },
    search: {
        type: String,
        default: "",
    },
    multiSearch: {
        type: Object,
        default: null,
    },
    numPages: {
        type: Number,
        default: 7,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    textCargando: {
        type: String,
        default: "Cargando...",
    },
    delaySearch: {
        type: Number,
        default: 300,
    },
    // para listar los registros por cards - PENDIENTE
    card: {
        type: Boolean,
        default: false,
    },
});

const listCols = ref(props.cols);
const listData = ref(props.data);
const listItems = ref([]);
const textSinRegistros = ref(props.sinRegistros);
const error = ref(false);
const mensajeError = ref("");
const per_page = ref(props.perPage);
const orderBy = ref(props.syncOrderBy);
const orderAsc = ref(
    props.syncOrderAsc ? props.syncOrderAsc.toLowerCase() : null,
);
const total = ref(listData.value.length);
const currentPage = ref(1);
const filter_page = ref(props.filterPage);
const tSearch = ref(props.search);
const oMultiSearch = ref(props.multiSearch);
const aPaginas = ref([]);
const cDeRegistros = ref(1);
const cARegistros = ref(per_page.value);
const totalPages = ref(0);
const pLoading = ref(false);
const intervalSearch = ref(null);

watch(
    () => props.syncOrderBy,
    (newVal) => {
        orderBy.value = newVal;
        // console.log("watch 1");
        cargarDatos();
    },
);

watch(
    () => props.syncOrderAsc,
    (newVal) => {
        orderAsc.value = newVal ? newVal.toLowerCase() : null;
        // console.log("watch 2");
        cargarDatos();
    },
);

watch(
    () => props.loading,
    (newVal) => {
        // console.log("watch 3");
        pLoading.value = newVal;
    },
);

watch(
    () => props.multiSearch,
    (newVal) => {
        // console.log("watch 4");
        setLoading(true);
        clearInterval(intervalSearch.value);
        intervalSearch.value = setTimeout(() => {
            currentPage.value = 1;
            oMultiSearch.value = newVal;
            cargarDatos();
        }, props.delaySearch);
    },
    { deep: true },
);

watch(
    () => props.search,
    (newVal) => {
        // console.log("watch 5");
        setLoading(true);
        tSearch.value = newVal;
        clearInterval(intervalSearch.value);
        intervalSearch.value = setTimeout(() => {
            currentPage.value = 1;
            cargarDatos();
        }, props.delaySearch);
    },
);

watch(per_page, (newValue, oldValue) => {
    // console.log("watch 6");
    cambiaPerPage();
});

watch(
    () => props.data,
    (newVal) => {
        // console.log("watch 7");
        listData.value = newVal;
        total.value = listData.value.length;
        cargarDatos();
    },
);

watch(
    () => listItems.value,
    async (newVal) => {
        if (newVal.length > 0) {
            // alto del contenedor
            nextTick(() => {
                establecerAltoContenedor();
            });
        }
    },
);

// funcion para determinar si se cargaron todos los elementos
const esperarCargaElementos = () => {
    return new Promise((r) => window.requestAnimationFrame(r));
};

/**
 * *****************************************
 *  FUNCIONES PARA LA CARGA DE DATOS
 */

// funcion general para generar los datos que se muestran en la tabla
// ya sea via URL o el Listado pasado en propiedades
const cargarDatos = async () => {
    listItems.value = [];
    setLoading(true);
    if (props.api) {
        const resp = await apiRegistros();
        total.value = resp.total;
        listItems.value = resp.data;
        totalPages.value = resp.lastPage;
        muestraCantidadRegistros();
    } else {
        listItems.value = await generarDatosPorLista();
        // Actualiza los contadores de registros
        muestraCantidadRegistros();
    }
    setLoading(false);
};

// cargar registros por URL axios
const apiRegistros = async () => {
    try {
        let dataParams = {
            page: currentPage.value,
            currentPage: currentPage.value,
            perPage: per_page.value,
            search: tSearch.value,
            orderBy: orderBy.value,
            orderAsc: orderAsc.value,
        };
        if (oMultiSearch.value) {
            dataParams = {
                page: currentPage.value,
                currentPage: currentPage.value,
                perPage: per_page.value,
                ...oMultiSearch.value,
                orderBy: orderBy.value,
                orderAsc: orderAsc.value,
            };
        }
        const response = await axios(props.url, {
            params: dataParams,
        });

        return response.data;
    } catch (err) {
        setLoading(false);
        error.value = true;
        console.log(err);
        return null;
    }
};
// generar el listado de datos sin uso de una api
const generarDatosPorLista = async () => {
    return new Promise(async (resolve, reject) => {
        try {
            const page = parseInt(currentPage.value);
            const pageSize = parseInt(per_page.value);
            const vOrderBy = orderBy.value;
            const vOrderAsc = orderAsc.value;

            // Filtra el array por el valor de búsqueda en cualquier columna
            let filteredArray = await listData.value.filter((item) => {
                return Object.values(item).some((value) =>
                    value
                        .toString()
                        .toLowerCase()
                        .includes(tSearch.value.toLowerCase()),
                );
            });

            total.value = filteredArray.length;
            totalPages.value = Math.ceil(total.value / per_page.value);

            // Aplica el ordenamiento según orderBy y orderAsc
            if (vOrderAsc) {
                filteredArray = filteredArray.sort((a, b) => {
                    // Obtener la configuración de la columna por su key
                    let columnConfig = listCols.value.find(
                        (col) => col.key === vOrderBy,
                    );
                    if (!columnConfig) {
                        return 0;
                    }

                    // Obtener valores a comparar
                    let valA = getColumnValue(a, vOrderBy) ?? "";
                    let valB = getColumnValue(b, vOrderBy) ?? "";
                    // Si el tipo es Number, convertir los valores
                    if (
                        columnConfig.type &&
                        columnConfig.type.toLowerCase() === "number"
                    ) {
                        valA = parseFloat(valA) || 0; // Asegurar que sea un número
                        valB = parseFloat(valB) || 0;
                    } else if (
                        columnConfig.type &&
                        columnConfig.type.toLowerCase() === "date"
                    ) {
                        // Convertir fechas al formato YYYY-MM-DD
                        valA = formatDateToISO(valA);
                        valB = formatDateToISO(valB);
                        // Intentar convertir las fechas a milisegundos
                        valA = isValidDate(valA) ? new Date(valA).getTime() : 0;
                        valB = isValidDate(valB) ? new Date(valB).getTime() : 0;
                    } else if (
                        typeof valA === "string" &&
                        typeof valB === "string"
                    ) {
                        // Si son cadenas, convertir a minúsculas
                        valA = valA.toLowerCase();
                        valB = valB.toLowerCase();
                    }

                    // Comparar valores
                    if (valA < valB) {
                        return vOrderAsc === "asc" ? -1 : 1;
                    }
                    if (valA > valB) {
                        return vOrderAsc === "asc" ? 1 : -1;
                    }
                    return 0;
                });
            }

            // Calcula el índice de inicio para la paginación
            const startIndex = (page - 1) * pageSize;

            // Realiza la paginación
            let listaFiltrada = filteredArray;
            if (props.conPaginacion) {
                listaFiltrada = filteredArray.slice(
                    startIndex,
                    startIndex + pageSize,
                );
            }

            // Devuelve la sección del array filtrado, ordenado y paginado
            resolve(listaFiltrada);
        } catch (error) {
            reject(error);
        }
    });
};

// Función para validar formatos de fecha
function isValidDate(dateString) {
    const date = new Date(dateString);
    return !isNaN(date.getTime());
}
// Función para convertir fechas de DD/MM/YYYY a YYYY-MM-DD
function formatDateToISO(dateString) {
    if (/\d{2}\/\d{2}\/\d{4}/.test(dateString)) {
        const [day, month, year] = dateString.split("/");
        return `${year}-${month}-${day}`;
    }
    return dateString; // Si ya está en formato ISO, devolverlo tal cual
}

// detectar el cambio de cantidad de registros por pagina
const cambiaPerPage = async () => {
    currentPage.value = 1;
    cargarDatos();
};

// detectar cambio del orden
const changeOrderBy = async (orderCol) => {
    let oldOrderBy = orderBy.value;
    orderBy.value = orderCol;
    if (oldOrderBy != orderBy.value) {
        orderAsc.value = "asc";
    } else {
        switch (orderAsc.value) {
            case "asc":
                orderAsc.value = "desc";
                break;
            case "desc":
                orderAsc.value = null;
                orderBy.value = null;
                break;
            case null:
                orderAsc.value = "asc";
                break;
        }
    }
    cargarDatos();
};

// detectar cambio de pagina
const cambioDePagina = async (value) => {
    currentPage.value = value;
    cargarDatos();
};

// Mostrar la cantidad de registgros encontrados
// segun la página y total
const muestraCantidadRegistros = () => {
    const startIndex = (parseInt(currentPage.value) - 1) * per_page.value;
    const total_reg = listItems.value.length - 1;
    cDeRegistros.value = startIndex + 1; // Primer registro de la página
    cARegistros.value = cDeRegistros.value + total_reg; // Último registro de la página
};

// obtener el valor anidado de la lista de items ej.: ["cliente.nombre"]
function getColumnValue(obj, key) {
    return key.split(".").reduce((acc, part) => acc && acc[part], obj);
}

// usar la funcion getRowClass enviado desde la lista de columnas
function getRowClass(item) {
    let classes = [];
    for (const column of listCols.value) {
        if (column.classRow) {
            const className = column.classRow(item);
            if (className) {
                classes.push(className);
            }
        }
    }
    return classes.join(" ");
}

// verificar la opcion activa de ordenacion en el header
function getClassActiveSort(item) {
    if (props.api) {
        return (
            orderBy.value == (item.keySortable ? item.keySortable : item.key)
        );
    }
    return orderBy.value == item.key;
}

// setear el estado de loading del contenido de la tabla
const setLoading = (value) => {
    pLoading.value = value;
};
/**
 * FIN FUNCIONES CARGA DE DATOS
 * ******************************************************
 */

/********************************************************
 * ******************************************************
 *  FUNCIONES Y VARIABLES DE RENDERIZACIÓN DE TABLA
 */
const miTable = ref(null);
const miContentHeaderRef = ref(null);
const miTableHeaderRef = ref(null);
const miContentScrollRef = ref(null);
const miContentTableRef = ref(null);
const miTableFooterRef = ref(null);
const miContentFooter = ref(null);
const miTableRef = ref(null);
const listAnchoColumnas = ref([]);
const widthMiTable = ref(
    miTable.value ? parseInt(miTable.value.offsetWidth) : 0,
);
const widthColFirstDefault = ref(40);
const widthColsDefault = ref(40);
const refsRowsCol = ref({});

// Determinar el ancho de columnas
const ajustarAnchoColumnas = async () => {
    const tamanio_cols = listCols.value.length;
    const residuo = widthMiTable.value % tamanio_cols;
    let widthCol = parseInt(widthMiTable.value / tamanio_cols);
    nextTick(async () => {
        if (miContentHeaderRef.value && miContentTableRef.value) {
            widthParamContent.value = miContentTableRef.value.offsetWidth;
            // obtener la lista de columnas
            const listThHeader =
                miContentHeaderRef.value.querySelectorAll("th");
            const colsColgroupHeader =
                miContentHeaderRef.value.querySelectorAll("col");
            const colsColgroupContent =
                miContentTableRef.value.querySelectorAll("col");

            const colsColgroupFooter = miTableFooterRef.value
                ? miTableFooterRef.value.querySelectorAll("col")
                : [];

            // recorrer las columnas listThHeader
            listThHeader.forEach((elemCol, indexCol) => {
                const width = parseInt(elemCol.offsetWidth);
                const iheader = elemCol.querySelector(".iheader");
                const label = elemCol.querySelector(".label");
                const widthContent = parseInt(label.offsetWidth) + 30; // la suma del padding

                let widthDefinido = widthCol;
                if (listCols.value[indexCol].width) {
                    widthDefinido = getCalculoAnchoPVW(
                        listCols.value[indexCol].width,
                    );
                    if (widthContent > widthDefinido) {
                        widthDefinido = widthContent;
                    }
                } else {
                    if (residuo != 0 && indexCol == 0) {
                        widthDefinido = widthCol * (tamanio_cols - 1);
                        widthDefinido = widthMiTable.value - widthDefinido;
                    } else {
                        if (widthContent > widthDefinido) {
                            widthDefinido = widthContent;
                        }
                    }
                }

                colsColgroupHeader[indexCol].style.width = widthDefinido + "px";
                colsColgroupContent[indexCol].style.width =
                    widthDefinido + "px";
                if (colsColgroupFooter.length > 0) {
                    colsColgroupFooter[indexCol].style.width =
                        widthDefinido + "px";
                    listAnchoColumnas.value[indexCol] = widthDefinido;
                }
            });
        }

        await establecerColumnasFixedSlot();
        establecerAltoContenedor();
    });
};

// Funcion del :style en th y td
// obtiene el estilo de position right|left si el elemento es fixed
const widthParamContent = ref(0);
const getStyleColRenderizado = (indexCol, widthParam) => {
    if (
        listCols.value[indexCol].fixed &&
        miContentTableRef.value &&
        miTableRef.value
    ) {
        let axis = "";
        let axisParam = "";
        axis = "left";
        axisParam = "l";
        let claseBoxShadow = {};
        const listLefts = listCols.value.filter(
            (elem) => elem.fixed && elem.fixed == true,
        );
        const listRights = listCols.value.filter(
            (elem) => elem.fixed && elem.fixed == "right",
        );

        if (listCols.value[indexCol].fixed == "right") {
            axis = "right";
            axisParam = "r";
        }
        const distancia = calcularDistanciaPosicionRL(
            indexCol,
            axisParam,
            listAnchoColumnas.value,
        );
        if (window.innerWidth > 790 || !props.tableResponsive) {
            return {
                [axis]: distancia,
                ...claseBoxShadow,
            };
        }
    }

    return "";
};

// Funcion para el :class e th y td
// para sombrear lo 1ros y ultimos de las columnas
const getClassShadow = (item, indexCol, width_content) => {
    let verificar = "";
    let claseShadow = "";
    if (item.fixed && miContentTableRef.value && miTableRef.value) {
        verificar = item.fixed == "right" ? "r" : "l";
        const listLefts = listCols.value.filter(
            (elem) => elem.fixed && elem.fixed == true,
        );
        const listRights = listCols.value.filter(
            (elem) => elem.fixed && elem.fixed == "right",
        );

        if (
            miTableRef.value.offsetWidth > miContentTableRef.value.offsetWidth
        ) {
            if (verificar == "r") {
                if (indexCol === listCols.value.length - listRights.length)
                    claseShadow = "fixed-shadow-right";
            } else {
                if (indexCol === listLefts.length - 1)
                    claseShadow = "fixed-shadow-left";
            }
        }
    }
    return claseShadow;
};

// Fijar el alto que tendra el contenedor segun la propiedad tableHeight
const establecerAltoContenedor = () => {
    if (miContentTableRef.value) {
        miContentTableRef.value.style.minHeight = "";
        if (props.tableHeight) {
            miContentTableRef.value.style.height = `${props.tableHeight}`;
            miContentTableRef.value.style.minHeight = `${props.tableHeight}`;
        } else {
            miContentTableRef.value.style.minHeight = `${
                listItems.value.length * 53
            }px`;
        }
    }

    // resetPositionScroll();
    setTimeout(() => {
        updateScrollbars();
    }, 300);
};

// Obtener la distancia right|left del array stylesRenderizados
// para cada nueva carga de datos
const getDistanciaEstablecida = (indexCol) => {
    let styles = {};
    if (stylesRenderizados.value && stylesRenderizados.value.length > 0) {
        if (stylesRenderizados.value[indexCol]) {
            styles = stylesRenderizados.value[indexCol];
        }
    }
    return styles;
};

// funcion para calcular la distancia derecha|izquierda del elemento
const calcularDistanciaPosicionRL = (indexCol, rl, listaActualizada) => {
    // console.log(listaActualizada);
    let inicio = 0;
    let fin = indexCol > 0 ? indexCol : 0;
    let tamanio_cols = listCols.value.length;
    let distancia = 0;
    let width_elems = [];
    if (rl == "r") {
        inicio = indexCol + 1 < tamanio_cols ? indexCol + 1 : tamanio_cols;
        fin = tamanio_cols;
    }
    width_elems = listaActualizada.slice(inicio, fin);
    distancia = width_elems.reduce((a, b) => {
        return a + b;
    }, 0);

    return distancia + "px";
};

// renderizar columnas externas
const establecerColumnasFixedSlot = () => {
    return new Promise((resolve, reject) => {
        try {
            if (miTableFooterRef.value) {
                const table = miTableFooterRef.value;
                const listFilas = table.querySelectorAll("tr");
                listFilas.forEach((fila) => {
                    const listIzquierda =
                        fila.querySelectorAll(".fixed-column-ext");
                    const listDerechaIni = fila.querySelectorAll(
                        ".fixed-column-ext-right",
                    );
                    const listDerecha = [...listDerechaIni].reverse();
                    let distancia_acum = 0;
                    listIzquierda.forEach((elem) => {
                        elem.style.position = "sticky";
                        elem.style.left = distancia_acum + "px";
                        if (elem.classList.contains("footer-fixed")) {
                            elem.style.bottom = "1px";
                        }
                        elem.style.left = distancia_acum + "px";
                        distancia_acum += parseFloat(elem.offsetWidth);
                    });

                    distancia_acum = 0;
                    listDerecha.forEach((elem) => {
                        elem.style.position = "sticky";
                        elem.style.right = distancia_acum + "px";
                        if (elem.classList.contains("footer-fixed")) {
                            elem.style.bottom = "1px";
                        }
                        elem.style.right = distancia_acum + "px";
                        distancia_acum += parseFloat(elem.offsetWidth);
                    });
                });
            }
            resolve();
        } catch (error) {
            reject(error);
        }
    });
};

// calcular anchos % o vw
const getCalculoAnchoPVW = (width) => {
    const unidad_val = obtenerUnidadValor(width);
    let resultado = unidad_val[1];
    if (unidad_val[0] && unidad_val[0] != "px") {
        resultado = (resultado * widthMiTable.value) / 100;
    }
    return parseInt(resultado);
};
function obtenerUnidadValor(valor) {
    const coincidencia = valor.match(/^(\d+)(px|%)$/);
    let unidad = null;
    let width = valor;
    if (coincidencia) {
        unidad = coincidencia[2] || null;
        width = coincidencia[1];
    }
    return [unidad, width];
}

/**
 * FIN FUNCIONES RENDER
 * ***********************************
 */

/************************************
 * **********************************
 * SCROLL
 **/
const scrollX = ref(null);
const scrollY = ref(null);
// Variables para el drag
const isDragging = ref(false);
const dragAxis = ref(null);
const startPos = ref(0);
const startScroll = ref(0);
const originalScrollYpx = ref(-1);
// resetear la posición de los scrollbars
const resetPositionScroll = () => {
    originalScrollYpx.value = -1;
    startPos.value = 0;
    if (miContentTableRef.value) {
        miContentTableRef.value.scrollLeft = 0;
        miContentTableRef.value.scrollTop = 0;
    }
    if (scrollX.value) {
        scrollX.value.style.left = 0 + "px";
    }
    if (scrollY.value) {
        scrollY.value.style.top = 0 + "px";
    }
};
// actualizar el tamaño del elemento de scroll (track)
const updateScrollbars = () => {
    if (miContentTableRef.value) {
        if (
            miContentTableRef.value.scrollWidth >
            miContentTableRef.value.offsetWidth
        ) {
            // El ancho de la barra de scroll visible en X
            let anchoScrollX =
                miContentTableRef.value.offsetWidth /
                miContentTableRef.value.scrollWidth;
            anchoScrollX *= 100;
            scrollX.value.style.width = anchoScrollX + "%";
            scrollX.value.parentElement.style.display = "block";
        } else {
            scrollX.value.parentElement.style.display = "none";
        }

        if (
            miContentTableRef.value.scrollHeight >
            miContentTableRef.value.offsetHeight
        ) {
            // El ancho de la barra de scroll visible en Y
            let altoScrollY =
                miContentTableRef.value.offsetHeight /
                miContentTableRef.value.scrollHeight;
            altoScrollY *= 100;
            scrollY.value.style.height = altoScrollY + "%";
            originalScrollYpx.value = scrollY.value.offsetHeight;
            if (originalScrollYpx.value < 15 && originalScrollYpx.value > 0) {
                scrollY.value.style.height = "16px";
            }
            scrollY.value.parentElement.style.display = "block";
        } else {
            scrollY.value.parentElement.style.display = "none";
        }
        syncScrollBodyHeader();
    }
};

// sincronizar el scroll X del contenedor con el header
const syncScrollBodyHeader = () => {
    if (miContentTableRef.value) {
        miContentTableRef.value.removeEventListener(
            "scroll",
            scrollContentAHeader,
        );
        miContentTableRef.value.addEventListener(
            "scroll",
            scrollContentAHeader,
        );
    }
    if (miContentHeaderRef.value) {
        miContentHeaderRef.value.removeEventListener(
            "scroll",
            scrollHeaderAContent,
        );
        miContentHeaderRef.value.addEventListener(
            "scroll",
            scrollHeaderAContent,
        );
    }
};

// eventos de scroll content
const scrollContentAHeader = (e) => {
    // Verifica si el contenedor tiene un scroll horizontal
    if (e.target.scrollWidth > e.target.clientWidth) {
        // Solo sincroniza el scroll si el contenedor tiene desplazamiento
        miContentHeaderRef.value.scrollLeft = e.target.scrollLeft;
        let positionLeftScroll =
            e.target.scrollLeft / miContentTableRef.value.scrollWidth;
        scrollX.value.style.left =
            positionLeftScroll * (miContentTableRef.value.offsetWidth - 5) +
            "px";
    } else {
        miContentTableRef.value.removeEventListener(
            "scroll",
            syncScrollBodyHeader,
        );
    }

    // Verifica si el contenedor tiene un scroll vertical
    if (e.target.scrollHeight > e.target.clientHeight) {
        // Solo sincroniza el scroll si el contenedor tiene desplazamiento
        miContentHeaderRef.value.scrollTop = e.target.scrollTop;
        let positionTopScroll =
            e.target.scrollTop / miContentTableRef.value.scrollHeight;
        scrollY.value.style.top =
            positionTopScroll *
                (miContentTableRef.value.offsetHeight -
                    (originalScrollYpx.value > -1 ? 16 : 5)) +
            "px";
    } else {
        miContentTableRef.value.removeEventListener(
            "scroll",
            syncScrollBodyHeader,
        );
    }
};

// eventos de scroll header
const scrollHeaderAContent = (e) => {
    // Verifica si el contenedor tiene un scroll horizontal
    if (e.target.scrollWidth > e.target.clientWidth) {
        // Solo sincroniza el scroll si el contenedor tiene desplazamiento
        miContentTableRef.value.scrollLeft = e.target.scrollLeft;
        let positionLeftScroll =
            e.target.scrollLeft / miContentHeaderRef.value.scrollWidth;
        scrollX.value.style.left =
            positionLeftScroll * (miContentHeaderRef.value.offsetWidth - 5) +
            "px";
    } else {
        miContentHeaderRef.value.removeEventListener(
            "scroll",
            syncScrollBodyHeader,
        );
    }
};

// iniciar el drag del scroll
const startDrag = (axis, event) => {
    isDragging.value = true;
    dragAxis.value = axis;
    startPos.value = axis === "x" ? event.pageX : event.pageY;
    const container = miContentTableRef.value;
    startScroll.value =
        axis === "x" ? container.scrollLeft : container.scrollTop;

    document.body.classList.add("no-select");
    removerEventosScroll();
    document.addEventListener("mousemove", handleMouseMove);
    document.addEventListener("mouseup", stopDrag);
};

// detectar el movimiento del scroll
const handleMouseMove = (event) => {
    if (isDragging.value === true) {
        dragAxis.value === "x";
        const mouseDifferential =
            (dragAxis.value === "x" ? event.pageX : event.pageY) -
            startPos.value;
        const container = miContentTableRef.value;

        let scrollEquivalent = 0;

        if (dragAxis.value === "x") {
            scrollEquivalent =
                mouseDifferential *
                (container.scrollWidth / container.offsetWidth);
            container.scrollLeft = startScroll.value + scrollEquivalent;
        } else {
            scrollEquivalent =
                mouseDifferential *
                (container.scrollHeight / container.offsetHeight);
            container.scrollTop = startScroll.value + scrollEquivalent;
        }
    }
};

// detener el scroll
const stopDrag = () => {
    // Habilitar la selección de texto nuevamente
    document.body.classList.remove("no-select");
    removerEventosScroll();
};

// remover los eventos de scroll
const removerEventosScroll = () => {
    document.removeEventListener("mousemove", handleMouseMove);
    document.removeEventListener("mouseup", stopDrag);
};

/**
 * FIN SCROLL
 ******************************************
 **/

const observerContentMiTable = ref(null);
const inertavalResizeContent = ref(null);
onMounted(async () => {
    if (props.api) {
        cargarDatos();
    }
    await esperarCargaElementos();
    widthMiTable.value = parseInt(miTable.value.offsetWidth);
    syncScrollBodyHeader();
    ajustarAnchoColumnas();
    // Iniciar observer content miTable
    observerContentMiTable.value = new ResizeObserver(async (entries) => {
        clearInterval(inertavalResizeContent.value);
        if (entries.length > 0) {
            const newWidth = entries[entries.length - 1].contentRect.width;
            inertavalResizeContent.value = setTimeout(async () => {
                if (widthMiTable.value != newWidth) {
                    widthMiTable.value = parseInt(newWidth);
                    await esperarCargaElementos();
                    ajustarAnchoColumnas();
                }
            }, 200);
        }
    });
    if (miTable.value) {
        observerContentMiTable.value.observe(miTable.value);
    }
});

onUnmounted(() => {
    // Limpiar observer
    if (observerContentMiTable.value && miContentScrollRef.value) {
        observerContentMiTable.value.unobserve(miContentScrollRef.value);
    }
    observerContentMiTable.value = null;
});

defineExpose({
    cargarDatos,
    setLoading,
});
</script>
<template>
    <div
        class="mi-table"
        :class="[$attrs.class, fixedHeader ? 'tablaFixeada' : '']"
        ref="miTable"
    >
        <div class="content-data">
            <div class="mi-content-header" ref="miContentHeaderRef">
                <table
                    class="table table-bordered"
                    :class="[
                        tableResponsive ? 'table-resposive' : '',
                        fixedHeader ? 'tablaFixeada' : '',
                    ]"
                    ref="miTableHeaderRef"
                >
                    <colgroup ref="tableHeaderGroup">
                        <col v-for="item in listCols" />
                    </colgroup>
                    <thead :class="[headerClass]">
                        <template v-if="$slots.tableHeader">
                            <slot name="tableHeader"></slot>
                        </template>
                        <template v-else>
                            <tr>
                                <th
                                    v-for="(item, index) in listCols"
                                    :colspan="`${item.colspan ? item.colspan : 1}`"
                                    :class="[
                                        item.fixed
                                            ? item.fixed == 'right'
                                                ? 'fixed-column-right'
                                                : 'fixed-column'
                                            : '',
                                        fixedHeader ? 'fixed-header' : '',
                                        getClassShadow(
                                            item,
                                            index,
                                            widthParamContent,
                                        ),
                                    ]"
                                    :data-width="item.width ? item.width : ''"
                                    :style="
                                        getStyleColRenderizado(
                                            index,
                                            widthParamContent,
                                        )
                                    "
                                >
                                    <div
                                        class="iheader sortable"
                                        v-if="item.sortable"
                                        @click="
                                            changeOrderBy(
                                                api
                                                    ? item.keySortable
                                                        ? item.keySortable
                                                        : item.key
                                                    : item.key,
                                            )
                                        "
                                    >
                                        <div class="label">
                                            {{ item.label }}
                                        </div>
                                        <div
                                            class="accion"
                                            :class="{
                                                active: getClassActiveSort(
                                                    item,
                                                ),
                                            }"
                                        >
                                            <i
                                                class="fa"
                                                :class="{
                                                    'fa-sort-amount-up-alt':
                                                        orderAsc == 'asc' &&
                                                        orderBy ==
                                                            (api
                                                                ? item.keySortable
                                                                    ? item.keySortable
                                                                    : item.key
                                                                : item.key),
                                                    'fa-sort-amount-down':
                                                        orderAsc == 'desc' &&
                                                        orderBy ==
                                                            (api
                                                                ? item.keySortable
                                                                    ? item.keySortable
                                                                    : item.key
                                                                : item.key),
                                                    'fa-sort':
                                                        !orderAsc ||
                                                        orderBy !=
                                                            (api
                                                                ? item.keySortable
                                                                    ? item.keySortable
                                                                    : item.key
                                                                : item.key),
                                                }"
                                            ></i>
                                        </div>
                                    </div>
                                    <div class="iheader" v-else>
                                        <div class="label">
                                            {{ item.label }}
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </template>
                    </thead>
                </table>
            </div>
            <div class="mi-content-scroll" ref="miContentScrollRef">
                <div
                    class="content-table"
                    :style="{
                        maxHeight: tableHeight ? tableHeight : '',
                        width: tableWidth ? tableWidth : '',
                    }"
                    ref="miContentTableRef"
                >
                    <div
                        class="mi-loading-table"
                        v-show="pLoading"
                        ref="miLoading"
                    >
                        <div>
                            <template v-if="$slots.loading">
                                <slot name="loading"></slot>
                            </template>
                            <template v-else> {{ textCargando }} </template>
                        </div>
                    </div>
                    <table
                        class="table table-bordered mb-0"
                        :class="[
                            tableClass,
                            tableResponsive ? 'table-resposive' : '',
                        ]"
                        ref="miTableRef"
                    >
                        <colgroup ref="tableHeaderGroup">
                            <col v-for="item in listCols" />
                        </colgroup>
                        <tbody
                            :class="[
                                bodyClass,
                                pLoading ? 'loading_active' : '',
                            ]"
                            ref="eTbody"
                        >
                            <template v-if="listItems.length > 0">
                                <tr
                                    v-for="(item, index_row) in listItems"
                                    :class="getRowClass(item)"
                                    :key="item.id ? item.id : index_row"
                                >
                                    <td
                                        v-for="(i_col, index_col) in listCols"
                                        :class="[
                                            i_col.fixed
                                                ? i_col.fixed == 'right'
                                                    ? 'fixed-column-right'
                                                    : 'fixed-column'
                                                : '',
                                            getClassShadow(
                                                i_col,
                                                index_col,
                                                widthParamContent,
                                            ),
                                        ]"
                                        :colspan="`${item.colspan ? item.colspan : 1}`"
                                        :ref="
                                            (el) =>
                                                (refsRowsCol[
                                                    `${index_row}-${index_col}`
                                                ] = el)
                                        "
                                        :style="
                                            getStyleColRenderizado(
                                                index_col,
                                                widthParamContent,
                                            )
                                        "
                                    >
                                        <div
                                            class="label-responsive"
                                            v-text="`${i_col.label}:`"
                                        ></div>
                                        <div
                                            class="mi-celda"
                                            :class="[
                                                i_col.classTd
                                                    ? i_col.classTd(item)
                                                    : '',
                                            ]"
                                        >
                                            <template v-if="$slots[i_col.key]">
                                                <slot
                                                    :name="i_col.key"
                                                    :item="item"
                                                    v-bind="$attrs"
                                                ></slot>
                                            </template>
                                            <template v-else>
                                                {{
                                                    i_col.isIndex
                                                        ? index_row + 1
                                                        : i_col.render
                                                          ? i_col.render(
                                                                item,
                                                                index_row,
                                                            )
                                                          : i_col.key
                                                            ? getColumnValue(
                                                                  item,
                                                                  i_col.key,
                                                              )
                                                            : ""
                                                }}
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr>
                                    <td
                                        :colspan="listCols.length"
                                        class="text-center py-2"
                                    >
                                        {{ textSinRegistros }}
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="content-scroll-x">
                    <div
                        class="mi-custom-scroll-x"
                        @mousedown="startDrag('x', $event)"
                        ref="scrollX"
                    ></div>
                </div>
                <div class="content-scroll-y">
                    <div
                        class="mi-custom-scroll-y"
                        @mousedown="startDrag('y', $event)"
                        ref="scrollY"
                    ></div>
                </div>
            </div>
            <div
                class="mi-content-footer"
                ref="miContentFooter"
                v-if="$slots.tableFooter"
            >
                <table class="table table-bordered" ref="miTableFooterRef">
                    <colgroup ref="tableHeaderGroup">
                        <col v-for="item in listCols" />
                    </colgroup>
                    <tbody :class="[footerClass]">
                        <slot name="tableFooter"></slot>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="content-foot" v-if="conPaginacion">
            <div class="row mt-1">
                <div class="my-1 col-md-3">
                    <select class="form-control rounded-0" v-model="per_page">
                        <option v-for="item in filter_page" :value="item">
                            Mostrar {{ item }} registros
                        </option>
                    </select>
                </div>
                <div class="my-1 col-md-6 d-flex justify-content-center">
                    <MiPaginacion
                        :current-page="currentPage"
                        :total-data="total"
                        :per-page="per_page"
                        @updatePage="cambioDePagina"
                    />
                </div>
                <div class="my-1 col-md-3 text-right">
                    Mostrando {{ cDeRegistros }} a {{ cARegistros }} registros -
                    Total {{ total }} registros
                </div>
            </div>
            <div v-if="error" class="alert alert-danger">
                Ocurrió un error al intentar obtener los registros
            </div>
        </div>
    </div>
</template>
<style scoped>
.mi-content-scroll {
    position: relative;
}

.content-scroll-x,
.content-scroll-y {
    opacity: 70%;
    position: absolute;
    background-color: transparent;
    z-index: 3;
}
.mi-table .mi-content-scroll:hover .content-scroll-x,
.mi-table .mi-content-scroll:hover .content-scroll-y {
    opacity: 100%;
}
.content-scroll-x {
    height: 10px;
    width: 100%;
    bottom: 0px;
}
.content-scroll-y {
    height: 100%;
    width: 10px;
    top: 0;
    right: 0;
}

.mi-custom-scroll-y,
.mi-custom-scroll-x {
    position: absolute;
    background: #cacaca;
    cursor: pointer;
    border-radius: 10px;
}

.mi-custom-scroll-y {
    margin-left: 2px;
    width: 8px;
    top: 0;
    right: 0px;
}

.mi-custom-scroll-x {
    margin-top: 2px;
    height: 8px;
    left: 0px;
}
</style>
