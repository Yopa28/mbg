<script setup>
import { useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    dailyMenuId: {
        type: [Number, String],
        default: null,
    },
});

const page = usePage();

const categories = [
    { value: 'taste', label: 'Rasa' },
    { value: 'portion', label: 'Porsi' },
    { value: 'cleanliness', label: 'Kebersihan' },
    { value: 'other', label: 'Catatan Khusus' },
];

const form = useForm({
    daily_menu_id: props.dailyMenuId,
    category: 'taste',
    name: '',
    contact: '',
    message: '',
});

const submit = () => {
    form.post('/feedback', {
        preserveScroll: true,
        onSuccess: () => form.reset('name', 'contact', 'message'),
    });
};
</script>

<template>
    <section class="mx-auto max-w-[720px] px-4 pb-12 sm:px-6">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <!-- Header -->
            <div class="border-b border-slate-100 px-5 py-5 sm:px-6">
                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#c89446]">
                    Suara Penerima Manfaat
                </p>

                <h2 class="mt-2 text-xl font-bold text-[#173060]">
                    Bagaimana pengalaman makan hari ini?
                </h2>

                <p class="mt-1.5 text-sm leading-5 text-slate-500">
                    Masukan Anda membantu meningkatkan kualitas pelayanan dan makanan MBG.
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-4 p-5 sm:p-6">
                <div
                    v-if="page.props.flash?.success"
                    class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700"
                >
                    {{ page.props.flash.success }}
                </div>

                <!-- Category -->
                <div>
                    <label class="mb-2 block text-[13px] font-semibold text-slate-700">
                        Kategori masukan
                    </label>

                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="category in categories"
                            :key="category.value"
                            type="button"
                            @click="form.category = category.value"
                            :class="form.category === category.value
                                ? 'border-[#173060] bg-[#173060] text-white'
                                : 'border-slate-200 bg-white text-slate-600 hover:border-[#173060]'"
                            class="rounded-full border px-4 py-2 text-xs font-medium transition"
                        >
                            {{ category.label }}
                        </button>
                    </div>

                    <p v-if="form.errors.category" class="mt-2 text-xs text-red-600">
                        {{ form.errors.category }}
                    </p>
                </div>

                <!-- Message -->
                <div>
                    <label class="mb-2 block text-[13px] font-semibold text-slate-700">
                        Pesan atau masukan *
                    </label>

                    <textarea
                        v-model="form.message"
                        rows="3"
                        maxlength="2000"
                        required
                        placeholder="Tuliskan masukan Anda mengenai makanan atau pelayanan..."
                        class="min-h-[100px] w-full resize-y rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[#173060] focus:ring-2 focus:ring-[#173060]/10"
                    ></textarea>

                    <div class="mt-1 flex justify-between gap-3">
                        <p v-if="form.errors.message" class="text-xs text-red-600">
                            {{ form.errors.message }}
                        </p>

                        <span class="ml-auto text-xs text-slate-400">
                            {{ form.message.length }}/2000
                        </span>
                    </div>
                </div>

                <!-- Optional information -->
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[13px] font-semibold text-slate-700">
                            Nama <span class="font-normal text-slate-400">(opsional)</span>
                        </label>

                        <input
                            v-model="form.name"
                            type="text"
                            maxlength="100"
                            placeholder="Nama Anda"
                            class="h-[42px] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#173060] focus:ring-2 focus:ring-[#173060]/10"
                        />

                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[13px] font-semibold text-slate-700">
                            Kontak <span class="font-normal text-slate-400">(opsional)</span>
                        </label>

                        <input
                            v-model="form.contact"
                            type="text"
                            maxlength="100"
                            placeholder="Email atau nomor telepon"
                            class="h-[42px] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#173060] focus:ring-2 focus:ring-[#173060]/10"
                        />

                        <p v-if="form.errors.contact" class="mt-1 text-xs text-red-600">
                            {{ form.errors.contact }}
                        </p>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs leading-5 text-slate-400">
                        Nama dan kontak tidak wajib diisi.
                    </p>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="h-10 rounded-xl bg-[#173060] px-5 text-sm font-semibold text-white transition hover:bg-[#24447f] disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {{ form.processing ? 'Mengirim...' : 'Kirim Masukan' }}
                    </button>
                </div>
            </form>
        </div>
    </section>
</template>
