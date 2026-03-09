import { computed } from 'vue'

/**
 * Colores por estatus — reutilizable en cualquier parte del dashboard.
 */
export const ESTATUS_COLORS = {
    pendiente: '#F59E0B',
    'en proceso': '#0EA5E9',
    resuelto: '#10B981',
    completado: '#10B981',
    cerrado: '#10B981',
    rechazado: '#EF4444',
    'en revisión': '#A855F7',
}
const ESTATUS_DEFAULT = '#86868B'

const BAR_COLORS = [
    '#8A1538', '#a01a45', '#C0392B', '#E74C3C',
    '#0EA5E9', '#F59E0B', '#10B981', '#A855F7',
]

/**
 * Composable que calcula todas las estadísticas, datos de charts
 * y helpers del Dashboard a partir del array de incidencias.
 */
export function useDashboardStats(incidencias) {
    const data = computed(() => incidencias.value ?? [])
    const tieneIncidencias = computed(() => data.value.length > 0)
    const total = computed(() => data.value.length)

    // ─── Contadores por estatus ──────────────────────────────
    const contar = (estatus) => data.value.filter(i => i.estatus === estatus).length

    const countPendiente = computed(() => contar('pendiente'))
    const countEnProceso = computed(() => contar('en proceso'))
    const countResuelto = computed(() => data.value.filter(i =>
        ['resuelto', 'completado', 'cerrado'].includes(i.estatus)).length)
    const countRechazado = computed(() => contar('rechazado'))

    const pct = (val) => total.value ? Math.round((val / total.value) * 100) : 0

    // ─── Stat Cards ─────────────────────────────────────────
    const statCards = computed(() => [
        {
            label: 'Total Incidencias',
            value: total.value,
            pct: 100,
            icon: 'pi pi-inbox',
            iconBg: 'bg-[#F5F5F7]',
            iconColor: 'text-[#1D1D1F]',
            badgeBg: 'bg-gray-100',
            badgeText: 'text-[#86868B]',
            deco: 'bg-gray-400',
            hover: 'hover:bg-[#1D1D1F] hover:border-[#1D1D1F] hover:shadow-[0_20px_40px_rgba(0,0,0,0.2)]',
        },
        {
            label: 'Pendientes',
            value: countPendiente.value,
            pct: pct(countPendiente.value),
            icon: 'pi pi-clock',
            iconBg: 'bg-amber-50',
            iconColor: 'text-amber-500',
            badgeBg: 'bg-amber-50',
            badgeText: 'text-amber-600',
            deco: 'bg-amber-400',
            hover: 'hover:bg-amber-500 hover:border-amber-500 hover:shadow-[0_20px_40px_rgba(245,158,11,0.3)]',
        },
        {
            label: 'En Proceso',
            value: countEnProceso.value,
            pct: pct(countEnProceso.value),
            icon: 'pi pi-sync',
            iconBg: 'bg-sky-50',
            iconColor: 'text-sky-600',
            badgeBg: 'bg-sky-50',
            badgeText: 'text-sky-700',
            deco: 'bg-sky-400',
            hover: 'hover:bg-sky-600 hover:border-sky-600 hover:shadow-[0_20px_40px_rgba(2,132,199,0.3)]',
        },
        {
            label: 'Resueltas',
            value: countResuelto.value,
            pct: pct(countResuelto.value),
            icon: 'pi pi-check-circle',
            iconBg: 'bg-emerald-50',
            iconColor: 'text-emerald-600',
            badgeBg: 'bg-emerald-50',
            badgeText: 'text-emerald-700',
            deco: 'bg-emerald-400',
            hover: 'hover:bg-emerald-600 hover:border-emerald-600 hover:shadow-[0_20px_40px_rgba(5,150,105,0.3)]',
        },
    ])

    // ─── Chart Donut: por estatus ───────────────────────────
    const estatusAgrupado = computed(() => {
        const map = {}
        data.value.forEach(i => {
            const k = i.estatus ?? 'sin estatus'
            map[k] = (map[k] || 0) + 1
        })
        return map
    })

    const chartEstatus = computed(() => {
        const labels = Object.keys(estatusAgrupado.value)
        const values = Object.values(estatusAgrupado.value)
        const colors = labels.map(l => ESTATUS_COLORS[l] ?? ESTATUS_DEFAULT)
        return {
            labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 8,
            }],
        }
    })

    const chartEstatusLeyenda = computed(() =>
        Object.entries(estatusAgrupado.value).map(([label, value]) => ({
            label,
            value,
            pct: pct(value),
            color: ESTATUS_COLORS[label] ?? ESTATUS_DEFAULT,
        }))
    )

    const donutOptions = {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '68%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (ctx) => ` ${ctx.label}: ${ctx.parsed} (${Math.round((ctx.parsed / (ctx.dataset.data.reduce((a, b) => a + b, 0))) * 100)}%)`,
                },
            },
        },
    }

    // ─── Chart Bar: por categoría ───────────────────────────
    const categoriaAgrupado = computed(() => {
        const map = {}
        data.value.forEach(i => {
            const k = i.tipo_incidencia ?? 'otro'
            map[k] = (map[k] || 0) + 1
        })
        return Object.entries(map)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 8)
    })

    const chartCategoria = computed(() => ({
        labels: categoriaAgrupado.value.map(([k]) => k),
        datasets: [{
            label: 'Incidencias',
            data: categoriaAgrupado.value.map(([, v]) => v),
            backgroundColor: categoriaAgrupado.value.map((_, i) => BAR_COLORS[i % BAR_COLORS.length]),
            borderRadius: 8,
            borderSkipped: false,
            hoverBackgroundColor: '#8A1538',
        }],
    }))

    const barOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: (ctx) => ` ${ctx.parsed.y} incidencias` } },
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { font: { size: 10, weight: 'bold' }, color: '#86868B', maxRotation: 30 },
            },
            y: {
                grid: { color: '#F5F5F7' },
                ticks: { font: { size: 10 }, color: '#86868B', stepSize: 1, precision: 0 },
                beginAtZero: true,
            },
        },
    }

    // ─── Últimas incidencias ────────────────────────────────
    const ultimasIncidencias = computed(() =>
        [...data.value]
            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
            .slice(0, 8)
    )

    // ─── Mapa: centroide ────────────────────────────────────
    const totalConCoordenadas = computed(() =>
        data.value.filter(i => i.latitud && i.longitud).length
    )

    const osmUrl = computed(() => {
        const conCoords = data.value.filter(i => i.latitud && i.longitud)
        if (conCoords.length) {
            const lats = conCoords.map(i => i.latitud)
            const lngs = conCoords.map(i => i.longitud)
            const cLat = lats.reduce((a, b) => a + b, 0) / lats.length
            const cLng = lngs.reduce((a, b) => a + b, 0) / lngs.length
            return `https://www.google.com/maps/embed/v1/view?key=AIzaSyCchiqlRlOnv6C4pXxh59tYDMRiK501Tmc&center=${cLat},${cLng}&zoom=13`
        }
        return 'https://www.google.com/maps/embed/v1/view?key=AIzaSyCchiqlRlOnv6C4pXxh59tYDMRiK501Tmc&center=19.4014,-99.0150&zoom=13'
    })

    // ─── Tabla: stats por categoría ─────────────────────────
    const categoriaStats = computed(() => {
        const map = {}
        data.value.forEach(i => {
            const k = i.tipo_incidencia ?? 'otro'
            if (!map[k]) map[k] = { total: 0, resueltas: 0, pendientes: 0 }
            map[k].total++
            if (['resuelto', 'completado', 'cerrado'].includes(i.estatus)) map[k].resueltas++
            if (i.estatus === 'pendiente') map[k].pendientes++
        })
        return Object.entries(map)
            .sort((a, b) => b[1].total - a[1].total)
            .map(([nombre, v]) => ({
                nombre,
                total: v.total,
                resueltas: v.resueltas,
                pendientes: v.pendientes,
                tasa: v.total ? Math.round((v.resueltas / v.total) * 100) : 0,
            }))
    })

    // ─── Helpers de presentación ────────────────────────────
    const badgeClass = (estatus) => {
        const m = {
            pendiente: 'bg-amber-100 text-amber-700',
            'en proceso': 'bg-sky-100 text-sky-700',
            resuelto: 'bg-emerald-100 text-emerald-700',
            completado: 'bg-emerald-100 text-emerald-700',
            cerrado: 'bg-emerald-100 text-emerald-700',
            rechazado: 'bg-rose-100 text-rose-700',
            'en revisión': 'bg-purple-100 text-purple-700',
        }
        return m[estatus] ?? 'bg-gray-100 text-gray-600'
    }

    const colorEstatus = (estatus) => ESTATUS_COLORS[estatus] ?? ESTATUS_DEFAULT

    const iconoCategoria = (nombre) => {
        const n = (nombre || '').toLowerCase()
        if (n.includes('bache') || n.includes('paviment')) return 'pi pi-exclamation-triangle'
        if (n.includes('alumbr') || n.includes('luz')) return 'pi pi-sun'
        if (n.includes('basura') || n.includes('residuo')) return 'pi pi-trash'
        if (n.includes('agua') || n.includes('fuga')) return 'pi pi-tint'
        if (n.includes('árbol') || n.includes('verde')) return 'pi pi-apple'
        if (n.includes('graffiti') || n.includes('vand')) return 'pi pi-palette'
        if (n.includes('ruido') || n.includes('sonido')) return 'pi pi-volume-up'
        return 'pi pi-map-marker'
    }

    const formatDate = (str) => {
        if (!str) return '—'
        const d = new Date(str)
        return d.toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' })
    }

    return {
        // data
        tieneIncidencias,
        total,
        statCards,
        // charts
        chartEstatus,
        chartEstatusLeyenda,
        donutOptions,
        chartCategoria,
        barOptions,
        // table & list
        ultimasIncidencias,
        categoriaStats,
        // map
        totalConCoordenadas,
        osmUrl,
        // helpers
        badgeClass,
        colorEstatus,
        iconoCategoria,
        formatDate,
    }
}
