<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout,
});

const props = defineProps({
    documentations: {
        type: Object,
        required: true,
    },
    dailyMenus: {
        type: Array,
        default: () => [],
    },
});

const categories = [
    { value: 'preparation', label: 'Persiapan Bahan' },
    { value: 'cooking', label: 'Proses Memasak' },
    { value: 'temperature_check', label: 'Pemeriksaan Suhu' },
    { value: 'serving', label: 'Penyajian' },
];

const showForm = ref(false);
const editingId = ref(null);

const form = useForm({
    daily_menu_id: '',
    category: 'preparation',
    title: '',
    description: '',
    photo: null,
    temperature: '',
    captured_at: '',
    is_published: false,
});

const categoryLabel = (value) => {
    return categories.find((item) => item.value === value)?.label ?? value;
};

const getPhotoUrl = (path) => {
    if (!path) return null;

    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) {
        return path;
    }

    return `/storage/${path}`;
};

const formatDate = (date) => {
    if (!date) return '-';

    return new Date(`${date.substring(0, 10)}T00:00:00`).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const openCreateForm = () => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    form.daily_menu_id = props.dailyMenus[0]?.id ?? '';
    form.category = 'preparation';
    form.is_published = false;
    showForm.value = true;
};

const openEditForm = (item) => {
    editingId.value = item.id;
    form.daily_menu_id = item.daily_menu_id;
    form.category = item.category;
    form.title = item.title ?? '';
    form.description = item.description ?? '';
    form.photo = null;
    form.temperature = item.temperature ?? '';
    form.captured_at = item.captured_at
        ? item.captured_at.substring(0, 16)
        : '';
    form.is_published = item.is_published;
    form.clearErrors();
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    const url = editingId.value
        ? `/admin/kitchen-photos/${editingId.value}`
        : '/admin/kitchen-photos';

    form.transform((data) => ({
        ...data,
        ...(editingId.value ? { _method: 'put' } : {}),
        temperature: data.category === 'temperature_check' ? data.temperature : null,
    })).post(url, {
        forceFormData: true,
        onSuccess: () => closeForm(),
    });
};

const togglePublication = (item) => {
    const action = item.is_published ? 'kembali menjadi draft' : 'dipublikasikan';

    if (!confirm(`Dokumentasi ini akan ${action}. Lanjutkan?`)) return;

    router.patch(`/admin/kitchen-photos/${item.id}/publication`);
};

const deleteDocumentation = (item) => {
    if (!confirm('Hapus dokumentasi ini beserta file fotonya?')) return;

    router.delete(`/admin/kitchen-photos/${item.id}`);
};
</script>

