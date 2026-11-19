<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import KitchenDocumentation from '@/Components/Public/KitchenDocumentation.vue';
import FeedbackForm from '@/Components/Public/FeedbackForm.vue';

defineOptions({
    layout: PublicLayout,
});

const props = defineProps({
    sppg: {
        type: Object,
        default: null,
    },
    menu: {
        type: Object,
        default: null,
    },
});

const portions = [
    {
        key: 'large',
        title: 'Porsi Besar',
        subtitle: 'Kategori porsi besar',
        description: 'Informasi susunan makanan dan kandungan gizi porsi besar.',
        accent: 'blue',
    },
    {
        key: 'small',
        title: 'Porsi Kecil',
        subtitle: 'Kategori porsi kecil',
        description: 'Informasi susunan makanan dan kandungan gizi porsi kecil.',
        accent: 'emerald',
    },
    {
        key: 'toddler',
        title: 'Porsi Balita',
        subtitle: 'Kategori porsi balita',
        description: 'Informasi susunan makanan dan kandungan gizi porsi balita.',
        accent: 'amber',
    },
];

const nutrientDefinitions = [
    { key: 'energy', label: 'Energi Total', field: 'calories', indicator: 'bg-amber-500' },
    { key: 'protein', label: 'Protein', field: 'protein', indicator: 'bg-emerald-500' },
    { key: 'carbs', label: 'Karbohidrat', field: 'carbs', indicator: 'bg-sky-500' },
    { key: 'fat', label: 'Lemak Sehat', field: 'fat', indicator: 'bg-amber-600' },
    { key: 'fiber', label: 'Serat Pangan', field: null, indicator: 'bg-emerald-600' },
    { key: 'iron', label: 'Zat Besi (Fe)', field: null, indicator: 'bg-rose-500' },
    { key: 'calcium', label: 'Kalsium (Ca)', field: null, indicator: 'bg-amber-400' },
];

