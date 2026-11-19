<script setup>
const bgnLogoUrl = '/images/bgn-seal.png';
const dairiBatikUrl = '/images/dairi-batik.png';

defineProps({
    sppg: {
        type: Object,
        default: null,
    },
});
</script>

<template>
    <div class="public-page-motion min-h-screen bg-bgn-paper text-slate-800">
        <header class="relative border-b-2 border-bgn-gold bg-bgn-navy text-white shadow-sm">
            <div class="relative mx-auto flex max-w-[1440px] items-center gap-4 px-5 py-4 sm:gap-5 sm:px-8 sm:py-5 lg:px-10">
                <a href="/" class="group flex shrink-0 items-center rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-bgn-gold-soft focus-visible:ring-offset-4 focus-visible:ring-offset-bgn-navy">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full border-2 border-bgn-gold-soft bg-bgn-cream p-1 shadow-sm transition-transform duration-200 ease-out group-hover:scale-[1.02] motion-reduce:transform-none sm:h-[88px] sm:w-20">
                        <img
                            :src="bgnLogoUrl"
                            alt="Lambang Badan Gizi Nasional"
                            width="1122"
                            height="1112"
                            class="h-full w-full object-contain"
                            fetchpriority="high"
                            decoding="async"
                        />
                    </div>
                </a>

                <div class="min-w-0">
                    <p v-if="sppg?.code" class="text-[10px] font-medium tracking-wide text-white/75">
                        ID SPPG: {{ sppg.code }}
                    </p>
                    <p class="mt-0.5 text-base font-bold uppercase leading-tight sm:text-xl">
                        Badan Gizi Nasional Republik Indonesia
                    </p>
                    <div class="mt-1 flex flex-wrap items-baseline gap-x-3 gap-y-0.5">
                        <p class="text-xs font-bold uppercase tracking-wide text-bgn-gold-soft sm:text-sm">
                            {{ sppg?.name || 'Informasi SPPG' }}
                        </p>
                        <p v-if="sppg?.address" class="flex items-baseline gap-2 text-[10px] leading-relaxed text-blue-50/80 sm:text-[11px]">
                            <span aria-hidden="true">•</span>
                            {{ sppg.address }}
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <main class="relative isolate min-h-[60vh]">
            <img
                :src="dairiBatikUrl"
                alt=""
                aria-hidden="true"
                width="600"
                height="600"
                class="pointer-events-none absolute left-1/2 top-72 z-0 hidden w-[min(60vw,600px)] -translate-x-1/2 opacity-[0.025] mix-blend-multiply select-none sm:block"
                loading="lazy"
                decoding="async"
            />
            <div class="relative z-10">
                <slot />
            </div>
        </main>

        <footer class="border-t-2 border-bgn-gold bg-bgn-navy text-white">
            <div class="mx-auto flex max-w-[1440px] flex-col gap-6 px-5 py-8 sm:px-8 md:flex-row md:items-center md:justify-between lg:px-10">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-bgn-gold-soft/60 bg-bgn-cream p-1">
                        <img
                            :src="bgnLogoUrl"
                            alt="Lambang Badan Gizi Nasional"
                            width="1122"
                            height="1112"
                            class="h-full w-full object-contain"
                            loading="lazy"
                            decoding="async"
                        />
                    </div>
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide">
                            Badan Gizi Nasional Republik Indonesia
                        </p>
                        <p class="mt-1 text-xs text-blue-100/65">
                            Satuan Pelayanan Pemenuhan Gizi
                        </p>
                        <p class="mt-1 text-xs font-semibold text-bgn-gold-soft">
                            {{ sppg?.name || 'Satuan Pelayanan Pemenuhan Gizi' }}
                        </p>
                    </div>
                </div>

                <div class="text-xs leading-6 text-blue-100/65 md:text-right">
                    <p>Informasi menu dan kandungan gizi makanan</p>
                    <p>© {{ new Date().getFullYear() }} Badan Gizi Nasional Republik Indonesia</p>
                </div>
            </div>
        </footer>
    </div>
</template>
