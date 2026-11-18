<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

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
</script>

<template>
    <Head :title="menu?.title || 'Informasi Menu MBG'" />

    <div>
        <!-- Hero menu -->
        <section class="relative overflow-hidden bg-[#f3f5f8]">
            <div class="absolute inset-0 opacity-[0.035]" style="background-image: radial-gradient(#173060 1px, transparent 1px); background-size: 18px 18px;"></div>

            <div class="relative mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-16">
                <div class="max-w-4xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-[#c89446]/40 bg-[#c89446]/10 px-4 py-2 text-xs font-bold uppercase tracking-wider text-[#8b6128]">
                        <span class="h-2 w-2 rounded-full bg-[#c89446]"></span>
                        Informasi Menu Harian
                    </div>

                    <template v-if="menu">
                        <h1 class="mt-6 text-3xl font-black leading-tight tracking-tight text-[#173060] sm:text-5xl">
                            {{ menu.title }}
                        </h1>

                        <p v-if="menu.description" class="mt-4 max-w-3xl text-sm leading-7 text-slate-600 sm:text-base">
                            {{ menu.description }}
                        </p>

                        <div class="mt-6 inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#173060]/5 text-[#173060]">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                    <rect x="3.5" y="5" width="17" height="16" rx="2"></rect>
                                    <path d="M7.5 3v4M16.5 3v4M3.5 10h17"></path>
                                </svg>
                            </span>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Tanggal penyajian
                                </p>
                                <p class="mt-1 text-sm font-bold text-[#173060]">
                                    {{ menuDate }}
                                </p>
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <h1 class="mt-6 text-3xl font-black leading-tight tracking-tight text-[#173060] sm:text-5xl">
                            Informasi Menu Makan Bergizi Gratis
                        </h1>
                        <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
                            Informasi menu dan kandungan gizi akan ditampilkan setelah menu harian dipublikasikan oleh SPPG.
                        </p>
                    </template>
                </div>
            </div>
        </section>

        <!-- Portion cards -->
        <section v-if="menu" class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-16">
            <div class="mb-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#a87532]">
                    Komposisi makanan
                </p>
                <h2 class="mt-2 text-2xl font-black text-[#173060] sm:text-3xl">
                    Informasi Setiap Porsi
                </h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                    Rincian makanan dan total kandungan gizi ditampilkan berdasarkan kategori porsi.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <article
                    v-for="portion in portions"
                    :key="portion.key"
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >
                    <div
                        class="border-b px-5 py-5"
                        :class="{
                            'border-blue-100 bg-blue-50/70': portion.accent === 'blue',
                            'border-emerald-100 bg-emerald-50/70': portion.accent === 'emerald',
                            'border-amber-100 bg-amber-50/70': portion.accent === 'amber',
                        }"
                    >
                        <p
                            class="text-[10px] font-bold uppercase tracking-[0.18em]"
                            :class="{
                                'text-blue-700': portion.accent === 'blue',
                                'text-emerald-700': portion.accent === 'emerald',
                                'text-amber-700': portion.accent === 'amber',
                            }"
                        >
                            {{ portion.subtitle }}
                        </p>
                        <h3 class="mt-2 text-xl font-black text-[#173060]">
                            {{ portion.title }}
                        </h3>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ getItems(portion.key).length }} jenis makanan
                        </p>
                    </div>

                    <div class="p-5">
                        <div
                            v-if="getItems(portion.key).length"
                            class="space-y-4"
                        >
                            <div
                                v-for="item in getItems(portion.key)"
                                :key="item.id"
                                class="flex gap-3 border-b border-slate-100 pb-4 last:border-0 last:pb-0"
                            >
                                <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100">
                                    <img
                                        v-if="getPhoto(item)"
                                        :src="getPhoto(item)"
                                        :alt="item.menu_item?.name || 'Foto makanan'"
                                        class="h-full w-full object-cover"
                                    />
                                    <div v-else class="flex h-full w-full items-center justify-center text-slate-300">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-7 w-7" stroke="currentColor" stroke-width="1.5">
                                            <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                                            <circle cx="8.5" cy="9" r="1.5"></circle>
                                            <path d="m21 15-5-5L5 20"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h4 class="text-sm font-bold text-slate-800">
                                        {{ item.menu_item?.name || 'Makanan' }}
                                    </h4>
                                    <p v-if="item.menu_item?.category" class="mt-1 text-xs text-slate-400">
                                        {{ item.menu_item.category }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ formatNumber(Number(item.quantity)) }} {{ item.unit }}
                                    </p>
                                    <p v-if="item.calories !== null && item.calories !== undefined" class="mt-1 text-xs font-semibold text-[#a87532]">
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

                        <div v-if="getItems(portion.key).length" class="mt-6 border-t border-slate-200 pt-5">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">
                                Ringkasan nutrisi
                            </p>

                            <div class="mt-4 rounded-xl bg-[#f4f6fa] p-4">
                                <p class="text-xs text-slate-500">Total energi</p>
                                <p class="mt-1 text-2xl font-black text-[#173060]">
                                    {{ getNutritionTotal(getItems(portion.key), 'calories') === null
                                        ? 'Belum lengkap'
                                        : formatNumber(getNutritionTotal(getItems(portion.key), 'calories')) }}
                                    <span v-if="getNutritionTotal(getItems(portion.key), 'calories') !== null" class="text-xs font-semibold">kkal</span>
                                </p>
                            </div>

                            <div class="mt-3 grid grid-cols-3 gap-2">
                                <div class="rounded-lg border border-slate-100 p-3">
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

                            <div class="mt-3 flex items-center justify-between rounded-lg border border-slate-100 px-3 py-3">
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

        <!-- Information note -->
        <section v-if="menu" class="mx-auto max-w-7xl px-5 pb-14 sm:px-8">
            <div class="rounded-xl border border-[#c89446]/30 bg-[#c89446]/10 p-5">
                <p class="text-sm font-bold text-[#795321]">
                    Catatan informasi gizi
                </p>
                <p class="mt-1 text-xs leading-6 text-slate-600">
                    Nilai kandungan gizi ditampilkan berdasarkan data yang dimasukkan oleh pengelola menu. Total nutrisi hanya ditampilkan apabila data untuk seluruh makanan pada kategori porsi tersebut telah diisi.
                </p>
            </div>
        </section>
    </div>
</template>
