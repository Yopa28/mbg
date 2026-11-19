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
    <section class="mx-auto max-w-[1280px] px-5 pb-16 pt-10 sm:px-8 lg:pb-20">
        <div class="mx-auto max-w-[960px] overflow-hidden rounded-[24px] border border-slate-200/80 bg-white shadow-[0_12px_40px_rgba(20,43,82,0.07)]">
            <div class="relative overflow-hidden bg-[#142b52] px-6 py-8 text-white sm:px-9 sm:py-10">
                <div class="pointer-events-none absolute -right-10 -top-24 h-64 w-64 rounded-full border border-white/[0.08]"></div>
                <div class="pointer-events-none absolute -right-2 -top-16 h-48 w-48 rounded-full border border-white/[0.08]"></div>

                <div class="relative">
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-[#e4c58f]">
                        Suara Penerima Manfaat
                    </p>
                    <h2 class="mt-3 text-2xl font-black leading-tight tracking-tight sm:text-3xl">
                        Bagaimana pengalaman makan hari ini?
                    </h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-blue-100/75">
                        Masukan Anda membantu meningkatkan kualitas makanan dan pelayanan Makan Bergizi Gratis.
                    </p>
                </div>

                <div class="relative mt-6 h-px w-20 bg-[#d5ad69]"></div>
            </div>

            <form @submit.prevent="submit" class="space-y-7 p-6 sm:p-9">
                <div
                    v-if="page.props.flash?.success"
                    class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
                >
                    <svg viewBox="0 0 24 24" fill="none" class="mt-0.5 h-5 w-5 shrink-0" stroke="currentColor" stroke-width="2">
                        <path d="m5 12 4 4L19 6"></path>
                    </svg>
                    <span>{{ page.props.flash.success }}</span>
                </div>

                <div>
                    <label class="mb-3 block text-sm font-bold text-[#142b52]">
                        Kategori masukan
                    </label>

                    <div class="flex flex-wrap gap-2.5">
                        <button
                            v-for="category in categories"
                            :key="category.value"
                            type="button"
                            @click="form.category = category.value"
                            :class="form.category === category.value
                                ? 'border-[#142b52] bg-[#142b52] text-white shadow-sm'
                                : 'border-slate-200 bg-white text-slate-600 hover:border-[#a87532] hover:text-[#142b52]'"
                            class="rounded-full border px-5 py-2.5 text-xs font-semibold transition duration-200"
                        >
                            {{ category.label }}
                        </button>
                    </div>

                    <p v-if="form.errors.category" class="mt-2 text-xs text-red-600">
                        {{ form.errors.category }}
                    </p>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label class="text-sm font-bold text-[#142b52]">
                            Pesan atau masukan <span class="text-[#b77932]">*</span>
                        </label>
                        <span class="text-xs tabular-nums text-slate-400">
                            {{ form.message.length }}/2000
                        </span>
                    </div>

                    <textarea
                        v-model="form.message"
                        rows="4"
                        maxlength="2000"
                        required
                        placeholder="Tuliskan masukan Anda mengenai makanan atau pelayanan..."
                        class="min-h-[120px] w-full resize-y rounded-xl border border-slate-200 bg-[#fafbfd] px-4 py-3.5 text-sm leading-6 text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#142b52] focus:bg-white focus:ring-4 focus:ring-[#142b52]/[0.06]"
                    ></textarea>

                    <p v-if="form.errors.message" class="mt-2 text-xs text-red-600">
                        {{ form.errors.message }}
                    </p>
                </div>

                <div>
                    <div class="mb-4">
                        <h3 class="text-sm font-bold text-[#142b52]">
                            Informasi tambahan
                        </h3>
                        <p class="mt-1 text-xs text-slate-400">
                            Nama dan kontak bersifat opsional.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-xs font-semibold text-slate-600">
                                Nama <span class="font-normal text-slate-400">(opsional)</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                maxlength="100"
                                placeholder="Nama Anda"
                                class="h-12 w-full rounded-xl border border-slate-200 bg-[#fafbfd] px-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#142b52] focus:bg-white focus:ring-4 focus:ring-[#142b52]/[0.06]"
                            />
                            <p v-if="form.errors.name" class="mt-2 text-xs text-red-600">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-semibold text-slate-600">
                                Kontak <span class="font-normal text-slate-400">(opsional)</span>
                            </label>
                            <input
                                v-model="form.contact"
                                type="text"
                                maxlength="100"
                                placeholder="Email atau nomor telepon"
                                class="h-12 w-full rounded-xl border border-slate-200 bg-[#fafbfd] px-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#142b52] focus:bg-white focus:ring-4 focus:ring-[#142b52]/[0.06]"
                            />
                            <p v-if="form.errors.contact" class="mt-2 text-xs text-red-600">
                                {{ form.errors.contact }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 border-t border-slate-100 pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <p class="max-w-md text-xs leading-5 text-slate-400">
                        Masukan Anda akan digunakan sebagai bahan evaluasi untuk meningkatkan kualitas pelayanan.
                    </p>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#142b52] px-7 text-sm font-bold text-white shadow-sm transition hover:bg-[#203d6b] disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {{ form.processing ? 'Mengirim...' : 'Kirim Masukan' }}
                        <svg v-if="!form.processing" viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14"></path>
                            <path d="m12 5 7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </section>
</template>