<template>
    <Head title="Kelola Foto Kegiatan Dapur" />

    <div class="space-y-6">
        <div
            v-if="$page.props.flash?.success"
            class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700"
        >
            {{ $page.props.flash.success }}
        </div>

        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">
                    Dokumentasi Kegiatan Dapur
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Kelola foto persiapan bahan, proses memasak, pemeriksaan suhu, dan penyajian.
                </p>
            </div>

            <button
                type="button"
                @click="openCreateForm"
                :disabled="dailyMenus.length === 0"
                class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
            >
                + Tambah Dokumentasi
            </button>
        </div>

        <div
            v-if="dailyMenus.length === 0"
            class="rounded-xl border border-amber-200 bg-amber-50 p-5"
        >
            <p class="font-semibold text-amber-800">Belum ada menu harian.</p>
            <p class="mt-1 text-sm text-amber-700">
                Buat menu harian terlebih dahulu sebelum menambahkan dokumentasi.
            </p>
            <a href="/admin/menus" class="mt-2 inline-block text-sm font-semibold underline">
                Buka Kelola Menu
            </a>
        </div>

        <section
            v-if="showForm"
            class="overflow-hidden rounded-xl border border-blue-200 bg-white shadow-sm"
        >
            <div class="flex items-center justify-between border-b border-slate-100 p-5">
                <div>
                    <h3 class="font-semibold text-slate-800">
                        {{ editingId ? 'Edit Dokumentasi' : 'Tambah Dokumentasi' }}
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Lengkapi informasi dan unggah foto kegiatan dapur.
                    </p>
                </div>

                <button
                    type="button"
                    @click="closeForm"
                    class="rounded-lg px-3 py-2 text-sm text-slate-500 hover:bg-slate-100"
                >
                    Tutup
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-5 p-5">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Menu dan Tanggal *
                    </label>
                    <select
                        v-model="form.daily_menu_id"
                        required
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm"
                    >
                        <option value="">Pilih menu harian</option>
                        <option v-for="menu in dailyMenus" :key="menu.id" :value="menu.id">
                            {{ formatDate(menu.menu_date) }} — {{ menu.title }}
                            ({{ menu.status === 'published' ? 'Dipublikasikan' : 'Draft' }})
                        </option>
                    </select>
                    <p v-if="form.errors.daily_menu_id" class="mt-1 text-xs text-red-600">
                        {{ form.errors.daily_menu_id }}
                    </p>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Kategori Kegiatan *
                        </label>
                        <select
                            v-model="form.category"
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm"
                        >
                            <option v-for="category in categories" :key="category.value" :value="category.value">
                                {{ category.label }}
                            </option>
                        </select>
                        <p v-if="form.errors.category" class="mt-1 text-xs text-red-600">
                            {{ form.errors.category }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Judul Dokumentasi
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="Contoh: Pemeriksaan suhu ayam"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"
                        />
                        <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">
                            {{ form.errors.title }}
                        </p>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Keterangan
                    </label>
                    <textarea
                        v-model="form.description"
                        rows="3"
                        placeholder="Jelaskan kegiatan yang terlihat pada foto..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"
                    ></textarea>
                    <p v-if="form.errors.description" class="mt-1 text-xs text-red-600">
                        {{ form.errors.description }}
                    </p>
                </div>

                <div v-if="form.category === 'temperature_check'" class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Suhu (°C) *
                        </label>
                        <input
                            v-model="form.temperature"
                            type="number"
                            step="0.01"
                            min="-50"
                            max="300"
                            required
                            placeholder="Contoh: 75"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"
                        />
                        <p v-if="form.errors.temperature" class="mt-1 text-xs text-red-600">
                            {{ form.errors.temperature }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Waktu Pemeriksaan
                        </label>
                        <input
                            v-model="form.captured_at"
                            type="datetime-local"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"
                        />
                    </div>
                </div>

                <div v-else>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Waktu Dokumentasi
                    </label>
                    <input
                        v-model="form.captured_at"
                        type="datetime-local"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Foto Kegiatan {{ editingId ? '(opsional jika tidak diganti)' : '*' }}
                    </label>
                    <input
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        :required="!editingId"
                        @input="form.photo = $event.target.files[0] || null"
                        class="block w-full rounded-lg border border-slate-300 bg-white text-sm text-slate-600 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-sm file:font-semibold"
                    />
                    <p class="mt-2 text-xs text-slate-400">
                        JPG, JPEG, PNG, atau WEBP. Maksimal 5 MB.
                    </p>
                    <p v-if="form.errors.photo" class="mt-1 text-xs text-red-600">
                        {{ form.errors.photo }}
                    </p>
                </div>

                <label class="flex items-start gap-3 rounded-lg border border-slate-200 p-4">
                    <input
                        v-model="form.is_published"
                        type="checkbox"
                        class="mt-1 rounded border-slate-300 text-blue-600"
                    />
                    <span>
                        <span class="block text-sm font-semibold text-slate-700">
                            Publikasikan dokumentasi
                        </span>
                        <span class="mt-1 block text-xs text-slate-500">
                            Dokumentasi hanya terlihat publik jika menu hariannya juga sudah dipublikasikan.
                        </span>
                    </span>
                </label>

                <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-100 pt-5 sm:flex-row">
                    <button
                        type="button"
                        @click="closeForm"
                        class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Dokumentasi' }}
                    </button>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 p-5">
                <h3 class="font-semibold text-slate-800">Daftar Dokumentasi</h3>
                <p class="mt-1 text-sm text-slate-500">
                    {{ documentations.total }} dokumentasi terdaftar.
                </p>
            </div>

            <div v-if="documentations.data.length" class="divide-y divide-slate-100">
                <article
                    v-for="item in documentations.data"
                    :key="item.id"
                    class="flex flex-col gap-4 p-5 sm:flex-row"
                >
                    <img
                        :src="getPhotoUrl(item.photo_path)"
                        :alt="item.title || categoryLabel(item.category)"
                        class="h-40 w-full rounded-lg border border-slate-200 object-cover sm:w-52"
                    />

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="font-semibold text-slate-800">
                                {{ item.title || categoryLabel(item.category) }}
                            </h4>
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
                                {{ categoryLabel(item.category) }}
                            </span>
                            <span
                                class="rounded-full px-3 py-1 text-xs font-semibold"
                                :class="item.is_published
                                    ? 'bg-emerald-50 text-emerald-700'
                                    : 'bg-amber-50 text-amber-700'"
                            >
                                {{ item.is_published ? 'Dipublikasikan' : 'Draft' }}
                            </span>
                        </div>

                        <p class="mt-2 text-sm text-slate-500">
                            {{ item.daily_menu?.title }} · {{ formatDate(item.daily_menu?.menu_date) }}
                        </p>

                        <p v-if="item.description" class="mt-2 text-sm text-slate-600">
                            {{ item.description }}
                        </p>

                        <p v-if="item.category === 'temperature_check' && item.temperature !== null" class="mt-2 text-sm font-semibold text-slate-700">
                            Suhu: {{ item.temperature }} °C
                        </p>

                        <p v-if="item.captured_at" class="mt-1 text-xs text-slate-400">
                            Waktu: {{ new Date(item.captured_at).toLocaleString('id-ID') }}
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <button
                                type="button"
                                @click="openEditForm(item)"
                                class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50"
                            >
                                Edit
                            </button>
                            <button
                                type="button"
                                @click="togglePublication(item)"
                                class="rounded-lg border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50"
                            >
                                {{ item.is_published ? 'Jadikan Draft' : 'Publikasikan' }}
                            </button>
                            <button
                                type="button"
                                @click="deleteDocumentation(item)"
                                class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </article>
            </div>

            <div v-else class="p-12 text-center">
                <p class="font-semibold text-slate-700">Belum ada dokumentasi foto.</p>
                <p class="mt-1 text-sm text-slate-400">
                    Tambahkan dokumentasi kegiatan dapur pertama.
                </p>
            </div>

            <div
                v-if="documentations.links && documentations.links.length > 3"
                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 p-4"
            >
                <p class="text-xs text-slate-500">
                    Menampilkan {{ documentations.from ?? 0 }}–{{ documentations.to ?? 0 }}
                    dari {{ documentations.total }} dokumentasi
                </p>

                <div class="flex flex-wrap gap-1">
                    <template v-for="(link, index) in documentations.links" :key="index">
                        <a
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            class="rounded-lg border px-3 py-2 text-xs"
                            :class="link.active
                                ? 'border-blue-600 bg-blue-600 text-white'
                                : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                        ></a>
                        <span
                            v-else
                            v-html="link.label"
                            class="rounded-lg border border-slate-100 px-3 py-2 text-xs text-slate-300"
                        ></span>
                    </template>
                </div>
            </div>
        </section>
    </div>
</template>