const formatDate = (value) => {
    if (!value) return '-';

    const [year, month, day] = value.substring(0, 10).split('-');
    const date = new Date(Number(year), Number(month) - 1, Number(day));

    return new Intl.DateTimeFormat('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(date);
};

const formatNumber = (value) => {
    if (value === null || value === undefined) return '-';

    return new Intl.NumberFormat('id-ID', {
        maximumFractionDigits: 2,
    }).format(value);
};

const getItems = (portionType) => {
    return props.menu?.items?.filter(
        (item) => item.portion_type === portionType,
    ) || [];
};

const getNutritionTotal = (items, field) => {
    if (!items.length) return null;

    // Jangan menampilkan total seolah lengkap jika ada nilai yang belum diisi.
    if (items.some((item) => item[field] === null || item[field] === undefined || item[field] === '')) {
        return null;
    }

    return items.reduce((total, item) => total + Number(item[field]), 0);
};

const getWeightInGrams = (items) => {
    if (!items.length) return null;

    let total = 0;

    for (const item of items) {
        const quantity = Number(item.quantity);
        const unit = String(item.unit || '').trim().toLowerCase();

        if (!Number.isFinite(quantity)) return null;

        if (['g', 'gr', 'gram', 'grama'].includes(unit)) {
            total += quantity;
        } else if (['kg', 'kilogram', 'kilogram'].includes(unit)) {
            total += quantity * 1000;
        } else {
            return null;
        }
    }

    return total;
};

const getPhoto = (item) => {
    const photo = item?.menu_item?.photo;

    if (!photo) return null;
    if (photo.startsWith('http://') || photo.startsWith('https://') || photo.startsWith('/')) {
        return photo;
    }

    return `/storage/${photo}`;
};

const menuDate = computed(() => formatDate(props.menu?.menu_date));

const nutritionRows = computed(() => {
    const items = props.menu?.items || [];

    return nutrientDefinitions.map((nutrient) => {
        const sources = nutrient.field
            ? [...new Set(
                items
                    .filter((item) => Number(item[nutrient.field]) > 0)
                    .map((item) => item.menu_item?.name?.trim())
                    .filter(Boolean),
            )]
            : [];

        return { ...nutrient, sources };
    });
});
</script>

<template>
    <Head :title="menu?.title || 'Informasi Menu MBG'" />

    <div>
        <!-- Hero menu -->
        <section class="public-page-enter relative overflow-hidden bg-[#f8fafc]">
            <div class="relative mx-auto max-w-7xl px-5 py-6 sm:px-8 sm:py-7 lg:px-10">
                <div class="max-w-5xl">
                    <div class="flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-[0.08em] text-[#a64c14] sm:text-[11px]">
                        <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5 shrink-0" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z"></path>
                            <path d="M14 3.75v4h4M8.5 12h7M8.5 15.5h7"></path>
                        </svg>
                        <span>DOKUMEN SAJIAN RESMI MBG</span>
                        <span aria-hidden="true" class="text-[#c89446]">•</span>
                        <span>MENU PAKET STANDAR NASIONAL</span>
                    </div>

                    <template v-if="menu">
                        <h1 class="mt-2 text-2xl font-bold leading-tight tracking-tight text-[#142b52] sm:text-[28px]">
                            {{ menu.title }}
                        </h1>

                        <p v-if="menu.description" class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                            {{ menu.description }}
                        </p>

                        <div class="mt-4 border-t border-slate-200"></div>

                        <p class="mt-5 inline-flex items-center gap-2 rounded-full bg-[#142b52] px-3 py-1 text-sm font-semibold text-[#ffd35a] sm:text-base">
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 shrink-0" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <rect x="4" y="5.5" width="16" height="15" rx="2"></rect>
                                <path d="M8 3.5v4M16 3.5v4M4 10h16"></path>
                            </svg>
                            Menu Hari Ini: {{ menuDate }}
                        </p>
                    </template>

                    <template v-else>
                        <h1 class="mt-2 text-2xl font-bold leading-tight tracking-tight text-[#173060] sm:text-[28px]">
                            Informasi Menu Makan Bergizi Gratis
                        </h1>
                        <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
                            Informasi menu dan kandungan gizi akan ditampilkan setelah menu harian dipublikasikan oleh SPPG.
                        </p>
                        <div class="mt-4 border-t border-slate-200"></div>
                    </template>
                </div>
            </div>
        </section>

        <!-- Portion cards -->
        <section v-if="menu" class="public-page-enter public-page-enter-delayed mx-auto max-w-7xl px-5 pb-5 pt-10 sm:px-8 sm:pt-12 lg:px-10">
            <div class="mb-9">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#a87532]">
                    Komposisi makanan
                </p>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-[#142b52] sm:text-4xl">
                    Informasi Setiap Porsi
                </h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                    Rincian makanan dan total kandungan gizi ditampilkan berdasarkan kategori porsi.
                </p>
            </div>

            <div class="grid items-start gap-6 lg:grid-cols-3 lg:gap-5">
                <article
                    v-for="portion in portions"
                    :key="portion.key"
                    class="group overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-[0_10px_30px_rgba(20,43,82,0.06)] transition-[transform,box-shadow] duration-300 ease-out hover:-translate-y-1 hover:shadow-[0_16px_38px_rgba(20,43,82,0.10)]"
                >
                    <div class="flex items-center justify-between gap-3 border-b border-[#c89446]/40 bg-[#142b52] px-5 py-3.5">
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-[0.16em] text-[#e4c58f]">
                                {{ portion.subtitle }}
                            </p>
                            <h3 class="mt-1 text-base font-bold tracking-tight text-white">
                                {{ portion.title }}
                            </h3>
                        </div>
                        <p class="text-right text-[10px] text-blue-100/75">
                            {{ getItems(portion.key).length }} jenis makanan
                        </p>
                    </div>

                    <div class="p-4 sm:p-5">
                        <div
                            v-if="getItems(portion.key).length"
                            class="space-y-4"
                        >
                            <div
                                v-for="item in getItems(portion.key)"
                                :key="item.id"
                                class="border-b border-slate-100 pb-4 last:border-0 last:pb-0"
                            >
                                <div class="aspect-[2.2/1] w-full overflow-hidden rounded-xl bg-[#f1f3f6]">
                                    <img
                                        v-if="getPhoto(item)"
                                        :src="getPhoto(item)"
                                        :alt="item.menu_item?.name || 'Foto makanan'"
                                        loading="lazy"
                                        class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.03]"
                                    />
                                    <div v-else class="flex h-full w-full items-center justify-center text-slate-300">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-7 w-7" stroke="currentColor" stroke-width="1.5">
                                            <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                                            <circle cx="8.5" cy="9" r="1.5"></circle>
                                            <path d="m21 15-5-5L5 20"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="flex items-start justify-between gap-4 px-1 pt-3">
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-bold leading-snug text-[#142b52]">
                                            {{ item.menu_item?.name || 'Makanan' }}
                                        </h4>
                                        <p v-if="item.menu_item?.category" class="mt-1 text-[11px] text-slate-400">
                                            {{ item.menu_item.category }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ formatNumber(Number(item.quantity)) }} {{ item.unit }}
                                        </p>
                                    </div>
                                    <p v-if="item.calories !== null && item.calories !== undefined" class="shrink-0 rounded-full bg-[#f8f4eb] px-3 py-1 text-[11px] font-bold text-[#8b6128]">
                                        {{ formatNumber(Number(item.calories)) }} kkal
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-else class="rounded-xl border border-dashed border-slate-200 px-4 py-8 text-center">
                            <p class="text-sm font-medium text-slate-500">
                                Belum ada data makanan
                            </p>
                            <p class="mt-1 text-xs text-slate-400">
                                Informasi porsi ini belum tersedia.
                            </p>
                        </div>

                        <div v-if="getItems(portion.key).length" class="mt-4 border-t border-slate-100 pt-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">
                                Ringkasan nutrisi
                            </p>

                            <div class="mt-3 flex items-center justify-between gap-3 rounded-xl border border-[#e7ebf2] bg-[#f5f7fb] px-4 py-3">
                                <p class="text-xs text-slate-500">Total energi</p>
                                <p class="text-xl font-black tracking-tight text-[#142b52]">
                                    {{ getNutritionTotal(getItems(portion.key), 'calories') === null
                                        ? 'Belum lengkap'
                                        : formatNumber(getNutritionTotal(getItems(portion.key), 'calories')) }}
                                    <span v-if="getNutritionTotal(getItems(portion.key), 'calories') !== null" class="text-xs font-semibold">kkal</span>
                                </p>
                            </div>

                            <div class="mt-3 grid grid-cols-3 gap-2">
                                <div class="rounded-xl border border-slate-100 bg-white p-3">
                                    <p class="text-[10px] text-slate-400">Protein</p>
                                    <p class="mt-1 text-sm font-bold text-slate-800">
                                        {{ getNutritionTotal(getItems(portion.key), 'protein') === null
                                            ? '-'
                                            : `${formatNumber(getNutritionTotal(getItems(portion.key), 'protein'))} g` }}
                                    </p>
                                </div>
                                <div class="rounded-lg border border-slate-100 p-3">
                                    <p class="text-[10px] text-slate-400">Karbohidrat</p>
                                    <p class="mt-1 text-sm font-bold text-slate-800">
                                        {{ getNutritionTotal(getItems(portion.key), 'carbs') === null
                                            ? '-'
                                            : `${formatNumber(getNutritionTotal(getItems(portion.key), 'carbs'))} g` }}
                                    </p>
                                </div>
                                <div class="rounded-lg border border-slate-100 p-3">
                                    <p class="text-[10px] text-slate-400">Lemak</p>
                                    <p class="mt-1 text-sm font-bold text-slate-800">
                                        {{ getNutritionTotal(getItems(portion.key), 'fat') === null
                                            ? '-'
                                            : `${formatNumber(getNutritionTotal(getItems(portion.key), 'fat'))} g` }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between rounded-xl border border-slate-100 bg-white px-4 py-3">
                                <span class="text-xs text-slate-500">Total berat makanan</span>
                                <span class="text-sm font-bold text-[#173060]">
                                    {{ getWeightInGrams(getItems(portion.key)) === null
                                        ? 'Belum tersedia'
                                        : `${formatNumber(getWeightInGrams(getItems(portion.key)))} g` }}
                                </span>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <aside class="mt-6 flex items-start gap-3 rounded-xl border border-[#e7dfd0] border-l-4 border-l-[#c89446] bg-[#fbf9f4] px-5 py-4">
                <svg viewBox="0 0 24 24" fill="none" class="mt-0.5 h-5 w-5 shrink-0 text-[#9a6d2e]" stroke="currentColor" stroke-width="1.7">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 11v5M12 8h.01"></path>
                </svg>
                <div>
                    <p class="text-sm font-bold text-[#795321]">
                        Catatan informasi gizi
                    </p>
                    <p class="mt-1 text-xs leading-5 text-slate-600">
                        Nilai kandungan gizi ditampilkan berdasarkan data yang dimasukkan oleh pengelola menu. Total nutrisi hanya ditampilkan apabila data untuk seluruh makanan pada kategori porsi tersebut telah diisi.
                    </p>
                </div>
            </aside>
        </section>

        <!-- Empty state -->
        <section v-else class="mx-auto max-w-3xl px-5 py-16 text-center sm:px-8">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#173060]/5 text-[#173060]">
                <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8" stroke="currentColor" stroke-width="1.6">
                    <path d="M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"></path>
                    <path d="M14 3v5h5M8 13h8M8 17h8"></path>
                </svg>
            </div>
            <h2 class="mt-5 text-xl font-bold text-[#173060]">
                Belum ada menu yang dipublikasikan
            </h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">
                Silakan kunjungi kembali halaman ini setelah SPPG mempublikasikan menu harian.
            </p>
        </section>

        <!-- Kitchen documentation -->
        <KitchenDocumentation
            v-if="menu"
            :documentations="menu.kitchen_documentations || []"
        />

        <!-- Nutrition transparency -->
        <section
            v-if="menu"
            class="mx-auto max-w-7xl px-5 py-10 sm:px-8 lg:px-10 lg:py-12"
        >
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-[0_4px_16px_-2px_rgba(15,30,61,0.05)]">
                <header class="border-b border-slate-100 bg-[#fafbfd] px-5 py-5 sm:px-6">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        Tabel Angka Kecukupan Gizi (AKG)
                    </p>
                    <h2 class="mt-1 text-lg font-bold text-[#112347] sm:text-xl">
                        Transparansi Kandungan Nutrisi Lengkap
                    </h2>
                    <p class="mt-1 text-xs leading-5 text-slate-500 sm:text-sm">
                        Perhitungan komprehensif berdasarkan pedoman standar Permenkes RI No. 28 Tahun 2019.
                    </p>
                </header>

                <div class="overflow-x-auto">
                    <table class="w-full table-fixed border-collapse text-left text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700">
                                <th scope="col" class="w-[36%] px-3 py-3 sm:px-6">
                                    Unsur Nutrisi
                                </th>
                                <th scope="col" class="px-3 py-3 sm:px-6">
                                    Sumber Bahan Makanan
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <tr
                                v-for="nutrient in nutritionRows"
                                :key="nutrient.key"
                                class="transition-colors hover:bg-slate-50/60"
                            >
                                <th scope="row" class="px-3 py-3 font-semibold text-slate-900 sm:px-6 sm:py-3.5">
                                    <span class="flex items-center gap-2">
                                        <span
                                            class="h-2 w-2 shrink-0 rounded-full"
                                            :class="nutrient.indicator"
                                        ></span>
                                        <span>{{ nutrient.label }}</span>
                                    </span>
                                </th>
                                <td class="break-words px-3 py-3 leading-5 sm:px-6 sm:py-3.5">
                                    <span v-if="nutrient.sources.length">
                                        {{ nutrient.sources.join(', ') }}
                                    </span>
                                    <span v-else class="text-slate-400">
                                        {{ nutrient.field
                                            ? 'Belum ada item dengan catatan unsur nutrisi ini.'
                                            : 'Belum tersedia pada data menu.' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="border-t border-slate-100 px-5 py-3 text-[11px] leading-5 text-slate-500 sm:px-6">
                    Sumber untuk energi, protein, karbohidrat, dan lemak diambil dari item menu yang memiliki nilai nutrisi terkait. Data serat pangan, zat besi, dan kalsium belum tersedia pada struktur menu saat ini.
                </p>
            </div>
        </section>

        <!-- Feedback penerima -->
        <FeedbackForm :daily-menu-id="menu?.id ?? null" />
    </div>
</template>
