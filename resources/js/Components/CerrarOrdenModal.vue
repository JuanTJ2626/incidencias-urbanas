<template>
  <Dialog
    :visible="visible"
    @update:visible="$emit('update:visible', $event)"
    header="Cerrar Orden de Trabajo"
    :modal="true" :style="{ width: '520px', maxWidth: '95vw' }" :draggable="false"
    :closable="!cerrando"
    @hide="onHide"
  >
    <div class="flex flex-col gap-5 py-2">
      <!-- Resumen -->
      <div class="bg-app-secondary rounded-xl p-3 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-brand-red/10 flex items-center justify-center shrink-0">
          <i class="pi pi-wrench text-brand-red"></i>
        </div>
        <div>
          <p class="text-sm font-bold text-[#1D1D1F] dark:text-white">{{ tarea?.tipo_incidencia }}</p>
          <p class="text-xs text-[#86868B] dark:text-[#A1A1A6]">{{ tarea?.direccion }}</p>
        </div>
      </div>

      <!-- Foto DESPUÉS (obligatoria) -->
      <div class="flex flex-col gap-2">
        <label class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wide flex items-center gap-1">
          <i class="pi pi-camera text-[#850D12]"></i>
          Evidencia fotográfica <span class="text-rose-500">*</span>
        </label>
        
        <div v-if="!cameraActiva && !fotoPreview" class="grid grid-cols-1 sm:grid-cols-2 gap-2">
           <!-- Botón Cámara Pro -->
           <button 
             type="button" @click="abrirCamara"
             class="flex flex-col items-center justify-center gap-2 py-4 border-2 border-dashed border-app-border rounded-2xl hover:border-brand-red hover:bg-brand-red/5 transition-all group"
           >
              <div class="w-10 h-10 rounded-full bg-app-secondary flex items-center justify-center group-hover:bg-brand-red/10 transition-colors">
                 <i class="pi pi-camera text-gray-500 group-hover:text-brand-red text-xl"></i>
              </div>
              <span class="text-[11px] font-bold text-gray-600 group-hover:text-brand-red uppercase tracking-wider">Tomar Foto</span>
           </button>

           <!-- Botón Galería -->
           <button 
             type="button" @click="$refs.galleryInput.click()"
             class="flex flex-col items-center justify-center gap-2 py-4 border-2 border-dashed border-app-border rounded-2xl hover:border-brand-red hover:bg-brand-red/5 transition-all group"
           >
              <div class="w-10 h-10 rounded-full bg-app-secondary flex items-center justify-center group-hover:bg-brand-red/10 transition-colors">
                 <i class="pi pi-image text-gray-500 group-hover:text-brand-red text-xl"></i>
              </div>
              <span class="text-[11px] font-bold text-gray-600 group-hover:text-brand-red uppercase tracking-wider">De Galería</span>
              <input ref="galleryInput" type="file" accept="image/*" class="hidden" @change="onFotoChange" />
           </button>
        </div>

        <!-- Cámara en Vivo -->
        <div v-if="cameraActiva" class="relative rounded-2xl overflow-hidden bg-black aspect-[4/3] shadow-2xl animate-fade-in border-4 border-brand-red/20">
          <video ref="videoStream" autoplay playsinline class="w-full h-full object-cover"></video>
          
          <div class="absolute inset-0 flex flex-col justify-between p-4 pointer-events-none">
              <div class="flex justify-between items-start">
                  <span class="bg-brand-red text-white text-[9px] font-black px-2 py-1 rounded-lg uppercase tracking-widest shadow-lg">Cámara Activa</span>
                  <button type="button" @click="detenerCamara" class="pointer-events-auto w-8 h-8 rounded-full bg-black/40 backdrop-blur-md text-white flex items-center justify-center hover:bg-black/60">
                      <i class="pi pi-times"></i>
                  </button>
              </div>
              <div class="flex justify-center pb-2">
                  <button 
                      type="button" @click="capturarFoto"
                      class="pointer-events-auto w-16 h-16 rounded-full border-4 border-white bg-white/20 backdrop-blur-sm flex items-center justify-center hover:scale-105 active:scale-90 transition-all shadow-2xl"
                  >
                      <div class="w-12 h-12 rounded-full bg-white shadow-inner"></div>
                  </button>
              </div>
          </div>
          <canvas ref="photoCanvas" class="hidden"></canvas>
        </div>

        <!-- Preview -->
        <div v-if="fotoPreview && !cameraActiva" class="relative mt-1 rounded-2xl overflow-hidden border border-[#E8E8ED] h-60 shadow-xl animate-fade-in">
          <img :src="fotoPreview" class="w-full h-full object-cover" alt="preview" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent pointer-events-none"></div>
          <button @click="fotoPreview = null; fotoDespues = null" class="absolute top-3 right-3 w-8 h-8 bg-brand-red text-white rounded-full flex items-center justify-center hover:bg-black transition-colors shadow-lg">
            <i class="pi pi-trash text-xs"></i>
          </button>
          <div class="absolute bottom-3 left-4">
             <span class="bg-white/90 backdrop-blur-md text-[#1D1D1F] text-[9px] font-black px-2 py-1 rounded-lg uppercase">Previsualización</span>
          </div>
        </div>
        <p v-if="errors.foto_despues" class="text-[10px] font-bold text-rose-500 bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-100 flex items-center gap-1.5 mt-1">
           <i class="pi pi-exclamation-circle"></i> {{ errors.foto_despues }}
        </p>
      </div>

      <!-- GPS y Ubicación Mejorada -->
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wide flex items-center gap-1">
          <i class="pi pi-map-marker text-[#850D12]"></i>
          Ubicación del trabajo <span class="text-rose-500">*</span>
        </label>

        <!-- Autocompletador de dirección -->
        <div class="relative w-full mb-1 group">
          <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-brand-red transition-colors"></i>
          <input 
            ref="addressInput"
            v-model="direccionManual"
            type="text" 
            placeholder="Busca dirección o usa GPS..." 
            class="w-full text-sm pl-11 pr-4 py-3 rounded-xl border border-app-border bg-app-secondary focus:bg-white focus:border-brand-red outline-none transition-all shadow-sm"
            :disabled="gpsLoading"
            autocomplete="off"
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
          <button
            @click="capturarGPS" :disabled="gpsLoading"
            class="flex items-center justify-center gap-2 py-2.5 rounded-xl font-bold text-sm border transition bg-app-secondary border-app-border text-[#1D1D1F] dark:text-white hover:bg-app-bg disabled:opacity-50"
          >
            <i :class="gpsLoading ? 'pi pi-spin pi-spinner' : 'pi pi-crosshairs'"></i>
            GPS
          </button>
          <div v-if="gpsOk" class="flex items-center justify-center gap-2 px-3 py-2 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-600 text-xs font-bold animate-fade-in">
            <i class="pi pi-check-circle"></i>
            Ubicación confirmada
          </div>
        </div>

        <p v-if="gpsError" class="text-[10px] text-rose-500 px-1">{{ gpsError }}</p>

        <div
          ref="miniMapContainer"
          class="w-full rounded-xl border border-app-border mt-1 overflow-hidden"
          style="height:220px"
        >
          <div v-if="!mapsReady" class="w-full h-full flex flex-col items-center justify-center bg-app-secondary text-gray-400 gap-2">
             <i class="pi pi-spin pi-spinner text-xl"></i>
             <span class="text-[10px] font-bold uppercase tracking-widest">Cargando Mapa...</span>
          </div>
        </div>
        <p class="text-[10px] text-[#86868B] dark:text-[#A1A1A6] italic px-1">
          <i class="pi pi-info-circle mr-1"></i>Puedes ajustar el marcador haciendo clic en el mapa.
        </p>
      </div>

      <!-- Notas (opcional) -->
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wide">
          <i class="pi pi-pencil text-[#86868B] mr-1"></i>Notas de cierre
          <span class="text-[#86868B] font-normal normal-case tracking-normal">(opcional)</span>
        </label>
        <Textarea v-model="notas" :rows="3" placeholder="Describe brevemente el trabajo realizado..." class="w-full text-sm resize-none" />
      </div>

      <p v-if="cierreError" class="text-sm text-rose-600 bg-rose-50 rounded-xl px-3 py-2 border border-rose-200">
        <i class="pi pi-exclamation-circle mr-1"></i>{{ cierreError }}
      </p>
    </div>

    <template #footer>
      <div class="flex gap-2 justify-end">
        <button 
          @click="onHide" 
          :disabled="cerrando"
          class="px-5 py-2.5 rounded-xl text-sm font-semibold border border-app-border hover:bg-app-secondary transition disabled:opacity-50"
        >
          Cancelar
        </button>
        <button
          @click="confirmarCierre"
          :disabled="!fotoDespues || !gpsOk || cerrando"
          class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold bg-brand-gradient text-white hover:opacity-90 shadow-lg shadow-brand-red/20 transition disabled:opacity-50"
        >
          <i :class="cerrando ? 'pi pi-spin pi-spinner' : 'pi pi-check'"></i>
          {{ cerrando ? 'Enviando...' : 'Confirmar cierre' }}
        </button>
      </div>
    </template>
  </Dialog>
