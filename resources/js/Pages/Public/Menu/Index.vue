
<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout,
});

const props = defineProps({
    sppg: {
        type: Object,
        default: null,
    },
    menus: {
        type: Object,
        required: true,
    },
});

const portionTypes = [
    { key: 'large', label: 'Porsi Besar' },
    { key: 'small', label: 'Porsi Kecil' },
    { key: 'toddler', label: 'Porsi Balita' },
];

const formatDate = (value) => {
    if (!value) return '-';

    const [year, month, day] = value.substring(0, 10).split('-');
    const date = new Date(Number(year), Number(month) - 1, Number(day));

    return new Intl.DateTimeFormat('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(date);
};

const menuCount = computed(() => props.menus.data?.length || 0);
</script>

<template>
    <div>
        <section class="bg-blue-700 text-white">
            <div class="mx-auto max-w-7xl px-6 py-16 md:py-20">
                <p class="text-sm font-semibold uppercase tracking-wider text-blue-200">
                    Makan Bergizi Gratis
                </p>

                <h1 class="mt-4 max-w-3xl text-3xl font-bold leading-tight md:text-5xl">
                    Menu harian dan informasi gizi makanan
                </h1>

                <p class="mt-5 max-w-2xl text-base leading-relaxed text-blue-100">
                    Lihat menu makanan yang disajikan beserta informasi kandungan
                    gizinya untuk setiap kategori porsi.
                </p>

                <div v-if="sppg" class="mt-8 border-t border-blue-500 pt-5">
                    <p class="text-sm text-blue-200">Disediakan oleh</p>
                    <p class="mt-1 text-lg font-semibold">{{ sppg.name }}</p>
                    <p v-if="sppg.address" class="mt-1 text-sm text-blue-100">
                        {{ sppg.address }}
                    </p>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-6 py-12">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-blue-700">INFORMASI MENU</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">
                        Menu yang dipublikasikan
                    </h2>
                    <p class="mt-2 text-sm text-slate-500">
                        Pilih tanggal menu untuk melihat detail makanan dan nutrisinya.
                    </p>
                </div>

                <p class="text-sm text-slate-500">
                    {{ menuCount }} menu pada halaman ini
                </p>
            </div>

            <div
                v-if="menus.data?.length"
                class="grid gap-6 md:grid-cols-2 xl:grid-cols-3"
            >
                <article
                    v-for="menu in menus.data"
                    :key="menu.id"
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md"
                >
                    <div class="bg-blue-50 px-6 py-5">
                        <p class="text-sm font-medium text-blue-700">
                            {{ formatDate(menu.menu_date) }}
                        </p>
                        <h3 class="mt-2 text-xl font-bold text-slate-900">
                            {{ menu.title }}
                        </h3>
                        <p v-if="menu.description" class="mt-2 text-sm leading-relaxed text-slate-600">
                            {{ menu.description }}
                        </p>
                    </div>

                    <div class="space-y-4 p-6">
                        <div
                            v-for="portion in portionTypes"
                            :key="portion.key"
                            class="border-b border-slate-100 pb-3 last:border-0 last:pb-0"
                        >
                            <p class="text-sm font-semibold text-slate-800">
                                {{ portion.label }}
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                {{
                                    menu.items?.filter(item => item.portion_type === portion.key).length
                                    || 0
                                }} jenis makanan
                            </p>
                        </div>
                    </div>

                    <div class="px-6 pb-6">
                        <Link
                            :href="`/menu/${menu.id}`"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-blue-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-800"
                        >
                            Lihat Detail Menu
                        </Link>
                    </div>
                </article>
            </div>

            <div
                v-else
                class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center"
            >
                <h3 class="text-lg font-semibold text-slate-800">
                    Belum ada menu yang dipublikasikan
                </h3>
                <p class="mt-2 text-sm text-slate-500">
                    Informasi menu akan ditampilkan di halaman ini setelah dipublikasikan.
                </p>
            </div>

            <div
                v-if="menus.links?.length > 3"
                class="mt-8 flex flex-wrap justify-center gap-2"
            >
                <template v-for="(link, index) in menus.links" :key="index">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="rounded-lg border px-4 py-2 text-sm"
                        :class="link.active
                            ? 'border-blue-700 bg-blue-700 text-white'
                            : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'"
                        v-html="link.label"
                    />
                </template>
            </div>
        </section>
    </div>
</template>
