
<script setup>
defineProps({
    sppg: {
        type: Object,
        default: null,
    },
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-800">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <a href="/" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-700 text-lg font-bold text-white">
                        MBG
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">
                            {{ sppg?.name || 'Informasi Menu MBG' }}
                        </p>
                        <p class="text-xs text-slate-500">Informasi Gizi Makan Bergizi Gratis</p>
                    </div>
                </a>

                <a href="/" class="text-sm font-semibold text-blue-700 hover:text-blue-900">
                    Menu Harian
                </a>
            </div>
        </header>

        <main>
            <slot />
        </main>

        <footer class="mt-16 border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-6 py-8 text-center">
                <p class="text-sm font-semibold text-slate-700">
                    {{ sppg?.name || 'Informasi Menu MBG' }}
                </p>
                <p class="mt-1 text-xs text-slate-500">
                    Informasi menu dan kandungan gizi makanan
                </p>
            </div>
        </footer>
    </div>
</template>
