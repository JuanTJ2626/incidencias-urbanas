// ─── Constantes ──────────────────────────────────────────────────────────────
export const GOOGLE_MAPS_KEY = 'AIzaSyCchiqlRlOnv6C4pXxh59tYDMRiK501Tmc'
export const DEFAULT_CENTER = { lat: 19.4014, lng: -99.0150 }

// ─── Carga de la API (singleton) ─────────────────────────────────────────────
let googleMapsPromise = null

export function loadGoogleMapsApi() {
	if (window.google?.maps) return Promise.resolve(window.google.maps)
	if (googleMapsPromise) return googleMapsPromise

	googleMapsPromise = new Promise((resolve, reject) => {
		const callbackName = '__initAdminMapaGoogle'
		window[callbackName] = () => {
			resolve(window.google.maps)
			delete window[callbackName]
		}

		const existingScript = document.querySelector('script[data-google-admin-mapa="true"]')
		if (existingScript) return

		const script = document.createElement('script')
		script.src = `https://maps.googleapis.com/maps/api/js?key=${GOOGLE_MAPS_KEY}&libraries=visualization,places&callback=${callbackName}`
		script.async = true
		script.defer = true
		script.dataset.googleAdminMapa = 'true'
		script.onerror = () => {
			reject(new Error('No fue posible descargar Google Maps.'))
			delete window[callbackName]
			googleMapsPromise = null
		}

		document.head.appendChild(script)
	})

	return googleMapsPromise
}

// ─── PulseOverlay (requiere que la API ya esté cargada) ───────────────────────
export function createPulseOverlayClass() {
	return class PulseOverlay extends window.google.maps.OverlayView {
		constructor(latlng, color) {
			super()
			this._pos = latlng
			this._color = color
			this._div = null
		}

		onAdd() {
			this._div = document.createElement('div')
			this._div.className = 'map-pulse'
			this._div.style.setProperty('--pulse-color', this._color)
			const d = (Math.random() * 2.2).toFixed(2)
			this._div.style.setProperty('--pulse-delay', `-${d}s`)
			this.getPanes().overlayLayer.appendChild(this._div)
		}

		draw() {
			if (!this._div) return
			const p = this.getProjection().fromLatLngToDivPixel(this._pos)
			if (!p) return
			this._div.style.left = p.x + 'px'
			this._div.style.top = p.y + 'px'
		}

		onRemove() {
			if (this._div && this._div.parentNode) this._div.parentNode.removeChild(this._div)
			this._div = null
		}
	}
}

// ─── Helpers de estatus ──────────────────────────────────────────────────────
export function statusType(estatus) {
	if (!estatus) return 'pending'
	const normalized = estatus.toLowerCase()
	if (normalized.includes('resuelto')) return 'resolved'
	if (normalized.includes('rechaz')) return 'rejected'
	if (normalized.includes('en proceso') || normalized.includes('activo') || normalized.includes('en revisión')) return 'inprogress'
	return 'pending'
}

export function statusColor(type) {
	const colors = {
		resolved: '#10b981',
		inprogress: '#3b82f6',
		rejected: '#ef4444',
		pending: '#f59e0b',
	}
	return colors[type] || colors.pending
}

export function statusInlineSvg(type) {
	const icons = {
		pending: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
		rejected: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
		inprogress: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
		resolved: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
	}
	return icons[type] || icons.pending
}

export function badgeClass(estatus, invertido = false) {
	const base = 'inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wide border '
	const type = statusType(estatus)
	const map = {
		pending: `${base}${invertido ? 'bg-white/10 text-white border-white/20' : 'bg-amber-100 text-amber-700 border-amber-200'}`,
		inprogress: `${base}${invertido ? 'bg-sky-400/20 text-white border-sky-200/20' : 'bg-sky-100 text-sky-700 border-sky-200'}`,
		resolved: `${base}${invertido ? 'bg-emerald-400/20 text-white border-emerald-200/20' : 'bg-emerald-100 text-emerald-700 border-emerald-200'}`,
		rejected: `${base}${invertido ? 'bg-rose-400/20 text-white border-rose-200/20' : 'bg-rose-100 text-rose-700 border-rose-200'}`,
	}
	return map[type] || map.pending
}

// ─── Construcción del ícono del marcador (requiere window.google.maps) ────────
export function construirIconoMarker(inc) {
	const type = statusType(inc.estatus)
	const cfgs = {
		pending: {
			bg: '#f59e0b', border: '#b45309',
			path: '<circle cx="12" cy="12" r="9.5"/><line x1="12" y1="8" x2="12" y2="12.5"/><circle cx="12" cy="16.5" r="1" fill="white" stroke="none"/>'
		},
		rejected: {
			bg: '#ef4444', border: '#b91c1c',
			path: '<circle cx="12" cy="12" r="9.5"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/>'
		},
		inprogress: {
			bg: '#3b82f6', border: '#1d4ed8',
			path: '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>'
		},
		resolved: {
			bg: '#10b981', border: '#047857',
			path: '<polyline points="20 6 9 17 4 12"/>'
		},
	}

	const config = cfgs[type] || cfgs.pending
	const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38"><filter id="ds"><feDropShadow dx="0" dy="2" stdDeviation="2.5" flood-color="rgba(0,0,0,0.35)"/></filter><rect x="1" y="1" width="36" height="36" rx="10" fill="${config.bg}" stroke="${config.border}" stroke-width="1.8" filter="url(#ds)"/><rect x="1" y="1" width="36" height="18" rx="10" fill="rgba(255,255,255,0.15)"/><g transform="translate(7,7)" fill="none" stroke="white" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">${config.path}</g></svg>`

	return {
		url: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`,
		scaledSize: new window.google.maps.Size(38, 38),
		anchor: new window.google.maps.Point(19, 19),
	}
}

// ─── Otras utilidades ─────────────────────────────────────────────────────────

// Límites geográficos de México (bounding box amplio)
const MEXICO_BOUNDS = { latMin: 14, latMax: 33, lngMin: -118, lngMax: -86 }

export function tieneCoordenadas(inc) {
	const lat = Number(inc?.latitud)
	const lng = Number(inc?.longitud)
	if (!Number.isFinite(lat) || !Number.isFinite(lng)) return false
	// Filtrar coordenadas fuera de México para evitar que el mapa se aleje al mundo
	return lat >= MEXICO_BOUNDS.latMin && lat <= MEXICO_BOUNDS.latMax
		&& lng >= MEXICO_BOUNDS.lngMin && lng <= MEXICO_BOUNDS.lngMax
}

export function formatearCoord(value) {
	const num = Number(value)
	return Number.isFinite(num) ? num.toFixed(6) : '—'
}

export function googleMapsUrl(inc) {
	return `https://www.google.com/maps/search/?api=1&query=${inc.latitud},${inc.longitud}`
}

export function calcularDistanciaKm(lat1, lng1, lat2, lng2) {
	const toRad = v => (v * Math.PI) / 180
	const R = 6371
	const dLat = toRad(lat2 - lat1)
	const dLng = toRad(lng2 - lng1)
	const a =
		Math.sin(dLat / 2) * Math.sin(dLat / 2) +
		Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
		Math.sin(dLng / 2) * Math.sin(dLng / 2)
	return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
}
