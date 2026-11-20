
<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

defineOptions({
    layout: {
        component: AdminLayout,
        props: {
            title: 'Dashboard',
            subtitle: 'Ringkasan pengelolaan menu dan informasi MBG.',
        },
    },
});

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
</script>

<template>
    <Head title="Dashboard Admin" />

    <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md motion-reduce:transform-none">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Menu Harian</p>
                    <p class="mt-3 text-3xl font-bold text-slate-800">{{ stats.total_menus }}</p>
                    <p class="mt-2 text-xs text-slate-400">Seluruh data menu</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-600">▤</span>
            </div>
        </div>

        <div class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md motion-reduce:transform-none">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Menu Dipublikasikan</p>
                    <p class="mt-3 text-3xl font-bold text-emerald-600">{{ stats.published_menus }}</p>
                    <p class="mt-2 text-xs text-slate-400">Menu yang dapat dilihat publik</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">✓</span>
            </div>
        </div>

        <div class="rounded-xl border border-amber-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-amber-200 hover:shadow-md motion-reduce:transform-none">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Menu Draft</p>
                    <p class="mt-3 text-3xl font-bold text-amber-600">{{ stats.draft_menus }}</p>
                    <p class="mt-2 text-xs text-slate-400">Menunggu publikasi</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-700">◷</span>
            </div>
        </div>

        <div class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md motion-reduce:transform-none">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Jenis Makanan</p>
                    <p class="mt-3 text-3xl font-bold text-blue-600">{{ stats.total_foods }}</p>
                    <p class="mt-2 text-xs text-slate-400">Data master makanan</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-700">◈</span>
            </div>
        </div>
    </section>

    <section class="mt-7 grid grid-cols-1 gap-4 md:grid-cols-3">
        <a href="/admin/menus" class="group flex items-center justify-between rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:bg-blue-50/40">
            <div>
                <p class="font-semibold text-slate-800">Kelola Menu</p>
                <p class="mt-1 text-xs text-slate-500">Buat dan publikasikan menu harian</p>
            </div>
            <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600 motion-reduce:transform-none">→</span>
        </a>
        <a href="/admin/menu-items" class="group flex items-center justify-between rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:bg-blue-50/40">
            <div>
                <p class="font-semibold text-slate-800">Kelola Nutrisi</p>
                <p class="mt-1 text-xs text-slate-500">Perbarui data bahan makanan</p>
            </div>
            <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600 motion-reduce:transform-none">→</span>
        </a>
        <a href="/admin/kitchen-photos" class="group flex items-center justify-between rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:bg-blue-50/40">
            <div>
                <p class="font-semibold text-slate-800">Dokumentasi Dapur</p>
                <p class="mt-1 text-xs text-slate-500">Unggah dan kelola kegiatan dapur</p>
            </div>
            <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600 motion-reduce:transform-none">→</span>
        </a>
    </section>

    <section class="mt-7 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white xl:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 p-5">
                <div>
                    <h3 class="font-semibold text-slate-800">Menu Terbaru</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Lima data menu harian terakhir.
                    </p>
                </div>

                <a
                    href="/admin/menus"
                    class="text-sm font-medium text-blue-600 hover:text-blue-700"
                >
                    Lihat semua
                </a>
            </div>

            <div v-if="recentMenus.length" class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Tanggal</th>
                            <th class="px-5 py-3 font-medium">Nama Menu</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="menu in recentMenus" :key="menu.id">
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                {{ formatDate(menu.menu_date) }}
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-800">
                                {{ menu.title }}
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-medium"
                                    :class="menu.status === 'published'
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'bg-amber-50 text-amber-700'"
                                >
                                    {{ menu.status === 'published' ? 'Terbit' : 'Draft' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="p-10 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-400">
                    ▤
                </div>
                <p class="mt-3 text-sm font-medium text-slate-700">
                    Belum ada menu harian
                </p>
                <p class="mt-1 text-sm text-slate-400">
                    Menu yang dibuat akan muncul di sini.
                </p>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-slate-800">Informasi SPPG</h3>
                <a
                    href="/admin/sppg"
                    class="text-xs font-medium text-blue-600 hover:text-blue-700"
                >
                    Pengaturan
                </a>
            </div>

            <div v-if="sppg" class="mt-5 space-y-4">
                <div>
                    <p class="text-xs text-slate-400">Nama SPPG</p>
                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ sppg.name }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-400">Kode SPPG</p>
                    <p class="mt-1 text-sm text-slate-700">
                        {{ sppg.code || '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-400">Alamat</p>
                    <p class="mt-1 text-sm text-slate-700">
                        {{ sppg.address || 'Belum diisi' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-400">Kepala SPPG</p>
                    <p class="mt-1 text-sm text-slate-700">
                        {{ sppg.head_name || '-' }}
                    </p>
                </div>
            </div>

            <div v-else class="mt-5 rounded-lg bg-amber-50 p-4">
                <p class="text-sm font-medium text-amber-800">
                    Informasi SPPG belum diatur.
                </p>
                <p class="mt-1 text-xs text-amber-700">
                    Lengkapi identitas SPPG agar informasi dapat ditampilkan.
                </p>
                <a
                    href="/admin/sppg"
                    class="mt-3 inline-block text-sm font-semibold text-amber-800 underline"
                >
                    Lengkapi sekarang
                </a>
            </div>
        </div>
    </section>
</template>

