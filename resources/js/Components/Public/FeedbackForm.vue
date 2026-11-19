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
    <section class="public-page-enter mx-auto max-w-7xl px-5 pb-12 pt-8 sm:px-8 lg:px-10 lg:pb-14">
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-[0_8px_28px_rgba(20,43,82,0.06)]">
            <div class="relative overflow-hidden bg-[#142b52] px-6 py-4 text-white sm:px-8">
                <div class="pointer-events-none absolute -right-8 -top-28 h-56 w-56 rounded-full border border-white/[0.08]"></div>
                <p class="relative text-[10px] font-bold uppercase tracking-[0.22em] text-[#e4c58f]">
                    Suara Penerima Manfaat
                </p>
                <div class="relative mt-3 h-px w-16 bg-[#d5ad69]"></div>
            </div>

            <div class="grid lg:grid-cols-[0.82fr_1.18fr]">
                <aside class="border-b border-slate-100 px-6 py-6 sm:px-8 sm:py-8 lg:border-b-0 lg:border-r lg:px-9">
                    <span class="inline-flex rounded bg-[#e8eef5] px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-[#142b52]">
                        Saluran pengawasan publik
                    </span>
                    <h2 class="mt-4 text-2xl font-black leading-tight tracking-tight text-[#142b52]">
                        Kirim Ulasan &amp; Catatan Langsung ke Admin SPPG
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        Masukan Anda membantu meningkatkan kualitas makanan dan pelayanan Makan Bergizi Gratis.
                    </p>

                    <div class="mt-6 rounded-xl border border-slate-200 bg-[#f8fafc] p-4">
                        <p class="flex items-center gap-2 text-xs font-bold text-[#142b52]">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" stroke="currentColor" stroke-width="2.5">
                                    <path d="m5 12 4 4L19 6"></path>
                                </svg>
                            </span>
                            Informasi masukan
                        </p>
                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Masukan Anda akan digunakan sebagai bahan evaluasi untuk meningkatkan kualitas pelayanan.
                        </p>
                    </div>
                </aside>

                <form @submit.prevent="submit" class="p-6 sm:p-8 lg:p-9">
                    <div
                        v-if="page.props.flash?.success"
                        class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
                    >
                        <svg viewBox="0 0 24 24" fill="none" class="mt-0.5 h-5 w-5 shrink-0" stroke="currentColor" stroke-width="2">
                            <path d="m5 12 4 4L19 6"></path>
                        </svg>
                        <span>{{ page.props.flash.success }}</span>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="mb-2.5 block text-xs font-bold text-[#142b52]">
                                Pilih kategori tanggapan <span class="text-[#b77932]">*</span>
                            </label>

                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                <button
                                    v-for="category in categories"
                                    :key="category.value"
                                    type="button"
                                    :aria-label="`Kategori ${category.label}`"
                                    @click="form.category = category.value"
                                    :aria-pressed="form.category === category.value"
                                    :class="form.category === category.value
                                        ? 'border-[#142b52] bg-[#142b52] text-white'
                                        : 'border-slate-200 bg-white text-slate-600 hover:border-[#a87532] hover:text-[#142b52]'"
                                    class="min-h-10 rounded-md border px-2 py-2 text-[11px] font-semibold transition duration-200 hover:-translate-y-px active:translate-y-0"
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
                                <label for="feedback-message" class="text-xs font-bold text-[#142b52]">
                                    Isi ulasan dan catatan detail <span class="text-[#b77932]">*</span>
                                </label>
                                <span class="text-xs tabular-nums text-slate-400">
                                    {{ form.message.length }}/2000
                                </span>
                            </div>

                            <textarea
                                id="feedback-message"
                                v-model="form.message"
                                rows="4"
                                maxlength="2000"
                                required
                                placeholder="Tuliskan masukan Anda mengenai makanan atau pelayanan..."
                                class="min-h-[112px] w-full resize-y rounded-md border border-slate-200 bg-white px-3 py-3 text-sm leading-5 text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#142b52] focus:ring-4 focus:ring-[#142b52]/[0.06]"
                            ></textarea>

                            <p v-if="form.errors.message" class="mt-2 text-xs text-red-600">
                                {{ form.errors.message }}
                            </p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label for="feedback-name" class="mb-1.5 block text-[11px] font-semibold text-slate-600">
                                    Nama <span class="font-normal text-slate-400">(opsional)</span>
                                </label>
                                <input
                                    id="feedback-name"
                                    v-model="form.name"
                                    type="text"
                                    maxlength="100"
                                    placeholder="Nama Anda"
                                    class="h-10 w-full rounded-md border border-slate-200 bg-white px-3 text-xs text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#142b52] focus:ring-4 focus:ring-[#142b52]/[0.06]"
                                />
                                <p v-if="form.errors.name" class="mt-2 text-xs text-red-600">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div>
                                <label for="feedback-contact" class="mb-1.5 block text-[11px] font-semibold text-slate-600">
                                    Kontak <span class="font-normal text-slate-400">(opsional)</span>
                                </label>
                                <input
                                    id="feedback-contact"
                                    v-model="form.contact"
                                    type="text"
                                    maxlength="100"
                                    placeholder="Email atau nomor telepon"
                                    class="h-10 w-full rounded-md border border-slate-200 bg-white px-3 text-xs text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#142b52] focus:ring-4 focus:ring-[#142b52]/[0.06]"
                                />
                                <p v-if="form.errors.contact" class="mt-2 text-xs text-red-600">
                                    {{ form.errors.contact }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                            <p class="max-w-xs text-[11px] leading-5 text-slate-400">
                                Nama dan kontak tidak wajib diisi.
                            </p>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-md bg-[#142b52] px-5 py-2.5 text-xs font-bold text-white transition duration-200 hover:-translate-y-px hover:bg-[#203d6b] hover:shadow-md active:translate-y-0 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                {{ form.processing ? 'Mengirim...' : 'Kirim Ulasan Langsung ke Admin SPPG' }}
                                <svg v-if="!form.processing" viewBox="0 0 24 24" fill="none" class="h-4 w-4 text-[#e4c58f]" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14"></path>
                                    <path d="m12 5 7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>
