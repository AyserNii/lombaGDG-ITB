import './bootstrap';

window.animateReactionParticles = function (button, reaction) {
    if (!button || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    button.classList.add('relative');
    const directions = [-32, -16, 0, 16, 32];
    directions.forEach((offset, index) => {
        const particle = document.createElement('span');
        particle.className = 'reaction-particle';
        particle.setAttribute('aria-hidden', 'true');
        particle.style.setProperty('--reaction-x', `${offset}px`);
        particle.style.setProperty('--reaction-delay', `${index * 24}ms`);
        particle.innerHTML = reaction === 'like'
            ? '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 10v12H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h3Zm2 0 4-8a3 3 0 0 1 3 3v3h5a2 2 0 0 1 2 2l-1.5 9a2 2 0 0 1-2 2H9V10Z"/></svg>'
            : '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 14V2h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-3Zm-2 0-4 8a3 3 0 0 1-3-3v-3H3a2 2 0 0 1-2-2l1.5-9A2 2 0 0 1 4.5 3H15v11Z"/></svg>';
        button.append(particle);
        particle.addEventListener('animationend', () => particle.remove(), { once: true });
    });
};

window.setPostSavedState = function (postId, isSaved) {
    document.querySelectorAll(`[data-save-post-id="${postId}"]`).forEach((button) => {
        button.dataset.saved = isSaved ? 'true' : 'false';
        button.setAttribute('aria-pressed', isSaved ? 'true' : 'false');
        button.title = isSaved ? 'Hapus dari simpanan' : 'Simpan ke koleksi privat';
        button.classList.toggle('text-[#4154e8]', isSaved);
        button.classList.toggle('text-slate-400', !isSaved);
        button.classList.toggle('hover:text-[#4154e8]', !isSaved);
        button.classList.toggle('hover:opacity-80', isSaved);

        const icon = button.querySelector('svg');
        if (icon) {
            icon.setAttribute('fill', isSaved ? 'currentColor' : 'none');
        }
    });
};

window.togglePasswordVisibility = function (button) {
    const container = button.closest('.relative') || button.parentElement;
    const input = container ? container.querySelector('input') : document.getElementById('password');
    if (!input) return;

    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    const eyeIcon = button.querySelector('.eye-icon');
    const eyeSlashIcon = button.querySelector('.eye-slash-icon');
    if (eyeIcon && eyeSlashIcon) {
        if (isPassword) {
            eyeIcon.classList.add('hidden');
            eyeSlashIcon.classList.remove('hidden');
        } else {
            eyeIcon.classList.remove('hidden');
            eyeSlashIcon.classList.add('hidden');
        }
    }
};

document.querySelectorAll('[data-password-toggle]:not([onclick])').forEach((button) => {
    button.addEventListener('click', () => {
        window.togglePasswordVisibility(button);
    });
});

document.querySelectorAll('[data-like]').forEach((button) => {
    button.addEventListener('click', () => {
        button.classList.toggle('text-rose-500');
        button.classList.toggle('text-[#4c54d2]');
    });
});

const locationSearchCache = new Map();

function formatLocation(properties) {
    return [...new Set([
        properties.name,
        properties.district,
        properties.city || properties.locality,
        properties.state,
    ].filter(Boolean))].join(', ');
}

document.querySelectorAll('[data-location-picker]').forEach((root) => {
    const countryInput = root.querySelector('[name="location_country"]');
    const locationInput = root.querySelector('[name="location"]');
    const latitudeInput = root.querySelector('[name="location_latitude"]');
    const longitudeInput = root.querySelector('[name="location_longitude"]');
    const suggestions = root.querySelector('[id$="-suggestions"]');
    const status = root.querySelector('[id$="-status"]');
    const gpsButton = root.querySelector('[data-location-gps]');

    if (!countryInput || !locationInput || !latitudeInput || !longitudeInput || !suggestions || !status || !gpsButton) {
        return;
    }

    const countryOptions = Array.from(countryInput.list?.options || []);
    const countryCodeByName = new Map(countryOptions.map((option) => [
        option.value.toLocaleLowerCase(),
        option.dataset.countryCode,
    ]));
    const countryNameByCode = new Map(countryOptions.map((option) => [
        option.dataset.countryCode,
        option.value,
    ]));

    let searchTimeout;
    let searchController;

    const setStatus = (message, isError = false) => {
        status.textContent = message;
        status.className = `min-h-4 text-[10px] ${isError ? 'text-red-600' : 'text-slate-500'}`;
    };

    const setLocation = (name, country, countryCode, latitude, longitude) => {
        locationInput.value = name;
        countryInput.value = countryNameByCode.get(countryCode?.toUpperCase()) || country || countryInput.value;
        latitudeInput.value = latitude.toFixed(7);
        longitudeInput.value = longitude.toFixed(7);
        suggestions.classList.add('hidden');
        setStatus('Lokasi dipilih. Kamu bisa mengubahnya sebelum menyimpan.');
    };

    const findPlace = async (latitude, longitude) => {
        const url = new URL('https://photon.komoot.io/reverse');
        url.search = new URLSearchParams({ lat: latitude, lon: longitude, lang: 'en' });
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error('Layanan pencarian lokasi tidak tersedia.');
        }

        const feature = (await response.json()).features?.[0];
        const name = feature ? formatLocation(feature.properties) : '';
        const country = feature?.properties?.country;
        const countryCode = feature?.properties?.countrycode;
        if (!name || !country) {
            throw new Error('Nama lokasi tidak ditemukan. Coba cari lokasi secara manual.');
        }

        return { name, country, countryCode };
    };

    countryInput.addEventListener('input', () => {
        locationInput.value = '';
        latitudeInput.value = '';
        longitudeInput.value = '';
        suggestions.classList.add('hidden');
        clearTimeout(searchTimeout);
        searchController?.abort();
        setStatus('Negara berubah. Masukkan kota atau wilayah untuk mencari.');
    });

    locationInput.addEventListener('input', () => {
        latitudeInput.value = '';
        longitudeInput.value = '';
        clearTimeout(searchTimeout);
        searchController?.abort();

        const query = locationInput.value.trim();
        const country = countryInput.value.trim();
        if (!country) {
            suggestions.classList.add('hidden');
            setStatus('Pilih negara terlebih dahulu.', true);
            return;
        }
        if (query.length < 2) {
            suggestions.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(async () => {
            const countryCode = countryCodeByName.get(country.toLocaleLowerCase());
            const searchCountry = countryCode
                ? new Intl.DisplayNames(['en'], { type: 'region' }).of(countryCode)
                : country;
            const cacheKey = `${countryCode || country.toLocaleLowerCase()}|${query.toLocaleLowerCase()}`;
            let features = locationSearchCache.get(cacheKey);

            try {
                if (!features) {
                    searchController = new AbortController();
                    const url = new URL('https://photon.komoot.io/api/');
                    url.search = new URLSearchParams({
                        q: `${query}, ${searchCountry}`,
                        limit: '8',
                        lang: 'en',
                    });
                    const response = await fetch(url, { signal: searchController.signal });
                    if (!response.ok) {
                        throw new Error('Saran lokasi tidak bisa dimuat.');
                    }

                    const data = await response.json();
                    features = data.features.filter((feature) =>
                        countryCode
                            ? feature.properties?.countrycode?.toUpperCase() === countryCode.toUpperCase()
                            : feature.properties?.country?.toLocaleLowerCase() === country.toLocaleLowerCase()
                    );
                    if (locationSearchCache.size >= 100) {
                        locationSearchCache.delete(locationSearchCache.keys().next().value);
                    }
                    locationSearchCache.set(cacheKey, features);
                }

                if (locationInput.value.trim() !== query || countryInput.value.trim() !== country) {
                    return;
                }

                suggestions.replaceChildren();
                features.forEach((feature) => {
                    const place = formatLocation(feature.properties);
                    if (!place) return;

                    const option = document.createElement('button');
                    option.type = 'button';
                    option.role = 'option';
                    option.className = 'block w-full rounded-lg px-3 py-2 text-left text-xs font-medium text-slate-700 hover:bg-indigo-50';
                    option.textContent = place;
                    option.addEventListener('click', () => {
                        const [longitude, latitude] = feature.geometry.coordinates;
                        setLocation(place, feature.properties.country, feature.properties.countrycode, latitude, longitude);
                    });
                    suggestions.append(option);
                });
                suggestions.classList.toggle('hidden', suggestions.childElementCount === 0);
                setStatus(suggestions.childElementCount
                    ? 'Pilih salah satu saran lokasi.'
                    : 'Tidak ada saran di negara ini. Periksa ejaan atau gunakan GPS.');
            } catch (error) {
                if (error.name === 'AbortError') return;
                suggestions.classList.add('hidden');
                setStatus(error.message || 'Saran lokasi gagal dimuat.', true);
            }
        }, 350);
    });

    gpsButton.addEventListener('click', () => {
        if (!navigator.geolocation) {
            setStatus('Browser ini tidak mendukung lokasi GPS.', true);
            return;
        }

        gpsButton.disabled = true;
        setStatus('Meminta izin lokasi GPS…');
        navigator.geolocation.getCurrentPosition(async ({ coords }) => {
            try {
                const place = await findPlace(coords.latitude, coords.longitude);
                setLocation(place.name, place.country, place.countryCode, coords.latitude, coords.longitude);
            } catch (error) {
                setStatus(error.message || 'Lokasi GPS berhasil didapat, tetapi nama lokasinya gagal dimuat.', true);
            } finally {
                gpsButton.disabled = false;
            }
        }, (error) => {
            const message = error.code === error.PERMISSION_DENIED
                ? 'Izin lokasi ditolak. Kamu masih bisa mencari atau mengetik lokasi secara manual.'
                : 'Lokasi GPS gagal didapat. Coba lagi atau cari lokasi secara manual.';
            setStatus(message, true);
            gpsButton.disabled = false;
        }, { enableHighAccuracy: false, timeout: 12000, maximumAge: 300000 });
    });

    document.addEventListener('click', (event) => {
        if (!root.contains(event.target)) {
            suggestions.classList.add('hidden');
        }
    });
});
