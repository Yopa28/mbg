<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout,
});

const props = defineProps({
    menus: { type: Object, required: true },
    filters: { type: Object, required: true },
    summary: { type: Object, required: true },
});

const form = reactive({
    date_from: props.filters.date_from ?? '',
    date_to: props.filters.date_to ?? '',
    status: props.filters.status ?? '',
});

const formatDate = (value) => {
    if (!value) return '-';

    return new Date(`${value.substring(0, 10)}T00:00:00`).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const applyFilter = () => {
    router.get('/admin/history', form, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const resetFilter = () => {
    form.date_from = '';
    form.date_to = '';
    form.status = '';
    applyFilter();
};
</script>

<template>
    <Head title="Riwayat Harian" />

    <div class="space-y-6">
        <div>
            <h2 class="text-lg font-semibold text-slate-800">Arsip Menu Harian</h2>
            <p class="mt-1 text-sm text-slate-500">
                Lihat kembali menu dan dokumentasi kegiatan MBG dari tanggal sebelumnya.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">Total arsip</p>
                <p class="mt-2 text-2xl font-bold text-slate-800">{{ summary.total }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">Dipublikasikan</p>
                <p class="mt-2 text-2xl font-bold text-emerald-700">{{ summary.published }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">Draft</p>
                <p class="mt-2 text-2xl font-bold text-amber-700">{{ summary.draft }}</p>
            </div>
        </div>

        <section class="rounded-xl border border-slate-200 bg-white p-5">
            <div class="mb-4">
                <h3 class="font-semibold text-slate-800">Filter Riwayat</h3>
                <p class="mt-1 text-xs text-slate-500">Pilih rentang tanggal atau status menu.</p>
            </div>

            <form @submit.prevent="applyFilter" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Dari tanggal</label>
                    <input
                        v-model="form.date_from"
                        type="date"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Sampai tanggal</label>
                    <input
                        v-model="form.date_to"
                        type="date"
                        :min="form.date_from || undefined"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                    <select
                        v-model="form.status"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm"
                    >
                        <option value="">Semua status</option>
                        <option value="published">Dipublikasikan</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Terapkan
                    </button>
                    <button
                        type="button"
                        @click="resetFilter"
                        class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50"
                    >
                        Reset
                    </button>
                </div>
            </form>
        </section>

        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-slate-800">Daftar Arsip</h3>
                <span class="text-xs text-slate-500">{{ menus.total }} menu ditemukan</span>
            </div>

            <div
                v-if="menus.data.length === 0"
                class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center"
            >
                <div class="text-3xl text-slate-300">◷</div>
                <h3 class="mt-3 font-semibold text-slate-700">Belum ada riwayat menu</h3>
                <p class="mt-1 text-sm text-slate-500">
                    Menu dari tanggal sebelumnya akan muncul di halaman ini.
                </p>
            </div>

            <div v-else class="space-y-3">
                <article
                    v-for="menu in menus.data"
                    :key="menu.id"
                    class="flex flex-col justify-between gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center"
                >
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="font-semibold text-slate-800">{{ menu.title }}</h4>
                            <span
                                class="rounded-full px-3 py-1 text-xs font-semibold"
                                :class="menu.status === 'published'
                                    ? 'bg-emerald-50 text-emerald-700'
                                    : 'bg-amber-50 text-amber-700'"
                            >
                                {{ menu.status === 'published' ? 'Dipublikasikan' : 'Draft' }}
                            </span>
                        </div>

                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                            <span>{{ formatDate(menu.menu_date) }}</span>
                            <span>{{ menu.items_count }} makanan</span>
                        </div>

                        <p v-if="menu.description" class="mt-2 text-sm text-slate-500">
                            {{ menu.description }}
                        </p>
                    </div>

                    <Link
                        :href="`/admin/history/${menu.id}`"
                        class="shrink-0 rounded-lg border border-blue-200 px-4 py-2.5 text-center text-sm font-semibold text-blue-700 hover:bg-blue-50"
                    >
                        Lihat Detail
                    </Link>
                </article>
            </div>
        </section>

        <div v-if="menus.links?.length > 3" class="flex flex-wrap gap-2">
            <template v-for="(link, index) in menus.links" :key="index">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    preserve-scroll
                    class="rounded-lg border px-3 py-2 text-sm"
                    :class="link.active
                        ? 'border-blue-600 bg-blue-600 text-white'
                        : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'"
                    v-html="link.label"
                />
                <span
                    v-else
                    class="rounded-lg border border-slate-100 px-3 py-2 text-sm text-slate-300"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>
