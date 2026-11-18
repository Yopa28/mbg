<script setup>
import { Head, router } from '@inertiajs/vue3';

defineProps({
    sppg: {
        type: Object,
        default: null,
    },
    stats: {
        type: Object,
        required: true,
    },
    recentMenus: {
        type: Array,
        default: () => [],
    },
});

const formatDate = (date) => {
    if (!date) return '-';

    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <Head title="Dashboard Admin" />

    <div class="min-h-screen bg-slate-50">
        <header class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-slate-800">
                        MBG System
                    </h1>
                    <p class="text-sm text-slate-500">
                        Panel Administrasi SPPG
                    </p>
                </div>

                <button
                    @click="logout"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50"
                >
                    Keluar
                </button>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 py-8">
            <div class="mb-8">
                <p class="text-sm text-slate-500">
                    Dashboard
                </p>

                <h2 class="text-2xl font-bold text-slate-800 mt-1">
                    Selamat datang, Admin
                </h2>

                <p class="text-slate-500 mt-2">
                    Berikut ringkasan pengelolaan menu MBG.
                </p>
            </div>

            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <p class="text-sm text-slate-500">Total Menu Harian</p>
                    <p class="text-3xl font-bold text-slate-800 mt-3">
                        {{ stats.total_menus }}
                    </p>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <p class="text-sm text-slate-500">Menu Dipublikasikan</p>
                    <p class="text-3xl font-bold text-green-600 mt-3">
                        {{ stats.published_menus }}
                    </p>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <p class="text-sm text-slate-500">Menu Draft</p>
                    <p class="text-3xl font-bold text-amber-600 mt-3">
                        {{ stats.draft_menus }}
                    </p>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <p class="text-sm text-slate-500">Total Jenis Makanan</p>
                    <p class="text-3xl font-bold text-blue-600 mt-3">
                        {{ stats.total_foods }}
                    </p>
                </div>
            </section>

            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
                <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl">
                    <div class="p-5 border-b border-slate-200">
                        <h3 class="font-semibold text-slate-800">
                            Menu Terbaru
                        </h3>
                        <p class="text-sm text-slate-500 mt-1">
                            Lima data menu harian terakhir.
                        </p>
                    </div>

                    <div v-if="recentMenus.length" class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 text-slate-500">
                                <tr>
                                    <th class="px-5 py-3 font-medium">Tanggal</th>
                                    <th class="px-5 py-3 font-medium">Nama Menu</th>
                                    <th class="px-5 py-3 font-medium">Status</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="menu in recentMenus" :key="menu.id">
                                    <td class="px-5 py-4 text-slate-600">
                                        {{ formatDate(menu.menu_date) }}
                                    </td>

                                    <td class="px-5 py-4 font-medium text-slate-800">
                                        {{ menu.title }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <span
                                            :class="menu.status === 'published'
                                                ? 'bg-green-50 text-green-700'
                                                : 'bg-amber-50 text-amber-700'"
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-medium"
                                        >
                                            {{ menu.status === 'published' ? 'Terbit' : 'Draft' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="p-8 text-center">
                        <p class="text-sm font-medium text-slate-600">
                            Belum ada menu harian.
                        </p>
                        <p class="text-sm text-slate-400 mt-1">
                            Menu yang dibuat akan muncul di bagian ini.
                        </p>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <h3 class="font-semibold text-slate-800">
                        Informasi SPPG
                    </h3>

                    <div v-if="sppg" class="mt-5 space-y-4">
                        <div>
                            <p class="text-xs text-slate-400">Nama SPPG</p>
                            <p class="text-sm font-medium text-slate-800 mt-1">
                                {{ sppg.name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">Kode SPPG</p>
                            <p class="text-sm text-slate-700 mt-1">
                                {{ sppg.code || '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">Alamat</p>
                            <p class="text-sm text-slate-700 mt-1">
                                {{ sppg.address || 'Belum diisi' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">Kepala SPPG</p>
                            <p class="text-sm text-slate-700 mt-1">
                                {{ sppg.head_name || '-' }}
                            </p>
                        </div>
                    </div>

                    <p v-else class="text-sm text-slate-500 mt-5">
                        Informasi SPPG belum diatur.
                    </p>
                </div>
            </section>
        </main>
    </div>
</template>
