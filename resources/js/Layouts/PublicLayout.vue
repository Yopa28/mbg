<script setup>
defineProps({
    sppg: {
        type: Object,
        default: null,
    },
});
</script>

<template>
    <div class="min-h-screen bg-[#f8fafc] text-slate-800">
        <header class="relative overflow-hidden border-b-4 border-[#c89446] bg-gradient-to-r from-[#173060] via-[#1b366b] to-[#15284f] text-white">
            <div class="pointer-events-none absolute -right-20 -top-28 h-80 w-80 rounded-full border border-white/10"></div>
            <div class="pointer-events-none absolute -right-8 -top-16 h-64 w-64 rounded-full border border-white/10"></div>

            <div class="relative mx-auto flex max-w-7xl flex-col gap-5 px-5 py-6 sm:flex-row sm:items-center sm:justify-between sm:px-8">
                <a href="/" class="flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-[#e5c58e]/50 bg-white/10 text-xl font-black tracking-tight text-[#f2d49e] shadow-lg">
                        BGN
                    </div>

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-[#e7c58e]">
                            Republik Indonesia
                        </p>
                        <p class="mt-1 text-lg font-bold leading-tight sm:text-xl">
                            Badan Gizi Nasional
                        </p>
                        <p class="mt-1 text-xs text-blue-100">
                            Informasi Menu Makan Bergizi Gratis
                        </p>
                    </div>
                </a>

                <div class="rounded-xl border border-white/15 bg-white/10 px-4 py-3 sm:max-w-sm">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-[#e7c58e]">
                        Satuan Pelayanan Pemenuhan Gizi
                    </p>
                    <p class="mt-1 font-semibold">
                        {{ sppg?.name || 'Informasi SPPG' }}
                    </p>
                    <p v-if="sppg?.code" class="mt-1 text-xs text-blue-100">
                        ID SPPG: {{ sppg.code }}
                    </p>
                    <p v-if="sppg?.address" class="mt-1 text-xs leading-relaxed text-blue-100">
                        {{ sppg.address }}
                    </p>
                </div>
            </div>
        </header>

        <main class="min-h-[60vh]">
            <slot />
        </main>

        <footer class="mt-16 border-t-4 border-[#c89446] bg-[#15284f] text-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-5 px-5 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-8">
                <div>
                    <p class="font-bold">Informasi Menu Makan Bergizi Gratis</p>
                    <p class="mt-1 text-xs text-blue-200">
                        {{ sppg?.name || 'Satuan Pelayanan Pemenuhan Gizi' }}
                    </p>
                </div>

                <p class="text-xs leading-relaxed text-blue-200 sm:text-right">
                    Informasi menu dan kandungan gizi makanan
                    <br />
                    © {{ new Date().getFullYear() }} — Informasi Pelayanan Gizi
                </p>
            </div>
        </footer>
    </div>
</template>
