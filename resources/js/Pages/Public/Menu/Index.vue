<script setup>
import { computed, onBeforeUnmount, onMounted, reactive } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import KitchenDocumentation from '@/Components/Public/KitchenDocumentation.vue';
import FeedbackForm from '@/Components/Public/FeedbackForm.vue';
import { getPublicPhotoUrl } from '@/Utils/publicPhotoUrl';

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
    { key: 'fiber', label: 'Serat Pangan', field: 'fiber', indicator: 'bg-emerald-600' },
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

const animatedNutrition = reactive({});
const nutritionAnimations = new WeakMap();

const getAnimatedNutrition = (portionKey, field) => {
    const items = getItems(portionKey);
    const total = getNutritionTotal(items, field);
    if (total === null) return null;

    const key = `${portionKey}-${field}`;
    return animatedNutrition[key] === undefined ? total : animatedNutrition[key];
};

const animateNutrition = (element) => {
    if (nutritionAnimations.has(element)) return;

    const value = Number(element.dataset.nutritionValue);
    const key = element.dataset.nutritionKey;
    if (!Number.isFinite(value) || !key) return;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        animatedNutrition[key] = value;
        return;
    }

    nutritionAnimations.set(element, true);
    const startedAt = performance.now();
    const duration = 650;

    const update = (now) => {
        const progress = Math.min((now - startedAt) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        animatedNutrition[key] = value * eased;
        if (progress < 1) requestAnimationFrame(update);
        else animatedNutrition[key] = value;
    };

    requestAnimationFrame(update);
};

const getPhoto = (item) => {
    return getPublicPhotoUrl(item?.menu_item?.photo);
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

let revealObserver;

onMounted(() => {
    const revealElements = document.querySelectorAll('.public-scroll-reveal');

    if (!revealElements.length) return;

    if (
        window.matchMedia('(prefers-reduced-motion: reduce)').matches ||
        !('IntersectionObserver' in window)
    ) {
        revealElements.forEach((element) => element.classList.add('is-visible'));
        return;
    }

    revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.classList.remove('is-pending');
            entry.target.classList.add('is-visible');
            entry.target.querySelectorAll('[data-nutrition-value]').forEach(animateNutrition);
            observer.unobserve(entry.target);
        });
    }, {
        rootMargin: '0px 0px -48px 0px',
        threshold: 0.08,
    });

    revealElements.forEach((element) => {
        const bounds = element.getBoundingClientRect();
        const isInViewport = bounds.top <= window.innerHeight && bounds.bottom >= 0;

        if (isInViewport) {
            element.classList.add('is-visible');
            element.querySelectorAll('[data-nutrition-value]').forEach(animateNutrition);
            return;
        }

        element.classList.add('is-pending');
        revealObserver.observe(element);
    });
});

onBeforeUnmount(() => revealObserver?.disconnect());
</script>

