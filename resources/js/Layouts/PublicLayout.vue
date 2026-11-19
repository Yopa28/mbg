<script setup>
defineProps({
    sppg: {
        type: Object,
        default: null,
    },
});
</script>

<template>
    <div class="min-h-screen bg-[#f7f8fa] text-slate-800">
        <header class="relative overflow-hidden bg-[#142b52] text-white">
            <div class="pointer-events-none absolute -right-24 -top-40 h-[420px] w-[420px] rounded-full border border-white/[0.08]"></div>
            <div class="pointer-events-none absolute -right-8 -top-24 h-[320px] w-[320px] rounded-full border border-white/[0.08]"></div>
            <div class="pointer-events-none absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-[#d5ad69] to-transparent"></div>

            <div class="relative mx-auto flex max-w-[1280px] flex-col gap-6 px-5 py-6 sm:px-8 lg:flex-row lg:items-center lg:justify-between lg:py-7">
                <a href="/" class="flex items-center gap-4">
                    <div class="flex h-[58px] w-[58px] shrink-0 items-center justify-center rounded-2xl border border-[#d8b777]/50 bg-white/[0.08] text-lg font-black tracking-tight text-[#f1d39b]">
                        BGN
                    </div>

                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-[0.28em] text-[#e4c58f]">
                            Republik Indonesia
                        </p>
                        <p class="mt-1 text-lg font-bold leading-tight sm:text-xl">
                            Badan Gizi Nasional
                        </p>
                        <p class="mt-1 text-xs text-blue-100/75">
                            Informasi Menu Makan Bergizi Gratis
                        </p>
                    </div>
                </a>

                <div class="max-w-full rounded-xl border border-white/10 bg-white/[0.07] px-5 py-4 lg:max-w-[430px]">
                    <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#e4c58f]">
                        Satuan Pelayanan Pemenuhan Gizi
                    </p>
                    <p class="mt-1.5 text-sm font-bold leading-snug text-white sm:text-base">
                        {{ sppg?.name || 'Informasi SPPG' }}
                    </p>
                    <p v-if="sppg?.code" class="mt-1.5 text-[11px] text-blue-100/70">
                        ID SPPG: {{ sppg.code }}
                    </p>
                    <p v-if="sppg?.address" class="mt-1 text-xs leading-relaxed text-blue-100/75">
                        {{ sppg.address }}
                    </p>
                </div>
            </div>
        </header>

        <main class="min-h-[60vh]">
            <slot />
        </main>

        <footer class="mt-16 bg-[#142b52] text-white">
            <div class="mx-auto flex max-w-[1280px] flex-col gap-6 px-5 py-9 sm:px-8 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-bold">
                        Informasi Menu Makan Bergizi Gratis
                    </p>
                    <p class="mt-1 text-xs text-blue-100/65">
                        {{ sppg?.name || 'Satuan Pelayanan Pemenuhan Gizi' }}
                    </p>
                </div>

                <div class="text-xs leading-6 text-blue-100/65 md:text-right">
                    <p>Informasi menu dan kandungan gizi makanan</p>
                    <p>© {{ new Date().getFullYear() }} — Informasi Pelayanan Gizi</p>
                </div>
            </div>
            <div class="h-1 bg-[#c89b55]"></div>
        </footer>
    </div>
</template>
