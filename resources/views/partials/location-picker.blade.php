@php
    $pickerId = $inputId;
    $countryId = $pickerId . '-country';
    $latitudeId = $pickerId . '-latitude';
    $longitudeId = $pickerId . '-longitude';
    $countryOptionsId = $pickerId . '-countries';
    $suggestionsId = $pickerId . '-suggestions';
    $statusId = $pickerId . '-status';
@endphp

@once
    <link rel="preconnect" href="https://photon.komoot.io">
@endonce

<div class="space-y-3" data-location-picker>
    <div>
        <label for="{{ $countryId }}" class="mb-1.5 block text-xs font-bold text-slate-900">Negara</label>
        <input id="{{ $countryId }}" type="text" name="location_country"
               value="{{ $country ?? 'Indonesia' }}" list="{{ $countryOptionsId }}"
               placeholder="Ketik atau pilih negara" autocomplete="country-name" required
               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-800 outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/15">
        <datalist id="{{ $countryOptionsId }}">
            @foreach ($countries as $countryCode => $countryName)
                <option value="{{ $countryName }}" data-country-code="{{ $countryCode }}"></option>
            @endforeach
        </datalist>
    </div>

    <div class="relative">
        <label for="{{ $pickerId }}" class="mb-1.5 block text-xs font-bold text-slate-900">{{ $label ?? 'Kota / Wilayah' }}</label>
        <input id="{{ $pickerId }}" type="text" name="location" value="{{ $value ?? '' }}"
               placeholder="Pilih negara, lalu ketik kota atau wilayah"
               autocomplete="off" {{ !empty($required) ? 'required' : '' }}
               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-800 outline-none transition focus:border-[#4154e8] focus:ring-2 focus:ring-[#4154e8]/15">
        <input id="{{ $latitudeId }}" type="hidden" name="location_latitude" value="{{ $latitude ?? '' }}">
        <input id="{{ $longitudeId }}" type="hidden" name="location_longitude" value="{{ $longitude ?? '' }}">
        <div id="{{ $suggestionsId }}" class="absolute inset-x-0 top-full z-30 mt-1 hidden max-h-56 overflow-y-auto rounded-xl border border-slate-200 bg-white p-1 shadow-xl" role="listbox"></div>
    </div>

    <div class="flex flex-wrap gap-2">
        <button type="button" data-location-gps
                class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] font-bold text-slate-700 transition hover:border-[#4154e8] hover:text-[#4154e8]">
            Gunakan lokasi GPS
        </button>
    </div>
    <p id="{{ $statusId }}" class="min-h-4 text-[10px] text-slate-500" aria-live="polite"></p>
</div>
