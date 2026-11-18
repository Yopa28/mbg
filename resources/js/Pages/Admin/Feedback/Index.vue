<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout,
});

defineProps({
    feedbacks: {
        type: Object,
        required: true,
    },
});

const categories = {
    taste: 'Rasa',
    portion: 'Porsi',
    cleanliness: 'Kebersihan',
    other: 'Catatan Khusus',
};

const statuses = {
    new: 'Baru',
    reviewed: 'Ditinjau',
    resolved: 'Selesai',
};

const formatDate = (value) => {
    if (!value) return '-';

    return new Date(value).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const updateStatus = (item, status) => {
    router.patch(`/admin/feedback/${item.id}`, { status }, {
        preserveScroll: true,
    });
};

const deleteFeedback = (item) => {
    if (!confirm('Hapus ulasan ini secara permanen?')) return;

    router.delete(`/admin/feedback/${item.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Hasil Ulasan" />

    <div class="space-y-6">
        <div
            v-if="$page.props.flash?.success"
            class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700"
        >
            {{ $page.props.flash.success }}
        </div>

        <div>
            <h2 class="text-lg font-semibold text-slate-800">
                Hasil Ulasan Penerima MBG
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Baca dan tindak lanjuti masukan dari penerima manfaat.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">Total ulasan</p>
                <p class="mt-2 text-2xl font-bold text-slate-800">
                    {{ feedbacks.total }}
                </p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">Halaman ini</p>
                <p class="mt-2 text-2xl font-bold text-slate-800">
                    {{ feedbacks.data.length }}
                </p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">Status baru di halaman ini</p>
                <p class="mt-2 text-2xl font-bold text-blue-700">
                    {{ feedbacks.data.filter(item => item.status === 'new').length }}
                </p>
            </div>
        </div>

        <div
            v-if="feedbacks.data.length === 0"
            class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center"
        >
            <div class="text-3xl text-slate-300">▱</div>
            <h3 class="mt-3 font-semibold text-slate-700">Belum ada ulasan</h3>
            <p class="mt-1 text-sm text-slate-500">
                Masukan dari penerima MBG akan muncul di halaman ini.
            </p>
        </div>

        <div v-else class="space-y-4">
            <article
                v-for="item in feedbacks.data"
                :key="item.id"
                class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                {{ categories[item.category] || item.category }}
                            </span>
                            <span
                                :class="{
                                    'bg-blue-50 text-blue-700': item.status === 'new',
                                    'bg-amber-50 text-amber-700': item.status === 'reviewed',
                                    'bg-emerald-50 text-emerald-700': item.status === 'resolved',
                                }"
                                class="rounded-full px-3 py-1 text-xs font-semibold"
                            >
                                {{ statuses[item.status] || item.status }}
                            </span>
                        </div>

                        <p class="mt-3 text-sm leading-6 text-slate-700">
                            {{ item.message }}
                        </p>
                    </div>

                    <p class="shrink-0 text-xs text-slate-400">
                        {{ formatDate(item.created_at) }}
                    </p>
                </div>

                <div class="mt-4 grid gap-2 border-t border-slate-100 pt-4 text-xs text-slate-500 sm:grid-cols-2">
                    <p>
                        Pengirim:
                        <span class="font-medium text-slate-700">
                            {{ item.name || 'Anonim' }}
                        </span>
                    </p>
                    <p>
                        Kontak:
                        <span class="font-medium text-slate-700">
                            {{ item.contact || 'Tidak dicantumkan' }}
                        </span>
                    </p>
                    <p class="sm:col-span-2">
                        Menu:
                        <span class="font-medium text-slate-700">
                            {{ item.daily_menu?.title || 'Menu tidak tersedia' }}
                        </span>
                    </p>
                </div>

                <div class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <label class="flex items-center gap-2 text-xs font-medium text-slate-600">
                        Status tindak lanjut
                        <select
                            :value="item.status"
                            @change="updateStatus(item, $event.target.value)"
                            class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs"
                        >
                            <option value="new">Baru</option>
                            <option value="reviewed">Ditinjau</option>
                            <option value="resolved">Selesai</option>
                        </select>
                    </label>

                    <button
                        type="button"
                        @click="deleteFeedback(item)"
                        class="self-start rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 sm:self-auto"
                    >
                        Hapus ulasan
                    </button>
                </div>
            </article>
        </div>

        <div
            v-if="feedbacks.links?.length > 3"
            class="flex flex-wrap gap-2"
        >
            <template v-for="link in feedbacks.links" :key="link.label">
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
