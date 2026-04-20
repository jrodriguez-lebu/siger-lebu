@extends('layouts.guest')
@section('title', 'Reportar Emergencia')

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- Header --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-3xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-600 text-white font-bold text-sm">SG</div>
                <div>
                    <p class="font-bold text-gray-900 leading-tight text-sm">SIGER</p>
                    <p class="text-xs text-gray-500">Municipalidad de Lebu</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                Portal Interno →
            </a>
        </div>
    </header>

    <div class="max-w-3xl mx-auto px-4 py-8">

        {{-- Alerta de emergencia grave --}}
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 flex items-start gap-3">
            <span class="text-2xl">🚨</span>
            <div>
                <p class="font-semibold text-red-800 text-sm">¿Es una emergencia con riesgo de vida?</p>
                <p class="text-red-700 text-sm mt-1">Llama inmediatamente al <strong>133 (Carabineros)</strong>, <strong>132 (Bomberos)</strong> o <strong>131 (SAMU)</strong> antes de completar este formulario.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Reportar Emergencia</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Complete todos los campos para que podamos atenderte rápidamente</p>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('public.report.store') }}" enctype="multipart/form-data"
                      x-data="reportForm()" class="space-y-6">
                    @csrf

                    {{-- PASO 1: Datos del reportante --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-white text-xs">1</span>
                            Sus datos de contacto
                        </h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="form-label">Nombre completo <span class="text-red-500">*</span></label>
                                <input type="text" name="reported_by_name" value="{{ old('reported_by_name') }}"
                                       class="form-input @error('reported_by_name') border-red-500 @enderror"
                                       placeholder="Juan Pérez González">
                                @error('reported_by_name') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Teléfono de contacto <span class="text-red-500">*</span></label>
                                <input type="tel" name="reported_by_phone" value="{{ old('reported_by_phone') }}"
                                       class="form-input @error('reported_by_phone') border-red-500 @enderror"
                                       placeholder="+56 9 1234 5678">
                                @error('reported_by_phone') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- PASO 2: Información de la emergencia --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-white text-xs">2</span>
                            Información de la emergencia
                        </h3>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="form-label">Tipo de emergencia <span class="text-red-500">*</span></label>
                                <select name="type" class="form-select @error('type') border-red-500 @enderror">
                                    <option value="">— Selecciona —</option>
                                    <option value="incendio"           {{ old('type') == 'incendio' ? 'selected' : '' }}>🔥 Incendio</option>
                                    <option value="accidente_transito" {{ old('type') == 'accidente_transito' ? 'selected' : '' }}>🚗 Accidente de Tránsito</option>
                                    <option value="rescate"            {{ old('type') == 'rescate' ? 'selected' : '' }}>🆘 Rescate</option>
                                    <option value="inundacion"         {{ old('type') == 'inundacion' ? 'selected' : '' }}>🌊 Inundación</option>
                                    <option value="emergencia_medica"  {{ old('type') == 'emergencia_medica' ? 'selected' : '' }}>🏥 Emergencia Médica</option>
                                    <option value="derrumbe"           {{ old('type') == 'derrumbe' ? 'selected' : '' }}>⛰️ Derrumbe</option>
                                    <option value="otro"               {{ old('type') == 'otro' ? 'selected' : '' }}>⚠️ Otro</option>
                                </select>
                                @error('type') <p class="form-error">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="form-label">Personas afectadas <span class="text-red-500">*</span></label>
                                <input type="number" name="affected_people" value="{{ old('affected_people', 1) }}"
                                       min="0" max="9999"
                                       class="form-input @error('affected_people') border-red-500 @enderror">
                                @error('affected_people') <p class="form-error">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="form-label">Dirección exacta <span class="text-red-500">*</span></label>
                                <input type="text" name="address" value="{{ old('address') }}"
                                       class="form-input @error('address') border-red-500 @enderror"
                                       placeholder="Calle, número, referencia">
                                @error('address') <p class="form-error">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="form-label">Sector / Barrio</label>
                                <input type="text" name="sector" value="{{ old('sector') }}"
                                       class="form-input"
                                       placeholder="Ej: Centro, Hualpén, Millongue">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="form-label">Descripción de la situación <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="4"
                                      class="form-input @error('description') border-red-500 @enderror"
                                      placeholder="Describe lo que está ocurriendo con el mayor detalle posible: qué pasó, cuántas personas están involucradas, si hay heridos...">{{ old('description') }}</textarea>
                            @error('description') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- PASO 3: Fotos --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-white text-xs">3</span>
                            Fotografías (opcional)
                        </h3>

                        <div class="rounded-lg border-2 border-dashed border-gray-300 p-6 text-center hover:border-blue-400 transition cursor-pointer"
                             @click="$refs.photoInput.click()">
                            <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Haz clic para subir fotos</p>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP hasta 5MB · Máximo 5 fotos</p>
                            <input type="file" name="photos[]" multiple accept="image/*"
                                   class="hidden" x-ref="photoInput"
                                   @change="previewPhotos($event)">
                        </div>

                        {{-- Preview de fotos --}}
                        <div x-show="previews.length > 0" class="mt-4 grid grid-cols-3 gap-3">
                            <template x-for="(src, i) in previews" :key="i">
                                <div class="relative rounded-lg overflow-hidden aspect-square bg-gray-100">
                                    <img :src="src" class="h-full w-full object-cover">
                                    <button type="button" @click="removePreview(i)"
                                            class="absolute top-1 right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-white text-xs hover:bg-red-700">
                                        ✕
                                    </button>
                                </div>
                            </template>
                        </div>

                        @error('photos.*') <p class="form-error mt-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500 max-w-xs">
                            Al enviar aceptas que tus datos sean procesados por la Unidad de Emergencias de la Municipalidad de Lebu.
                        </p>
                        <button type="submit"
                                class="btn-danger px-6 py-2.5 text-sm font-semibold">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Enviar Reporte
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function reportForm() {
    return {
        previews: [],
        files: [],
        previewPhotos(event) {
            const newFiles = Array.from(event.target.files);
            newFiles.forEach(file => {
                if (this.previews.length >= 5) return;
                const reader = new FileReader();
                reader.onload = e => this.previews.push(e.target.result);
                reader.readAsDataURL(file);
            });
        },
        removePreview(index) {
            this.previews.splice(index, 1);
        }
    }
}
</script>
@endpush
@endsection
