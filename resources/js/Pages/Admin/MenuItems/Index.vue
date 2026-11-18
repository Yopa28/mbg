
<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout,
});

const props = defineProps({
    menuItems: {
        type: Object,
        required: true,
    },
});

const showForm = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    category: '',
    description: '',
});

const openCreateForm = () => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showForm.value = true;
};

const openEditForm = (item) => {
    editingId.value = item.id;
    form.name = item.name;
    form.category = item.category ?? '';
    form.description = item.description ?? '';
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
    if (editingId.value) {
        form.put(`/admin/menu-items/${editingId.value}`, {
            onSuccess: () => closeForm(),
        });
    } else {
        form.post('/admin/menu-items', {
            onSuccess: () => closeForm(),
        });
    }
};

const deleteItem = (item) => {
    if (!confirm(`Hapus makanan "${item.name}"?`)) {
        return;
    }

    router.delete(`/admin/menu-items/${item.id}`);
};
</script>

<template>
    <Head title="Kelola Makanan" />

    <div class="space-y-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">
                    Master Data Makanan
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Kelola daftar makanan yang dapat digunakan dalam menu harian.
                </p>
            </div>

            <button
                type="button"
                @click="openCreateForm"
                class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
            >
                + Tambah Makanan
            </button>
        </div>

        <section
            v-if="showForm"
            class="max-w-2xl overflow-hidden rounded-xl border border-blue-200 bg-white"
        >
            <div class="border-b border-slate-100 p-5">
                <h3 class="font-semibold text-slate-800">
                    {{ editingId ? 'Edit Data Makanan' : 'Tambah Makanan Baru' }}
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    Masukkan informasi dasar makanan.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-5 p-5">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Nama Makanan *
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="Contoh: Nasi putih"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                        {{ form.errors.name }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Kategori
                    </label>
                    <select
                        v-model="form.category"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500"
                    >
                        <option value="">Pilih kategori</option>
                        <option value="Makanan Pokok">Makanan Pokok</option>
                        <option value="Lauk Hewani">Lauk Hewani</option>
                        <option value="Lauk Nabati">Lauk Nabati</option>
                        <option value="Sayuran">Sayuran</option>
                        <option value="Buah">Buah</option>
                        <option value="Minuman">Minuman</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <p v-if="form.errors.category" class="mt-1 text-xs text-red-600">
                        {{ form.errors.category }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Deskripsi
                    </label>
                    <textarea
                        v-model="form.description"
                        rows="3"
                        placeholder="Deskripsi singkat makanan..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
                    ></textarea>
                    <p v-if="form.errors.description" class="mt-1 text-xs text-red-600">
                        {{ form.errors.description }}
                    </p>
                </div>

                <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-100 pt-5 sm:flex-row">
                    <button
                        type="button"
                        @click="closeForm"
                        class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Makanan' }}
                    </button>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 p-5">
                <h3 class="font-semibold text-slate-800">Daftar Makanan</h3>
                <p class="mt-1 text-sm text-slate-500">
                    {{ menuItems.total }} jenis makanan terdaftar.
                </p>
            </div>

            <div v-if="menuItems.data.length" class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Nama Makanan</th>
                            <th class="px-5 py-3 font-medium">Kategori</th>
                            <th class="px-5 py-3 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="item in menuItems.data"
                            :key="item.id"
                            class="hover:bg-slate-50/70"
                        >
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-800">
                                    {{ item.name }}
                                </p>
                                <p v-if="item.description" class="mt-1 text-xs text-slate-400">
                                    {{ item.description }}
                                </p>
                            </td>

                            <td class="px-5 py-4 text-slate-600">
                                {{ item.category || '-' }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        @click="openEditForm(item)"
                                        class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-white"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        @click="deleteItem(item)"
                                        class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="p-12 text-center">
                <p class="font-medium text-slate-700">
                    Belum ada data makanan.
                </p>
                <p class="mt-1 text-sm text-slate-400">
                    Tambahkan makanan pertama untuk mulai menyusun menu harian.
                </p>
            </div>

            <div
                v-if="menuItems.links && menuItems.links.length > 3"
                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 p-4"
            >
                <p class="text-xs text-slate-500">
                    Menampilkan {{ menuItems.from ?? 0 }}–{{ menuItems.to ?? 0 }}
                    dari {{ menuItems.total }} makanan
                </p>

                <div class="flex flex-wrap gap-1">
                    <template v-for="(link, index) in menuItems.links" :key="index">
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

