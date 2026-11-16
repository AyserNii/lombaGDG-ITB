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

    document.querySelectorAll('[data-dashboard-chart-item]').forEach((item) => {
        item.addEventListener('click', () => {
            const chart = item.closest('[role="group"]');
            const detail = chart?.parentElement.querySelector('[data-dashboard-chart-detail]');
            if (!chart || !detail) return;

            chart.querySelectorAll('[data-dashboard-chart-item]').forEach((chartItem) => {
                chartItem.setAttribute('aria-pressed', chartItem === item ? 'true' : 'false');
            });

            detail.textContent = `${item.dataset.chartLabel}: ${item.dataset.chartValue}`;
        });
    });

    document.querySelectorAll('[data-food-like-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const button = form.querySelector('button[type="submit"]');
            const count = button?.querySelector('[data-food-like-count]');
            if (!button || !count || button.disabled) return;

            const previousLiked = button.getAttribute('aria-pressed') === 'true';
            const previousCount = Number(count.textContent || 0);
            const nextLiked = !previousLiked;
            const nextCount = Math.max(0, previousCount + (nextLiked ? 1 : -1));
            const updateLike = (liked, likeCount) => {
                button.setAttribute('aria-pressed', liked ? 'true' : 'false');
                button.setAttribute('aria-label', liked ? 'Hapus suka' : 'Sukai sesi');
                button.classList.toggle('bg-rose-50', liked);
                button.classList.toggle('text-rose-600', liked);
                button.classList.toggle('text-slate-500', !liked);
                const icon = button.querySelector('svg');
                if (icon) icon.setAttribute('fill', liked ? 'currentColor' : 'none');
                count.textContent = String(likeCount);
            };

            updateLike(nextLiked, nextCount);
            button.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(form),
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data.success) {
                    throw new Error(data.message || data.error || 'Suka sesi gagal diperbarui. Coba lagi.');
                }

                updateLike(data.liked, data.likes_count);
            } catch (error) {
                updateLike(previousLiked, previousCount);
                window.showErrorToast?.('Suka gagal diperbarui', error.message || 'Periksa koneksi internet lalu coba lagi.');
            } finally {
                button.disabled = false;
            }
        });
    });

    document.querySelectorAll('[data-food-photo-uploader]').forEach((uploader) => {
        const form = uploader.closest('[data-food-session-create-form]');
        const dropzone = uploader.querySelector('[data-food-photo-dropzone]');
        const fileInput = uploader.querySelector('[data-food-photo-input]');
        const imagePath = form?.querySelector('[data-food-photo-path]');
        const imageUrl = uploader.querySelector('[data-food-photo-url]');
        const placeholder = uploader.querySelector('[data-food-photo-placeholder]');
        const previewWrap = uploader.querySelector('[data-food-photo-preview-wrap]');
        const preview = uploader.querySelector('[data-food-photo-preview]');
        const photoLoading = uploader.querySelector('[data-food-photo-loading]');
        const photoName = uploader.querySelector('[data-food-photo-name]');
        const photoStatus = uploader.querySelector('[data-food-photo-status]');
        const removeButton = uploader.querySelector('[data-food-photo-remove]');
        const errorMessage = uploader.querySelector('[data-food-photo-error]');
        const progressWrap = uploader.querySelector('[data-food-photo-progress-wrap]');
        const progressBar = uploader.querySelector('[data-food-photo-progress-bar]');
        const progressLabel = uploader.querySelector('[data-food-photo-progress-label]');
        const progressValue = uploader.querySelector('[data-food-photo-progress-value]');
        const submitButton = form?.querySelector('button[type="submit"]');

        if (!form || !dropzone || !fileInput || !imagePath || !imageUrl || !placeholder || !previewWrap || !preview) return;

        let uploadSequence = 0;
        let previewSequence = 0;
        let isUploading = false;
        let isUrlPending = false;
        let isUrlInvalid = false;
        let localPreviewUrl = null;

        const setPreview = (src, name, status) => {
            if (localPreviewUrl && src !== localPreviewUrl) {
                URL.revokeObjectURL(localPreviewUrl);
                localPreviewUrl = null;
            }

            preview.src = src;
            placeholder.classList.add('hidden');
            previewWrap.classList.remove('hidden');
            previewWrap.classList.add('flex');
            if (photoName) photoName.textContent = name || 'Foto warung';
            if (photoStatus) photoStatus.textContent = status || '';
        };

        const clearPreview = () => {
            if (localPreviewUrl) {
                URL.revokeObjectURL(localPreviewUrl);
                localPreviewUrl = null;
            }
            preview.removeAttribute('src');
            placeholder.classList.remove('hidden');
            previewWrap.classList.add('hidden');
            previewWrap.classList.remove('flex');
            if (photoName) photoName.textContent = '';
            if (photoStatus) photoStatus.textContent = '';
        };

        const setError = (message, title = 'Gagal mengupload foto') => {
            if (errorMessage) {
                errorMessage.textContent = message;
                errorMessage.classList.remove('hidden');
            }
            window.showErrorToast?.(title, message);
        };

        const clearError = () => {
            if (errorMessage) {
                errorMessage.textContent = '';
                errorMessage.classList.add('hidden');
            }
        };

        const setProgress = (percent, label = 'Mengunggah foto…') => {
            progressWrap?.classList.remove('hidden');
            if (progressBar) progressBar.style.width = `${percent}%`;
            progressBar?.setAttribute('aria-valuenow', String(percent));
            if (progressLabel) progressLabel.textContent = label;
            if (progressValue) progressValue.textContent = `${percent}%`;
        };

        const hideProgress = () => {
            progressWrap?.classList.add('hidden');
            if (progressBar) progressBar.style.width = '0%';
        };

        const setLoading = (loading) => {
            photoLoading?.classList.toggle('hidden', !loading);
            photoLoading?.classList.toggle('flex', loading);
        };

        const uploadPhoto = async (file) => {
            const sequence = ++uploadSequence;
            previewSequence++;
            fileInput.value = '';
            isUrlPending = false;
            isUrlInvalid = false;
            isUploading = true;
            imageUrl.value = '';
            imagePath.value = '';
            clearError();

            if (localPreviewUrl) URL.revokeObjectURL(localPreviewUrl);
            localPreviewUrl = URL.createObjectURL(file);
            setPreview(localPreviewUrl, file.name, 'Mengunggah dan memeriksa foto…');
            setLoading(true);
            setProgress(0);
            if (submitButton) submitButton.disabled = true;

            const requestBody = new FormData();
            requestBody.append('photo', file);
            const csrfToken = form.querySelector('[name="_token"]')?.value;

            try {
                const response = await window.axios.post(form.dataset.photoUploadUrl, requestBody, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    onUploadProgress: (progressEvent) => {
                        if (sequence !== uploadSequence || !progressEvent.total) return;
                        const percent = Math.min(95, Math.round((progressEvent.loaded / progressEvent.total) * 95));
                        setProgress(percent, 'Mengunggah dan memeriksa foto…');
                    },
                });

                if (sequence !== uploadSequence) return;
                if (!response.data.success || !response.data.path || !response.data.url) {
                    throw new Error(response.data.message || 'Server tidak dapat memastikan foto tersimpan.');
                }

                imagePath.value = response.data.path;
                setLoading(false);
                setProgress(100, 'Foto berhasil diunggah.');
                setPreview(response.data.url, file.name, 'Foto berhasil diunggah dan siap dipakai.');
            } catch (error) {
                if (sequence !== uploadSequence) return;
                imagePath.value = '';
                fileInput.value = '';
                setLoading(false);
                clearPreview();
                hideProgress();
                const responseData = error.response?.data;
                const message = responseData?.errors?.photo?.[0]
                    || responseData?.message
                    || (error.response
                        ? 'Server gagal menerima foto. Periksa format dan ukuran file, lalu coba lagi.'
                        : 'Koneksi ke server terputus. Periksa jaringan lalu unggah foto lagi.');
                setError(message);
            } finally {
                if (sequence === uploadSequence) {
                    isUploading = false;
                    if (submitButton) submitButton.disabled = false;
                }
            }
        };

        const showLinkedImage = (url) => {
            const sequence = ++previewSequence;
            uploadSequence++;
            isUploading = false;
            isUrlPending = true;
            isUrlInvalid = false;
            imagePath.value = '';
            fileInput.value = '';
            hideProgress();
            clearError();
            if (localPreviewUrl) {
                URL.revokeObjectURL(localPreviewUrl);
                localPreviewUrl = null;
            }
            if (submitButton) submitButton.disabled = false;
            previewWrap.classList.remove('hidden');
            previewWrap.classList.add('flex');
            if (photoName) photoName.textContent = 'Foto dari tautan';
            if (photoStatus) photoStatus.textContent = 'Memeriksa tautan foto…';
            setLoading(true);
            preview.onload = () => {
                if (sequence !== previewSequence) return;
                isUrlPending = false;
                isUrlInvalid = false;
                setLoading(false);
                if (photoStatus) photoStatus.textContent = 'Tautan foto berhasil dimuat.';
            };
            preview.onerror = () => {
                if (sequence !== previewSequence) return;
                isUrlPending = false;
                isUrlInvalid = true;
                setLoading(false);
                clearPreview();
                setError('Foto dari tautan gagal dimuat. Periksa URL dan pastikan tautan mengarah langsung ke gambar.', 'Tautan foto gagal dimuat');
            };
            preview.src = url;
        };

        const clearPhoto = () => {
            uploadSequence++;
            previewSequence++;
            isUploading = false;
            isUrlPending = false;
            isUrlInvalid = false;
            setLoading(false);
            imagePath.value = '';
            imageUrl.value = '';
            fileInput.value = '';
            clearError();
            clearPreview();
            hideProgress();
            if (submitButton) submitButton.disabled = false;
        };

        fileInput.addEventListener('change', () => {
            const file = fileInput.files?.[0];
            if (file) uploadPhoto(file);
        });

        imageUrl.addEventListener('input', () => {
            const url = imageUrl.value.trim();
            if (url) showLinkedImage(url);
            else clearPhoto();
        });

        removeButton?.addEventListener('click', clearPhoto);
        dropzone.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropzone.classList.add('border-amber-500', 'bg-amber-50');
        });
        dropzone.addEventListener('dragleave', (event) => {
            if (event.relatedTarget instanceof Node && dropzone.contains(event.relatedTarget)) return;
            dropzone.classList.remove('border-amber-500', 'bg-amber-50');
        });
        dropzone.addEventListener('drop', (event) => {
            event.preventDefault();
            dropzone.classList.remove('border-amber-500', 'bg-amber-50');
            const file = event.dataTransfer?.files?.[0];
            if (!file) return;
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) {
                setError('Format atau ukuran foto tidak didukung. Pilih JPG, PNG, atau WEBP dengan ukuran maksimal 5 MB.');
                return;
            }
            uploadPhoto(file);
        });

        form.addEventListener('submit', (event) => {
            if (isUploading || isUrlPending || isUrlInvalid) {
                event.preventDefault();
                const message = isUploading
                    ? 'Tunggu sampai proses unggah foto selesai.'
                    : 'Pastikan tautan foto berhasil dimuat atau hapus foto tersebut sebelum menerbitkan sesi.';
                window.showErrorToast?.('Foto belum siap', message);
            }
        });

        if (imagePath.value && uploader.dataset.initialPhotoUrl) {
            setPreview(uploader.dataset.initialPhotoUrl, 'Foto yang sudah diunggah', 'Siap dipakai.');
        } else if (imageUrl.value.trim()) {
            showLinkedImage(imageUrl.value.trim());
        }
    });

    const foodCreateDialog = document.getElementById('food-session-create-dialog');
    const foodJoinDialog = document.getElementById('food-session-join-dialog');
    const foodDetailDialog = document.getElementById('food-session-detail-dialog');

    document.querySelectorAll('[data-open-food-create]').forEach((button) => {
        button.addEventListener('click', () => foodCreateDialog?.showModal());
    });

    function openFoodJoin(button) {
        const form = foodJoinDialog?.querySelector('[data-food-join-form]');
        const sessionId = foodJoinDialog?.querySelector('[data-food-join-id]');
        const title = foodJoinDialog?.querySelector('[data-food-join-title]');
        if (!foodJoinDialog || !form || !sessionId || !title) return;

        form.action = button.dataset.joinUrl;
        sessionId.value = button.dataset.sessionId;
        title.textContent = button.dataset.sessionTitle || 'Gabung sesi makan';
        foodJoinDialog.showModal();
    }

    document.querySelectorAll('[data-open-food-join]').forEach((button) => {
        button.addEventListener('click', () => openFoodJoin(button));
    });

    function openFoodSession(sessionId) {
        const template = Array.from(document.querySelectorAll('template[data-food-session-template]'))
            .find((item) => item.dataset.foodSessionTemplate === String(sessionId));
        const content = foodDetailDialog?.querySelector('[data-food-session-detail-content]');
        if (!foodDetailDialog || !template || !content) return;

        content.replaceChildren(template.content.cloneNode(true));
        foodDetailDialog.showModal();
    }

    document.querySelectorAll('[data-open-food-session]').forEach((button) => {
        button.addEventListener('click', () => openFoodSession(button.dataset.sessionId));
    });

    document.querySelectorAll('[data-close-food-dialog]').forEach((button) => {
        button.addEventListener('click', () => button.closest('dialog')?.close());
    });

    document.querySelectorAll('dialog:not([data-page-transition-loader])').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) dialog.close();
        });
    });

    const foodFormError = document.querySelector('[data-food-form-error]');
    if (foodFormError) {
        const { form, sessionId, message } = foodFormError.dataset;
        if (form === 'create') {
            foodCreateDialog?.showModal();
        } else if (form === 'join') {
            const joinButton = Array.from(document.querySelectorAll('[data-open-food-join]'))
                .find((button) => button.dataset.sessionId === sessionId);
            if (joinButton) openFoodJoin(joinButton);
        }

        window.showErrorToast?.('Periksa kembali data', message || 'Ada bagian yang perlu diperbaiki.');
    }

    const pageTransitionLoader = document.getElementById('page-transition-loader');

    if (pageTransitionLoader) {
        let pageLoaderTimeout;

        const hidePageLoader = () => {
            window.clearTimeout(pageLoaderTimeout);
            pageTransitionLoader.classList.remove('is-visible');
            if (pageTransitionLoader.open) pageTransitionLoader.close();
            document.body.removeAttribute('aria-busy');
        };

        const showPageLoader = () => {
            window.clearTimeout(pageLoaderTimeout);
            if (!pageTransitionLoader.open) pageTransitionLoader.showModal();
            document.body.setAttribute('aria-busy', 'true');
            requestAnimationFrame(() => pageTransitionLoader.classList.add('is-visible'));
            pageLoaderTimeout = window.setTimeout(hidePageLoader, 20000);
        };

        pageTransitionLoader.addEventListener('cancel', (event) => event.preventDefault());

        document.addEventListener('click', (event) => {
            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            if (!(event.target instanceof Element)) return;

            const link = event.target.closest('a[href]');
            if (!link || link.hasAttribute('download') || (link.target && link.target.toLowerCase() !== '_self')) return;
            if (link.rel.split(/\s+/).includes('external')) return;

            const destination = new URL(link.href, window.location.href);
            if (!['http:', 'https:'].includes(destination.protocol) || destination.origin !== window.location.origin) {
                return;
            }

            if (destination.pathname === window.location.pathname && destination.search === window.location.search) {
                return;
            }

            showPageLoader();
        });

        document.addEventListener('submit', (event) => {
            if (event.defaultPrevented || !(event.target instanceof HTMLFormElement)) return;

            const form = event.target;
            if (form.method === 'dialog' || (form.target && form.target.toLowerCase() !== '_self')) return;

            const destination = new URL(form.action, window.location.href);
            if (!['http:', 'https:'].includes(destination.protocol) || destination.origin !== window.location.origin) {
                return;
            }

            showPageLoader();
        });

        window.addEventListener('pageshow', hidePageLoader);
    }
