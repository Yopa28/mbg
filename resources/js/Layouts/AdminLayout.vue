
<script setup>
import { router, Link, usePage } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'Dashboard',
    },
    subtitle: {
        type: String,
        default: 'Panel Administrasi SPPG',
    },
});

const page = usePage();

const navigation = [
    {
        label: 'Dashboard',
        href: '/admin/dashboard',
        icon: '⌂',
        active: ['/admin/dashboard'],
    },
    {
        label: 'Kelola Menu',
        href: '/admin/menus',
        icon: '▤',
        active: ['/admin/menus'],
    },
    {
        label: 'Kelola Makanan',
        href: '/admin/menu-items',
        icon: '◈',
        active: ['/admin/menu-items'],
    },
    {
        label: 'Kelola Foto',
        href: null,
        icon: '▧',
        disabled: true,
    },
    {
        label: 'Edit Nutrisi',
        href: null,
        icon: '✚',
        disabled: true,
    },
    {
        label: 'Hasil Ulasan',
        href: null,
        icon: '▱',
        disabled: true,
    },
    {
        label: 'Riwayat Harian',
        href: null,
        icon: '◷',
        disabled: true,
    },
    {
        label: 'Pengaturan SPPG',
        href: '/admin/sppg',
        icon: '⚙',
        active: ['/admin/sppg'],
    },
];

const isActive = (item) => {
    return item.active?.includes(page.url.split('?')[0]) ?? false;
};

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-800">
        <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-slate-200 bg-white lg:flex">
            <div class="flex h-20 items-center gap-3 border-b border-slate-100 px-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-sm font-bold text-white">
                    MB
                </div>
                <div>
                    <h1 class="font-bold text-slate-900">MBG System</h1>
                    <p class="text-xs text-slate-500">Panel Administrasi</p>
                </div>
            </div>

            <div class="px-4 py-5">
                <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Menu Utama
                </p>

                <nav class="space-y-1">
                    <template v-for="item in navigation" :key="item.label">
                        <Link
                            v-if="item.href"
                            :href="item.href"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition"
                            :class="isActive(item)
                                ? 'bg-blue-50 text-blue-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                        >
                            <span class="w-5 text-center text-base">{{ item.icon }}</span>
                            {{ item.label }}
                        </Link>

                        <div
                            v-else
                            class="flex cursor-not-allowed items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-slate-400"
                            :title="`${item.label} belum tersedia`"
                        >
                            <span class="w-5 text-center text-base">{{ item.icon }}</span>
                            <span>{{ item.label }}</span>
                            <span class="ml-auto text-[10px]">Segera</span>
                        </div>
                    </template>
                </nav>
            </div>

            <div class="mt-auto border-t border-slate-100 p-4">
                <div class="mb-3 rounded-xl bg-blue-50 p-3">
                    <p class="text-xs font-semibold text-blue-800">Sistem MBG</p>
                    <p class="mt-1 text-xs leading-relaxed text-blue-600">
                        Pengelolaan informasi menu dan nutrisi harian.
                    </p>
                </div>

                <button
                    type="button"
                    @click="logout"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-red-50 hover:text-red-600"
                >
                    <span class="w-5 text-center">↪</span>
                    Keluar
                </button>
            </div>
        </aside>

        <div class="lg:pl-64">
            <header class="sticky top-0 z-20 flex h-20 items-center justify-between border-b border-slate-200 bg-white/95 px-5 backdrop-blur sm:px-8">
                <div>
                    <p class="text-xs text-slate-400">MBG System / Admin</p>
                    <h2 class="mt-1 text-lg font-semibold text-slate-800">
                        {{ title }}
                    </h2>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-semibold text-slate-700">Administrator</p>
                        <p class="text-xs text-slate-400">Pengelola SPPG</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 font-semibold text-blue-700">
                        A
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-7xl p-5 sm:p-8">
                <div class="mb-7">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                        {{ title }}
                    </h1>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ subtitle }}
                    </p>
                </div>

                <slot />
            </main>
        </div>
    </div>
</template>

