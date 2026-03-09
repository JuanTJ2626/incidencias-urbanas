import { ref } from 'vue'

/**
 * Composable que encapsula la lógica de geolocalización del usuario
 * y el dibujo de su marcador/círculo de precisión en Google Maps.
 */
export function useUserLocation() {
    const userPosition = ref(null)
    const locationLoading = ref(false)
    const locationError = ref('')

    let userMarker = null
    let userCircle = null

    /**
     * Solicita la ubicación del navegador y la dibuja en el mapa.
     * @param {google.maps.Map} map — instancia activa del mapa
     */
    function verMiUbicacion(map) {
        if (!navigator.geolocation) {
            locationError.value = 'Tu navegador no soporta geolocalización.'
            return
        }

        if (!map) return

        locationLoading.value = true
        locationError.value = ''

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const coords = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                }

                userPosition.value = coords
                dibujarUbicacion(map, coords, position.coords.accuracy)
                locationLoading.value = false
            },
            (error) => {
                const messages = {
                    1: 'Debes permitir el acceso a tu ubicación para mostrarla en el mapa.',
                    2: 'No se pudo determinar tu ubicación actual.',
                    3: 'Se agotó el tiempo para obtener tu ubicación.',
                }
                locationError.value = messages[error.code] || 'No fue posible obtener tu ubicación.'
                locationLoading.value = false
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000,
            },
        )
    }

    /**
     * Dibuja (o reemplaza) el marcador y círculo de precisión del usuario.
     */
    function dibujarUbicacion(map, coords, accuracy = 0) {
        if (userMarker) userMarker.setMap(null)
        if (userCircle) userCircle.setMap(null)

        userMarker = new window.google.maps.Marker({
            map,
            position: coords,
            title: 'Mi ubicación',
            zIndex: 999,
            icon: {
                path: window.google.maps.SymbolPath.CIRCLE,
                scale: 10,
                fillColor: '#9D1B32',
                fillOpacity: 1,
                strokeColor: '#FFFFFF',
                strokeWeight: 3,
            },
        })

        userCircle = new window.google.maps.Circle({
            map,
            center: coords,
            radius: accuracy || 0,
            strokeColor: '#60A5FA',
            strokeOpacity: 0.55,
            strokeWeight: 1,
            fillColor: '#93C5FD',
            fillOpacity: 0.18,
        })

        map.panTo(coords)
        map.setZoom(16)
    }

    /**
     * Limpia marcador y círculo del mapa (para beforeUnmount).
     */
    function limpiarUbicacion() {
        if (userMarker) {
            userMarker.setMap(null)
            userMarker = null
        }
        if (userCircle) {
            userCircle.setMap(null)
            userCircle = null
        }
    }

    return {
        userPosition,
        locationLoading,
        locationError,
        verMiUbicacion,
        limpiarUbicacion,
    }
}
