<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout,
});

const props = defineProps({
    dailyMenu: {
        type: Object,
        required: true,
    },
});

const portions = [
    { value: 'large', label: 'Porsi Besar' },
    { value: 'small', label: 'Porsi Kecil' },
    { value: 'toddler', label: 'Porsi Balita' },
];

const portionGroups = computed(() => {
    return portions.map((portion) => ({
        ...portion,
        items: props.dailyMenu.items.filter(
            (item) => item.portion_type === portion.value
        ),
    }));
});

const categories = {
    preparation: 'Persiapan Bahan',
    cooking: 'Proses Memasak',
    temperature_check: 'Pemeriksaan Suhu',
    serving: 'Penyajian',
};

const formatDate = (value) => {
    if (!value) return '-';

    return new Date(`${value.substring(0, 10)}T00:00:00`).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const getPhotoUrl = (path) => {
    if (!path) return null;

    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) {
        return path;
    }

    return `/storage/${path}`;
};

const formatNumber = (value) => {
    if (value === null || value === undefined || value === '') return '-';

    return Number(value).toLocaleString('id-ID', {
        maximumFractionDigits: 2,
    });
};
</script>

<template>
    <Head :title="`Detail Riwayat - ${dailyMenu.title}`" />

    <div class="space-y-6">
        <Link
            href="/admin/history"
            class="inline-flex items-center gap-2 text-sm font-medium text-blue-700 hover:text-blue-800"
        >
            ← Kembali ke Riwayat Harian
        </Link>

        <section class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="rounded-full px-3 py-1 text-xs font-semibold"
                            :class="dailyMenu.status === 'published'
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-amber-50 text-amber-700'"
                        >
                            {{ dailyMenu.status === 'published' ? 'Dipublikasikan' : 'Draft' }}
                        </span>
                        <span class="text-xs text-slate-400">Arsip menu harian</span>
                    </div>

                    <h2 class="mt-3 text-xl font-bold text-slate-900">
                        {{ dailyMenu.title }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ formatDate(dailyMenu.menu_date) }}
                    </p>
                </div>

                <div class="rounded-lg bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    <p>{{ dailyMenu.items.length }} makanan</p>
                    <p>{{ dailyMenu.kitchen_documentations.length }} dokumentasi</p>
                </div>
            </div>

            <p v-if="dailyMenu.description" class="mt-5 border-t border-slate-100 pt-4 text-sm leading-6 text-slate-600">
                {{ dailyMenu.description }}
            </p>
        </section>

        <section class="space-y-4">
            <div>
                <h3 class="text-lg font-semibold text-slate-800">Rincian Menu dan Nutrisi</h3>
                <p class="mt-1 text-sm text-slate-500">
                    Data makanan dikelompokkan berdasarkan jenis porsi.
                </p>
            </div>

            <div
                v-for="portion in portionGroups"
                :key="portion.value"
                class="overflow-hidden rounded-xl border border-slate-200 bg-white"
            >
                <div class="border-b border-slate-100 bg-slate-50 px-5 py-4">
                    <h4 class="font-semibold text-slate-800">{{ portion.label }}</h4>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ portion.items.length }} jenis makanan
                    </p>
                </div>

                <div v-if="portion.items.length" class="divide-y divide-slate-100">
                    <div
                        v-for="item in portion.items"
                        :key="item.id"
                        class="p-5"
                    >
                        <div class="flex flex-col justify-between gap-2 sm:flex-row">
                            <div>
                                <h5 class="font-semibold text-slate-800">
                                    {{ item.menu_item?.name || 'Makanan tidak tersedia' }}
                                </h5>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ formatNumber(item.quantity) }} {{ item.unit }}
                                    <span v-if="item.menu_item?.category">
                                        · {{ item.menu_item.category }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Kalori</p>
                                <p class="mt-1 font-semibold text-slate-800">
                                    {{ formatNumber(item.calories) }} <span class="text-xs font-normal">kkal</span>
                                </p>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Protein</p>
                                <p class="mt-1 font-semibold text-slate-800">
                                    {{ formatNumber(item.protein) }} <span class="text-xs font-normal">g</span>
                                </p>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Karbohidrat</p>
                                <p class="mt-1 font-semibold text-slate-800">
                                    {{ formatNumber(item.carbs) }} <span class="text-xs font-normal">g</span>
                                </p>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Serat</p>
                                <p class="mt-1 font-semibold text-slate-800">
                                    {{ formatNumber(item.fiber) }} <span class="text-xs font-normal">g</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="px-5 py-6 text-sm text-slate-400">
                    Tidak ada makanan untuk jenis porsi ini.
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <div>
                <h3 class="text-lg font-semibold text-slate-800">Dokumentasi Kegiatan Dapur</h3>
                <p class="mt-1 text-sm text-slate-500">
                    Dokumentasi yang terkait dengan menu pada tanggal ini.
                </p>
            </div>

            <div
                v-if="dailyMenu.kitchen_documentations.length === 0"
                class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-sm text-slate-500"
            >
                Belum ada dokumentasi untuk menu ini.
            </div>

            <div v-else class="space-y-4">
                <article
                    v-for="photo in dailyMenu.kitchen_documentations"
                    :key="photo.id"
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white sm:flex"
                >
                    <div class="bg-slate-50 sm:w-64 sm:shrink-0">
                        <img
                            v-if="getPhotoUrl(photo.photo_path)"
                            :src="getPhotoUrl(photo.photo_path)"
                            :alt="photo.title || categories[photo.category] || 'Dokumentasi dapur'"
                            class="h-56 w-full object-cover sm:h-full"
                        />
                    </div>

                    <div class="flex-1 p-5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                {{ categories[photo.category] || photo.category }}
                            </span>
                            <span
                                class="rounded-full px-3 py-1 text-xs font-semibold"
                                :class="photo.is_published
                                    ? 'bg-emerald-50 text-emerald-700'
                                    : 'bg-slate-100 text-slate-600'"
                            >
                                {{ photo.is_published ? 'Dipublikasikan' : 'Draft' }}
                            </span>
                        </div>

                        <h4 class="mt-3 font-semibold text-slate-800">
                            {{ photo.title || categories[photo.category] || 'Dokumentasi dapur' }}
                        </h4>

                        <p v-if="photo.description" class="mt-2 text-sm leading-6 text-slate-600">
                            {{ photo.description }}
                        </p>

                        <p v-if="photo.temperature !== null" class="mt-3 text-sm text-slate-600">
                            Suhu: <strong>{{ formatNumber(photo.temperature) }} °C</strong>
                        </p>

                        <p v-if="photo.captured_at" class="mt-3 text-xs text-slate-400">
                            Waktu dokumentasi:
                            {{ new Date(photo.captured_at).toLocaleString('id-ID') }}
                        </p>
                    </div>
                </article>
            </div>
        </section>
    </div>
</template>
