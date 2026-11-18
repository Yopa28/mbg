
<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

const props = defineProps({
    sppg: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    name: props.sppg?.name ?? '',
    code: props.sppg?.code ?? '',
    address: props.sppg?.address ?? '',
    head_name: props.sppg?.head_name ?? '',
    phone: props.sppg?.phone ?? '',
});

const submit = () => {
    form.put('/admin/sppg');
};
</script>

<template>
    <AdminLayout
        title="Pengaturan SPPG"
        subtitle="Kelola identitas dan informasi dasar Satuan Pelayanan Pemenuhan Gizi."
    >
        <Head title="Pengaturan SPPG" />

        <div class="max-w-3xl overflow-hidden rounded-xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 p-6">
                <h3 class="font-semibold text-slate-800">
                    Informasi Dasar SPPG
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    Informasi ini digunakan pada halaman publik MBG.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-5 p-6">
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
                        Nama SPPG <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="Contoh: SPPG Kecamatan ..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                        {{ form.errors.name }}
                    </p>
                </div>

                <div>
                    <label for="code" class="mb-2 block text-sm font-medium text-slate-700">
                        Kode SPPG
                    </label>
                    <input
                        id="code"
                        v-model="form.code"
                        type="text"
                        placeholder="Masukkan kode SPPG"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />
                    <p v-if="form.errors.code" class="mt-1 text-xs text-red-600">
                        {{ form.errors.code }}
                    </p>
                </div>

                <div>
                    <label for="address" class="mb-2 block text-sm font-medium text-slate-700">
                        Alamat SPPG
                    </label>
                    <textarea
                        id="address"
                        v-model="form.address"
                        rows="3"
                        placeholder="Masukkan alamat lengkap SPPG"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    ></textarea>
                    <p v-if="form.errors.address" class="mt-1 text-xs text-red-600">
                        {{ form.errors.address }}
                    </p>
                </div>

                <div>
                    <label for="head_name" class="mb-2 block text-sm font-medium text-slate-700">
                        Nama Kepala SPPG
                    </label>
                    <input
                        id="head_name"
                        v-model="form.head_name"
                        type="text"
                        placeholder="Masukkan nama kepala SPPG"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />
                    <p v-if="form.errors.head_name" class="mt-1 text-xs text-red-600">
                        {{ form.errors.head_name }}
                    </p>
                </div>

                <div>
                    <label for="phone" class="mb-2 block text-sm font-medium text-slate-700">
                        Nomor Telepon
                    </label>
                    <input
                        id="phone"
                        v-model="form.phone"
                        type="tel"
                        placeholder="Contoh: 08xxxxxxxxxx"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />
                    <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">
                        {{ form.errors.phone }}
                    </p>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        @click="form.reset()"
                        class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50"
                    >
                        Reset
                    </button>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

