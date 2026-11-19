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
    <section class="public-scroll-reveal mx-auto max-w-7xl px-5 pb-12 pt-8 sm:px-8 lg:px-10 lg:pb-14">
        <div class="overflow-hidden rounded-xl border border-bgn-line bg-white shadow-sm">
            <div class="relative overflow-hidden bg-bgn-navy px-6 py-4 text-white sm:px-8">
                <p class="relative text-[10px] font-bold uppercase tracking-[0.22em] text-bgn-gold-soft">
                    Suara Penerima Manfaat
                </p>
                <div class="relative mt-3 h-px w-16 bg-bgn-gold"></div>
            </div>

            <div class="grid lg:grid-cols-[0.82fr_1.18fr]">
                <aside class="border-b border-slate-100 px-6 py-6 sm:px-8 sm:py-8 lg:border-b-0 lg:border-r lg:px-9">
                    <span class="inline-flex rounded-sm bg-bgn-surface px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-bgn-navy">
                        Saluran pengawasan publik
                    </span>
                    <h2 class="mt-4 text-2xl font-bold leading-tight tracking-tight text-bgn-navy">
                        Kirim Ulasan &amp; Catatan Langsung ke Admin SPPG
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        Masukan Anda membantu meningkatkan kualitas makanan dan pelayanan Makan Bergizi Gratis.
                    </p>

                    <div class="mt-6 rounded-lg border border-bgn-line bg-bgn-surface p-4">
                        <p class="flex items-center gap-2 text-xs font-bold text-bgn-navy">
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
                        role="status"
                        aria-live="polite"
                        class="mb-5 flex items-start gap-3 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
                    >
                        <svg viewBox="0 0 24 24" fill="none" class="mt-0.5 h-5 w-5 shrink-0" stroke="currentColor" stroke-width="2">
                            <path d="m5 12 4 4L19 6"></path>
                        </svg>
                        <span>{{ page.props.flash.success }}</span>
                    </div>

                    <div class="space-y-5">
                        <fieldset
                            :aria-describedby="form.errors.category ? 'feedback-category-error' : undefined"
                            :aria-invalid="Boolean(form.errors.category)"
                        >
                            <legend class="mb-2.5 text-sm font-bold text-bgn-navy">
                                Pilih kategori tanggapan <span class="text-bgn-gold-dark">*</span>
                            </legend>

                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                <button
                                    v-for="category in categories"
                                    :key="category.value"
                                    type="button"
                                    :aria-label="`Kategori ${category.label}`"
                                    @click="form.category = category.value"
                                    :aria-pressed="form.category === category.value"
                                    :class="form.category === category.value
                                        ? 'border-bgn-navy bg-bgn-navy text-white'
                                        : 'border-slate-200 bg-white text-slate-600 hover:border-bgn-gold hover:bg-bgn-surface hover:text-bgn-navy'"
                                    class="min-h-11 rounded-md border px-3 py-2 text-xs font-semibold transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-bgn-gold focus-visible:ring-offset-2"
                                >
                                    {{ category.label }}
                                </button>
                            </div>

                            <p v-if="form.errors.category" id="feedback-category-error" role="alert" class="mt-2 text-xs text-red-600">
                                {{ form.errors.category }}
                            </p>
                        </fieldset>

                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <label for="feedback-message" class="text-sm font-bold text-bgn-navy">
                                    Isi ulasan dan catatan detail <span class="text-bgn-gold-dark">*</span>
                                </label>
                                <span class="text-xs tabular-nums text-slate-400">
                                    {{ form.message.length }}/2000
                                </span>
                            </div>

                            <textarea
                                id="feedback-message"
                                v-model="form.message"
                                :aria-invalid="Boolean(form.errors.message)"
                                :aria-describedby="form.errors.message ? 'feedback-message-error' : undefined"
                                rows="4"
                                maxlength="2000"
                                required
                                placeholder="Tuliskan masukan Anda mengenai makanan atau pelayanan..."
                                :class="form.errors.message
                                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10'
                                    : 'border-slate-200 focus:border-bgn-navy focus:ring-bgn-navy/10'"
                                class="min-h-[120px] w-full resize-y rounded-md border bg-white px-3 py-3 text-sm leading-5 text-slate-700 outline-none transition-colors placeholder:text-slate-400 focus:ring-2"
                            ></textarea>

                            <p v-if="form.errors.message" id="feedback-message-error" role="alert" class="mt-2 text-xs text-red-600">
                                {{ form.errors.message }}
                            </p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label for="feedback-name" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                    Nama <span class="font-normal text-slate-400">(opsional)</span>
                                </label>
                                <input
                                    id="feedback-name"
                                    v-model="form.name"
                                    :aria-invalid="Boolean(form.errors.name)"
                                    :aria-describedby="form.errors.name ? 'feedback-name-error' : undefined"
                                    type="text"
                                    maxlength="100"
                                    placeholder="Nama Anda"
                                    :class="form.errors.name
                                        ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10'
                                        : 'border-slate-200 focus:border-bgn-navy focus:ring-bgn-navy/10'"
                                    class="h-11 w-full rounded-md border bg-white px-3 text-sm text-slate-700 outline-none transition-colors placeholder:text-slate-400 focus:ring-2"
                                />
                                <p v-if="form.errors.name" id="feedback-name-error" role="alert" class="mt-2 text-xs text-red-600">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div>
                                <label for="feedback-contact" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                    Kontak <span class="font-normal text-slate-400">(opsional)</span>
                                </label>
                                <input
                                    id="feedback-contact"
                                    v-model="form.contact"
                                    :aria-invalid="Boolean(form.errors.contact)"
                                    :aria-describedby="form.errors.contact ? 'feedback-contact-error' : undefined"
                                    type="text"
                                    maxlength="100"
                                    placeholder="Email atau nomor telepon"
                                    :class="form.errors.contact
                                        ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10'
                                        : 'border-slate-200 focus:border-bgn-navy focus:ring-bgn-navy/10'"
                                    class="h-11 w-full rounded-md border bg-white px-3 text-sm text-slate-700 outline-none transition-colors placeholder:text-slate-400 focus:ring-2"
                                />
                                <p v-if="form.errors.contact" id="feedback-contact-error" role="alert" class="mt-2 text-xs text-red-600">
                                    {{ form.errors.contact }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                            <p class="max-w-xs text-xs leading-5 text-slate-500">
                                Nama dan kontak tidak wajib diisi.
                            </p>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                :aria-busy="form.processing"
                                class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-md bg-bgn-navy px-5 py-2.5 text-sm font-bold text-white transition-colors duration-150 hover:bg-bgn-navy-deep focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-bgn-gold focus-visible:ring-offset-2 active:bg-bgn-navy disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                            >
                                {{ form.processing ? 'Mengirim...' : 'Kirim Ulasan Langsung ke Admin SPPG' }}
                                <svg v-if="!form.processing" viewBox="0 0 24 24" fill="none" class="h-4 w-4 text-bgn-gold-soft" stroke="currentColor" stroke-width="2">
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
