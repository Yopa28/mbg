<script setup>
const props = defineProps({
    documentations: {
        type: Array,
        default: () => [],
    },
});

const categories = [
    {
        value: 'preparation',
        label: 'Persiapan Bahan',
        description: 'Dokumentasi proses persiapan bahan makanan.',
    },
    {
        value: 'cooking',
        label: 'Proses Memasak',
        description: 'Dokumentasi proses pengolahan dan memasak.',
    },
    {
        value: 'temperature_check',
        label: 'Pemeriksaan Suhu',
        description: 'Dokumentasi pemeriksaan suhu makanan.',
    },
    {
        value: 'serving',
        label: 'Penyajian',
        description: 'Dokumentasi proses penataan dan penyajian makanan.',
    },
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

const getItems = (category) => {
    return props.documentations.filter(
        (item) => item.category === category,
    );
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
        class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-16"
    >
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#a87532]">
                Transparansi proses dapur
            </p>
            <h2 class="mt-2 text-2xl font-black text-[#173060] sm:text-3xl">
                Dokumentasi Kegiatan Dapur
            </h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                Dokumentasi proses pengolahan makanan yang disediakan oleh SPPG.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
    <article
        v-for="item in documentations"
        :key="item.id"
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
    >
        <!-- Foto -->
        <div class="aspect-video w-full overflow-hidden bg-slate-100">
            <img
                :src="getPhotoUrl(item.photo_path)"
                :alt="item.title || 'Dokumentasi dapur'"
                class="h-full w-full object-cover"
            />
        </div>

        <!-- Informasi -->
        <div class="p-5">
            <span class="inline-block rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                {{ getCategoryLabel(item.category) }}
            </span>

            <h4 class="mt-3 font-bold text-slate-800">
                {{ item.title || getCategoryLabel(item.category) }}
            </h4>

            <p
                v-if="item.description"
                class="mt-2 text-sm leading-6 text-slate-600"
            >
                {{ item.description }}
            </p>

            <div
                v-if="item.temperature !== null && item.temperature !== undefined"
                class="mt-4 rounded-lg bg-blue-50 px-3 py-2"
            >
                <p class="text-xs text-blue-600">
                    Hasil pemeriksaan suhu
                </p>
                <p class="mt-1 text-lg font-bold text-[#173060]">
                    {{ item.temperature }} °C
                </p>
            </div>

            <p
                v-if="item.captured_at"
                class="mt-4 border-t border-slate-100 pt-3 text-xs text-slate-400"
            >
                {{ formatDateTime(item.captured_at) }}
            </p>
        </div>
    </article>
</div>
    </section>
</template>
