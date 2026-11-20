<script setup>
import { computed } from 'vue';
import { getPublicPhotoUrl } from '@/Utils/publicPhotoUrl';

const props = defineProps({
    documentations: {
        type: Array,
        default: () => [],
    },
});

const hasPhotographicDocumentation = computed(() => {
    return props.documentations.some((item) => getPublicPhotoUrl(item.photo_path));
});

const categories = [
    { value: 'preparation', label: 'Persiapan Bahan' },
    { value: 'cooking', label: 'Proses Memasak' },
    { value: 'temperature_check', label: 'Pemeriksaan Suhu' },
    { value: 'serving', label: 'Penyajian' },
];

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
        class="public-scroll-reveal public-scroll-reveal--stagger border-y border-bgn-line bg-bgn-paper"
    >
        <div class="mx-auto max-w-[1440px] px-5 py-10 sm:px-8 lg:px-10 lg:py-12">
            <div class="public-reveal-item mb-6">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-bgn-gold-dark">
                    Transparansi proses dapur
                </p>
                <h2 class="mt-2 text-2xl font-bold tracking-tight text-bgn-navy sm:text-3xl">
                    Dokumentasi Kegiatan Dapur
                </h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                    Lihat dokumentasi proses persiapan, pengolahan, pemeriksaan, hingga penyajian makanan.
                </p>
            </div>

            <div
                class="grid items-start grid-cols-1 gap-5 md:grid-cols-2"
                :class="documentations.length === 1
                    ? 'xl:grid-cols-1'
                    : documentations.length === 2
                        ? 'xl:grid-cols-2'
                        : hasPhotographicDocumentation
                            ? 'xl:auto-rows-fr xl:grid-cols-3'
                            : 'xl:grid-cols-3'"
            >
                <article
                    v-for="(item, index) in documentations"
                    :key="item.id"
                    :style="{ '--public-reveal-delay': `${Math.min(index + 1, 4) * 45}ms` }"
                    :class="hasPhotographicDocumentation && documentations.length >= 3 && index === 0
                        ? 'xl:col-span-2 xl:row-span-2 xl:flex xl:flex-col xl:self-stretch'
                        : hasPhotographicDocumentation && documentations.length >= 3 && index < 3
                            ? 'xl:col-start-3'
                            : ''"
                    class="public-reveal-item group overflow-hidden rounded-xl border border-bgn-line bg-white shadow-sm transition-[transform,box-shadow,border-color] duration-200 hover:-translate-y-0.5 hover:border-bgn-gold/40 hover:shadow-[0_8px_22px_rgba(20,43,82,0.08)] motion-reduce:transform-none"
                >
                    <div class="group/photo relative aspect-[16/10] w-full overflow-hidden bg-slate-100">
                        <img
                            v-if="getPublicPhotoUrl(item.photo_path)"
                            :src="getPublicPhotoUrl(item.photo_path)"
                            :alt="item.title || 'Dokumentasi dapur'"
                            loading="lazy"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover/photo:scale-[1.02] motion-reduce:transform-none"
                        />
                        <div
                            v-else
                            class="flex h-full flex-col items-center justify-center bg-bgn-surface px-4 text-center"
                        >
                            <svg viewBox="0 0 32 32" fill="none" class="h-8 w-8 text-bgn-gold-dark/70" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                                <rect x="4.5" y="6.5" width="23" height="19" rx="2"></rect>
                                <circle cx="11" cy="13" r="2"></circle>
                                <path d="m7 23 7-7 4 4 3-3 4 6"></path>
                            </svg>
                            <span class="mt-3 text-sm font-semibold text-slate-600">
                                Foto dokumentasi belum tersedia
                            </span>
                        </div>
                    </div>

                    <div
                        :class="hasPhotographicDocumentation && documentations.length >= 3 && index === 0
                            ? 'xl:flex xl:flex-1 xl:flex-col xl:justify-between'
                            : ''"
                        class="p-4 sm:p-5"
                    >
                        <span class="inline-flex rounded-sm border-l-2 border-bgn-gold bg-bgn-surface px-2 py-1 text-[10px] font-semibold text-slate-600">
                            {{ getCategoryLabel(item.category) }}
                        </span>
                        <h3 class="mt-2 text-base font-bold leading-snug text-bgn-navy">
                            {{ item.title || getCategoryLabel(item.category) }}
                        </h3>

                        <p
                            v-if="item.description"
                            class="mt-2 text-sm leading-6 text-slate-500"
                        >
                            {{ item.description }}
                        </p>



                        <p
                            v-if="item.captured_at"
                            class="mt-4 border-t border-slate-100 pt-3 text-xs text-slate-500"
                        >
                            {{ formatDateTime(item.captured_at) }}
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>