<template>
    <Head :title="menu?.title || 'Informasi Menu MBG'" />

    <div>
        <!-- Hero menu -->
        <section class="public-hero-ambient relative overflow-hidden border-b border-bgn-line">
            <div class="mx-auto max-w-[1440px] px-5 py-10 sm:px-8 sm:py-12 lg:px-10 lg:py-16">
                <div v-if="menu" class="grid items-end gap-8 lg:grid-cols-[minmax(0,1.55fr)_minmax(260px,0.45fr)] lg:gap-12">
                    <div class="public-page-enter min-w-0">
                        <div class="flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-[0.18em] text-bgn-gold-dark sm:text-[11px]">
                            <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5 shrink-0" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z"></path>
                                <path d="M14 3.75v4h4M8.5 12h7M8.5 15.5h7"></path>
                            </svg>
                            <span>DOKUMEN SAJIAN RESMI MBG</span>
                        </div>

                        <div class="public-gold-line mt-5 h-px w-20 bg-bgn-gold"></div>

                        <h1 class="mt-5 break-words text-4xl font-bold leading-[1.04] tracking-[-0.035em] text-bgn-navy sm:text-5xl lg:text-[64px]">
                            {{ menu.title }}
                        </h1>

                        <p v-if="menu.description" class="mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">
                            {{ menu.description }}
                        </p>
                    </div>

                    <div class="public-page-enter public-hero-date relative overflow-hidden border-l-[3px] border-bgn-gold bg-bgn-navy px-6 py-6 text-white shadow-[0_14px_32px_rgba(23,45,80,0.12)] sm:px-7 sm:py-7 lg:justify-self-end lg:w-full">
                        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-bgn-gold-soft">Menu Hari Ini</p>
                        <p class="mt-3 text-lg font-semibold leading-7 sm:text-xl">{{ menuDate }}</p>
                        <div class="mt-5 h-px w-full bg-white/10"></div>
                        <p class="mt-3 text-[10px] font-bold uppercase tracking-[0.16em] text-white/55">Jadwal penyajian resmi</p>
                    </div>
                </div>

                <div v-else class="public-page-enter max-w-5xl">
                    <div class="flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-[0.08em] text-bgn-gold-dark sm:text-[11px]">
                        <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5 shrink-0" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z"></path>
                            <path d="M14 3.75v4h4M8.5 12h7M8.5 15.5h7"></path>
                        </svg>
                        <span>DOKUMEN SAJIAN RESMI MBG</span>
                        <span aria-hidden="true" class="text-bgn-gold">•</span>
                        <span>MENU PAKET STANDAR NASIONAL</span>
                    </div>

                    <h1 class="mt-3 text-3xl font-bold leading-tight tracking-tight text-bgn-navy-deep sm:text-4xl">
                        Informasi Menu Makan Bergizi Gratis
                    </h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
                        Informasi menu dan kandungan gizi akan ditampilkan setelah menu harian dipublikasikan oleh SPPG.
                    </p>
                </div>
            </div>
        </section>

        <!-- Portion cards -->
        <section v-if="menu" class="public-scroll-reveal public-scroll-reveal--stagger mx-auto max-w-[1440px] px-5 pb-5 pt-10 sm:px-8 sm:pt-12 lg:px-10">
            <div class="public-reveal-item mb-8 flex flex-col gap-4 border-b border-bgn-line pb-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-bgn-gold-dark">Komposisi makanan</p>
                    <h2 class="mt-2 text-3xl font-bold tracking-[-0.025em] text-bgn-navy sm:text-[38px]">Informasi Setiap Porsi</h2>
                </div>
                <p class="max-w-md text-sm leading-6 text-slate-500 sm:text-right">Rincian susunan makanan dan kandungan gizi berdasarkan kategori porsi.</p>
            </div>

            <div class="grid items-start gap-6 md:grid-cols-2 xl:grid-cols-3 xl:gap-5">
                <article
                    v-for="(portion, index) in portions"
                    :key="portion.key"
                    :style="{ '--public-reveal-delay': `${(index + 1) * 55}ms` }"
                    class="public-reveal-item group overflow-hidden rounded-lg border border-bgn-line bg-white shadow-[0_8px_28px_rgba(23,45,80,0.06)] transition-[transform,box-shadow,border-color] duration-300 ease-out hover:-translate-y-1 hover:border-bgn-gold/50 hover:shadow-[0_16px_36px_rgba(23,45,80,0.12)] motion-reduce:transform-none"
                >
                    <div class="flex items-center justify-between gap-3 border-b border-bgn-gold/40 bg-bgn-navy px-5 py-4">
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-[0.16em] text-bgn-gold-soft">
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
                                <div class="public-image-reveal group/photo aspect-[16/10] w-full overflow-hidden rounded-md bg-slate-100">
                                    <img
                                        v-if="getPhoto(item)"
                                        :src="getPhoto(item)"
                                        :alt="item.menu_item?.name || 'Foto makanan'"
                                        loading="lazy"
                                        class="h-full w-full object-cover transition-transform duration-300 ease-out group-hover/photo:scale-[1.02] motion-reduce:transform-none"
                                    />
                                    <div v-else class="flex h-full w-full items-center justify-center bg-bgn-surface px-4 text-center">
                                        <div>
                                            <svg viewBox="0 0 32 32" fill="none" class="mx-auto h-8 w-8 text-bgn-gold-dark/70" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                                                <circle cx="16" cy="16" r="10.5"></circle>
                                                <circle cx="16" cy="16" r="6.5"></circle>
                                                <path d="M4.5 8v7M2.5 10.5h4M27.5 8v7M25.5 10.5h4"></path>
                                            </svg>
                                            <p class="mt-3 text-sm font-semibold text-slate-600">
                                                Foto makanan belum tersedia
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-start justify-between gap-4 px-1 pt-3">
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-bold leading-snug text-bgn-navy">
                                            {{ item.menu_item?.name || 'Makanan' }}
                                        </h4>
                                        <p v-if="item.menu_item?.category" class="mt-1 text-[11px] text-slate-400">
                                            {{ item.menu_item.category }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ formatNumber(Number(item.quantity)) }} {{ item.unit }}
                                        </p>
                                    </div>
                                    <p v-if="item.calories !== null && item.calories !== undefined" class="shrink-0 rounded-sm bg-bgn-gold-pale px-2.5 py-1 text-[11px] font-bold text-bgn-gold-dark">
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

                            <div class="mt-3 flex items-center justify-between gap-3 rounded-lg border border-bgn-line bg-bgn-surface px-4 py-3">
                                <p class="text-xs text-slate-500">Total energi</p>
                                <p
                                    v-if="getNutritionTotal(getItems(portion.key), 'calories') !== null"
                                    class="public-nutrition-value text-lg font-bold tracking-tight text-bgn-navy tabular-nums"
                                    :data-nutrition-key="`${portion.key}-calories`"
                                    :data-nutrition-value="getNutritionTotal(getItems(portion.key), 'calories')"
                                >
                                    {{ formatNumber(getAnimatedNutrition(portion.key, 'calories')) }}
                                    <span class="text-xs font-semibold">kkal</span>
                                </p>
                                <p v-else class="text-lg font-bold tracking-tight text-bgn-navy">
                                    {{ getNutritionTotal(getItems(portion.key), 'calories') === null
                                        ? 'Belum lengkap' : '' }}
                                </p>
                            </div>

                            <div class="mt-3 grid grid-cols-3 divide-x divide-slate-100 border-y border-slate-100 py-3">
                                <div class="px-2 first:pl-0">
                                    <p class="text-[11px] text-slate-500">Protein</p>
                                    <p
                                        v-if="getNutritionTotal(getItems(portion.key), 'protein') !== null"
                                        class="public-nutrition-value mt-1 text-sm font-bold text-slate-800 tabular-nums"
                                        :data-nutrition-key="`${portion.key}-protein`"
                                        :data-nutrition-value="getNutritionTotal(getItems(portion.key), 'protein')"
                                    >
                                        {{ formatNumber(getAnimatedNutrition(portion.key, 'protein')) }} g
                                    </p>
                                    <p v-else class="mt-1 text-sm font-bold text-slate-800">-</p>
                                </div>
                                <div class="px-2">
                                    <p class="text-[11px] text-slate-500">Karbohidrat</p>
                                    <p
                                        v-if="getNutritionTotal(getItems(portion.key), 'carbs') !== null"
                                        class="public-nutrition-value mt-1 text-sm font-bold text-slate-800 tabular-nums"
                                        :data-nutrition-key="`${portion.key}-carbs`"
                                        :data-nutrition-value="getNutritionTotal(getItems(portion.key), 'carbs')"
                                    >
                                        {{ formatNumber(getAnimatedNutrition(portion.key, 'carbs')) }} g
                                    </p>
                                    <p v-else class="mt-1 text-sm font-bold text-slate-800">-</p>
                                </div>
                                <div class="px-2">
                                    <p class="text-[11px] text-slate-500">Serat</p>
                                    <p
                                        v-if="getNutritionTotal(getItems(portion.key), 'fiber') !== null"
                                        class="public-nutrition-value mt-1 text-sm font-bold text-slate-800 tabular-nums"
                                        :data-nutrition-key="`${portion.key}-fiber`"
                                        :data-nutrition-value="getNutritionTotal(getItems(portion.key), 'fiber')"
                                    >
                                        {{ formatNumber(getAnimatedNutrition(portion.key, 'fiber')) }} g
                                    </p>
                                    <p v-else class="mt-1 text-sm font-bold text-slate-800">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <aside class="mt-6 flex items-start gap-3 rounded-lg border border-bgn-line border-l-2 border-l-bgn-gold bg-white px-5 py-4">
                <svg viewBox="0 0 24 24" fill="none" class="mt-0.5 h-5 w-5 shrink-0 text-bgn-gold-dark" stroke="currentColor" stroke-width="1.7">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 11v5M12 8h.01"></path>
                </svg>
                <div>
                    <p class="text-sm font-bold text-bgn-navy">
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
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-bgn-navy/5 text-bgn-navy">
                <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8" stroke="currentColor" stroke-width="1.6">
                    <path d="M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"></path>
                    <path d="M14 3v5h5M8 13h8M8 17h8"></path>
                </svg>
            </div>
            <h2 class="mt-5 text-xl font-bold text-bgn-navy">
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
            class="public-scroll-reveal mx-auto max-w-[1440px] px-5 py-12 sm:px-8 lg:px-10 lg:py-14"
        >
            <div class="overflow-hidden border-y border-bgn-line bg-white">
                <header class="grid gap-5 border-b border-bgn-line px-5 py-7 sm:px-8 lg:grid-cols-[1fr_0.5fr] lg:items-end lg:px-10 lg:py-9">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-bgn-gold-dark">Tabel Angka Kecukupan Gizi (AKG)</p>
                        <h2 class="mt-3 text-2xl font-bold tracking-[-0.025em] text-bgn-navy sm:text-[34px]">Transparansi Kandungan Nutrisi Lengkap</h2>
                    </div>
                    <p class="text-sm leading-6 text-slate-500 lg:text-right">Perhitungan komprehensif berdasarkan pedoman standar Permenkes RI No. 28 Tahun 2019.</p>
                </header>

                <div class="overflow-x-auto">
                    <table class="min-w-[620px] w-full table-fixed border-collapse text-left text-sm">
                        <caption class="sr-only">
                            Sumber bahan makanan untuk setiap unsur nutrisi pada menu.
                        </caption>
                        <thead>
                            <tr class="border-b border-bgn-line bg-bgn-surface text-[11px] font-bold uppercase tracking-[0.12em] text-slate-600">
                                <th scope="col" class="w-[36%] px-5 py-4 sm:px-10">
                                    Unsur Nutrisi
                                </th>
                                <th scope="col" class="px-5 py-4 sm:px-10">
                                    Sumber Bahan Makanan
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <tr
                                v-for="nutrient in nutritionRows"
                                :key="nutrient.key"
                                class="border-b border-slate-100 transition-colors duration-200 last:border-b-0 hover:bg-bgn-surface/70"
                            >
                                <th scope="row" class="px-5 py-5 font-bold text-slate-900 sm:px-10">
                                    <span class="flex items-center gap-2">
                                        <span
                                            class="h-2 w-2 shrink-0 rounded-full"
                                            :class="nutrient.indicator"
                                        ></span>
                                        <span>{{ nutrient.label }}</span>
                                    </span>
                                </th>
                                <td class="break-words px-5 py-5 leading-6 text-slate-600 sm:px-10">
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

                <p class="border-t border-slate-100 px-5 py-4 text-xs leading-5 text-slate-500 sm:px-8">
                    Sumber untuk energi, protein, karbohidrat, dan serat diambil dari item menu yang memiliki nilai nutrisi terkait. Baris nutrisi ditandai belum lengkap jika salah satu bahan pada kategori porsi belum memiliki nilainya.
                </p>
            </div>
        </section>

        <!-- Feedback penerima -->
        <FeedbackForm :daily-menu-id="menu?.id ?? null" />
    </div>
</template>
