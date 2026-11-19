<script setup>
const props = defineProps({
    documentations: {
        type: Array,
        default: () => [],
    },
});

const categories = [
    { value: 'preparation', label: 'Persiapan Bahan' },
    { value: 'cooking', label: 'Proses Memasak' },
    { value: 'temperature_check', label: 'Pemeriksaan Suhu' },
    { value: 'serving', label: 'Penyajian' },
];

const getPhotoUrl = (path) => {
    if (!path) return null;

    if (
        path.startsWith('http://') ||
        path.startsWith('https://') ||
        path.startsWith('/')
    ) {
        return path;
    }

    return `/storage/${path}`;
};

const getCategoryLabel = (value) => {
    return categories.find((category) => category.value === value)?.label || value;
};

const formatDateTime = (value) => {
    if (!value) return null;

    return new Date(value).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <section
        v-if="documentations.length"
        class="border-y border-slate-200/70 bg-[#f1f4f8]"
    >
        <div class="mx-auto max-w-[1280px] px-5 py-14 sm:px-8 lg:py-20">
            <div class="mb-9">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#a87532]">
                    Transparansi proses dapur
                </p>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-[#142b52] sm:text-4xl">
                    Dokumentasi Kegiatan Dapur
                </h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                    Lihat dokumentasi proses persiapan, pengolahan, pemeriksaan, hingga penyajian makanan.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="item in documentations"
                    :key="item.id"
                    class="group overflow-hidden rounded-[22px] border border-slate-200/80 bg-white shadow-[0_10px_35px_rgba(20,43,82,0.05)] transition-shadow duration-300 hover:shadow-[0_16px_42px_rgba(20,43,82,0.10)]"
                >
                    <div class="relative aspect-[4/3] w-full overflow-hidden bg-slate-100">
                        <img
                            v-if="getPhotoUrl(item.photo_path)"
                            :src="getPhotoUrl(item.photo_path)"
                            :alt="item.title || 'Dokumentasi dapur'"
                            loading="lazy"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]"
                        />
                        <div
                            v-else
                            class="flex h-full items-center justify-center text-sm text-slate-400"
                        >
                            Foto belum tersedia
                        </div>

                        <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-black/35 to-transparent"></div>

                        <span class="absolute bottom-4 left-4 rounded-full border border-white/30 bg-white/95 px-3 py-1.5 text-[11px] font-bold text-[#142b52] shadow-sm">
                            {{ getCategoryLabel(item.category) }}
                        </span>
                    </div>

                    <div class="p-5 sm:p-6">
                        <h3 class="text-base font-bold leading-snug text-[#142b52]">
                            {{ item.title || getCategoryLabel(item.category) }}
                        </h3>

                        <p
                            v-if="item.description"
                            class="mt-2 text-sm leading-6 text-slate-500"
                        >
                            {{ item.description }}
                        </p>

                        <div
                            v-if="item.temperature !== null && item.temperature !== undefined"
                            class="mt-5 flex items-center justify-between rounded-xl border border-[#e7ebf2] bg-[#f5f7fb] px-4 py-3"
                        >
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Hasil pemeriksaan suhu
                                </p>
                                <p class="mt-1 text-xl font-black text-[#142b52]">
                                    {{ item.temperature }} °C
                                </p>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#142b52]/5 text-[#142b52]">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.7">
                                    <path d="M14 14.76V5a3 3 0 0 0-6 0v9.76a5 5 0 1 0 6 0Z"></path>
                                    <path d="M11 11v7"></path>
                                </svg>
                            </div>
                        </div>

                        <p
                            v-if="item.captured_at"
                            class="mt-5 border-t border-slate-100 pt-4 text-xs text-slate-400"
                        >
                            {{ formatDateTime(item.captured_at) }}
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>