</template>

<script>
import Dialog from 'primevue/dialog'
import Textarea from 'primevue/textarea'
import axios from 'axios'
import { loadGoogleMapsApi, DEFAULT_CENTER } from '@/utils/mapUtils.js'

export default {
  name: 'CerrarOrdenModal',
  components: { Dialog, Textarea },
  props: {
    visible: Boolean,
    tarea: Object
  },
  emits: ['update:visible', 'success'],
  data() {
    return {
      fotoDespues: null,
      fotoPreview: null,
      latCierre: null,
      lngCierre: null,
      gpsLoading: false,
      gpsOk: false,
      gpsAccuracy: null,
      gpsError: null,
      notas: '',
      cerrando: false,
      cierreError: null,
      errors: {},
      
      // Mapas y Autocomplete
      mapsReady: false,
      miniMap: null,
      marker: null,
      autocomplete: null,
      direccionManual: '',
      geocoder: null,

      // Cámara
      cameraActiva: false,
      stream: null,
    }
  },
  watch: {
    visible(val) {
      if (val) {
        this.resetForm()
        this.$nextTick(() => {
          this.initMiniMap()
        })
      } else {
        this.limpiarRecursos()
      }
    }
  },
  methods: {
    resetForm() {
      this.fotoDespues = null
      this.fotoPreview = null
      this.latCierre = null
      this.lngCierre = null
      this.gpsOk = false
      this.gpsError = null
      this.notas = ''
      this.cerrando = false
      this.cierreError = null
      this.errors = {}
      this.direccionManual = ''
      this.cameraActiva = false
    },
    limpiarRecursos() {
      this.miniMap = null
      this.marker = null
      this.autocomplete = null
      this.detenerCamara()
    },
    onHide() {
      this.$emit('update:visible', false)
    },
    async initMiniMap() {
      console.log('--- Iniciando initMiniMap ---')
      try {
        const googleMaps = await loadGoogleMapsApi()
        this.mapsReady = true
        this.geocoder = new googleMaps.Geocoder()

        const isDark = document.documentElement.classList.contains('dark')
        const initialPos = DEFAULT_CENTER

        this.miniMap = new googleMaps.Map(this.$refs.miniMapContainer, {
          center: initialPos,
          zoom: 16,
          disableDefaultUI: true,
          zoomControl: true,
          styles: isDark ? [
            { elementType: 'geometry', stylers: [{ color: '#242f3e' }] },
            { elementType: 'labels.text.stroke', stylers: [{ color: '#242f3e' }] },
            { elementType: 'labels.text.fill', stylers: [{ color: '#746855' }] },
          ] : []
        })

        this.marker = new googleMaps.Marker({
          position: initialPos,
          map: this.miniMap,
          draggable: true,
          animation: googleMaps.Animation.DROP
        })

        this.miniMap.addListener('click', (e) => {
          this.setLocation(e.latLng.lat(), e.latLng.lng(), true)
        })
        this.marker.addListener('dragend', () => {
          const pos = this.marker.getPosition()
          this.setLocation(pos.lat(), pos.lng(), true)
        })

        const inputEl = this.$refs.addressInput
        this.autocomplete = new googleMaps.places.Autocomplete(inputEl, {
          componentRestrictions: { country: 'mx' },
          fields: ['geometry', 'formatted_address']
        })

        this.autocomplete.addListener('place_changed', () => {
          const place = this.autocomplete.getPlace()
          if (place.geometry && place.geometry.location) {
            const loc = place.geometry.location
            this.setLocation(loc.lat(), loc.lng(), false)
            this.direccionManual = place.formatted_address
          }
        })
      } catch (e) {
        console.error("Error crítico en initMiniMap:", e)
      }
    },
    setLocation(lat, lng, updateText = true) {
      this.latCierre = lat
      this.lngCierre = lng
      this.gpsOk = true
      
      const pos = { lat, lng }
      if (this.marker) {
        this.marker.setPosition(pos)
        this.miniMap.panTo(pos)
      }

      if (updateText && this.geocoder) {
        this.geocoder.geocode({ location: pos }, (results, status) => {
          if (status === 'OK' && results[0]) {
            this.direccionManual = results[0].formatted_address
          }
        })
      }
    },
    onFotoChange(e) {
      const file = e.target.files[0]
      if (!file) return
      this.fotoDespues = file
      this.fotoPreview = URL.createObjectURL(file)
      this.errors = { ...this.errors, foto_despues: null }
    },
    capturarGPS() {
      if (!navigator.geolocation) { 
        this.gpsError = 'Tu dispositivo no soporta geolocalización.'
        return 
      }
      this.gpsLoading = true; this.gpsError = null;
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          this.setLocation(pos.coords.latitude, pos.coords.longitude, true)
          this.gpsAccuracy = Math.round(pos.coords.accuracy)
          this.gpsLoading = false
        },
        (err) => { 
          this.gpsError = `No se pudo obtener la ubicación: ${err.message}`
          this.gpsLoading = false 
        },
        { enableHighAccuracy: true, timeout: 10000 }
      )
    },
    confirmarCierre() {
      if (!this.fotoDespues || !this.gpsOk) return
      this.cerrando = true; this.cierreError = null
      
      const form = new FormData()
      form.append('foto_despues', this.fotoDespues)
      form.append('lat_cierre', this.latCierre)
      form.append('lng_cierre', this.lngCierre)
      form.append('notas_cierre', this.notas)

      axios.post(`/trabajador/incidencias/${this.tarea.id}/cerrar`, form, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      }).then((response) => {
        this.$emit('success', this.tarea.id)
        this.onHide()
      }).catch(e => {
        const errs = e.response?.data?.errors
        if (errs) this.errors = Object.fromEntries(Object.entries(errs).map(([k,v]) => [k, v[0]]))
        else this.cierreError = 'Error al cerrar la orden. Intenta de nuevo.'
      }).finally(() => { 
        this.cerrando = false 
      })
    },
    async abrirCamara() {
      this.fotoPreview = null
      this.fotoDespues = null
      try {
        this.cameraActiva = true
        await this.$nextTick()
        this.stream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: 'environment' },
          audio: false
        })
        this.$refs.videoStream.srcObject = this.stream
      } catch (err) {
        console.error('Error al acceder a la cámara:', err)
        this.cameraActiva = false
      }
    },
    detenerCamara() {
      if (this.stream) {
        this.stream.getTracks().forEach(track => track.stop())
        this.stream = null
      }
      this.cameraActiva = false
    },
    capturarFoto() {
      const video = this.$refs.videoStream
      const canvas = this.$refs.photoCanvas
      canvas.width = video.videoWidth
      canvas.height = video.videoHeight
      const ctx = canvas.getContext('2d')
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height)
      canvas.toBlob((blob) => {
        const file = new File([blob], `cierre_${Date.now()}.jpg`, { type: 'image/jpeg' })
        this.fotoDespues = file
        this.fotoPreview = canvas.toDataURL('image/jpeg')
        this.detenerCamara()
        this.errors = { ...this.errors, foto_despues: null }
      }, 'image/jpeg', 0.85)
    },
  }
}
</script>

<style scoped>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fade-in { animation: fadeIn .35s ease-out forwards }
</style>
