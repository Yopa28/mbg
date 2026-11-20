
<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import KitchenDocumentation from '@/Components/Public/KitchenDocumentation.vue';

defineOptions({
    layout: PublicLayout,
});

const props = defineProps({
    sppg: {
        type: Object,
        default: null,
    },
    menu: {
        type: Object,
        required: true,
    },
});

const portions = [
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

const getItems = (portionType) => {
    return props.menu.items?.filter(item => item.portion_type === portionType) || [];
};

const getTotal = (items, field) => {
    return items.reduce((total, item) => {
        return total + (Number(item[field]) || 0);
    }, 0);
};

const formatNumber = (value) => {
    return new Intl.NumberFormat('id-ID', {
        maximumFractionDigits: 2,
    }).format(value);
};
</script>

<template>
    <div>
        <section class="bg-blue-700 text-white">
            <div class="mx-auto max-w-7xl px-6 py-12">
                <Link href="/" class="text-sm font-medium text-blue-200 hover:text-white">
                    ← Kembali ke menu
                </Link>

                <p class="mt-8 text-sm font-semibold text-blue-200">
                    {{ formatDate(menu.menu_date) }}
                </p>

                <h1 class="mt-3 text-3xl font-bold md:text-4xl">
                    {{ menu.title }}
                </h1>

                <p v-if="menu.description" class="mt-4 max-w-2xl leading-relaxed text-blue-100">
                    {{ menu.description }}
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-6 py-12">
            <div class="mb-8">
                <p class="text-sm font-semibold text-blue-700">RINCIAN MAKANAN</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">
                    Komposisi dan kandungan gizi
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    Informasi makanan dan nilai nutrisi berdasarkan kategori porsi.
                </p>
            </div>

            <div class="space-y-10">
                <section
                    v-for="portion in portions"
                    :key="portion.key"
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white"
                >
                    <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                        <h3 class="text-xl font-bold text-slate-900">
                            {{ portion.label }}
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ getItems(portion.key).length }} jenis makanan
                        </p>
                    </div>

                    <div class="p-6">
                        <div
                            v-if="getItems(portion.key).length"
                            class="grid gap-4 md:grid-cols-2"
                        >
                            <article
                                v-for="item in getItems(portion.key)"
                                :key="item.id"
                                class="rounded-xl border border-slate-200 p-5"
                            >
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h4 class="font-semibold text-slate-900">
                                            {{ item.menu_item?.name || 'Makanan' }}
                                        </h4>
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ formatNumber(Number(item.quantity) || 0) }}
                                            {{ item.unit }}
                                        </p>
                                    </div>

                                    <span class="rounded-lg bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                        {{ formatNumber(Number(item.calories) || 0) }} kkal
                                    </span>
                                </div>

                                <div class="mt-5 grid grid-cols-3 gap-3 border-t border-slate-100 pt-4">
                                    <div>
                                        <p class="text-xs text-slate-500">Protein</p>
                                        <p class="mt-1 font-semibold text-slate-800">
                                            {{ formatNumber(Number(item.protein) || 0) }} g
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500">Karbohidrat</p>
                                        <p class="mt-1 font-semibold text-slate-800">
                                            {{ formatNumber(Number(item.carbs) || 0) }} g
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500">Serat</p>
                                        <p class="mt-1 font-semibold text-slate-800">
                                            {{ formatNumber(Number(item.fiber) || 0) }} g
                                        </p>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <p v-else class="py-6 text-center text-sm text-slate-500">
                            Belum ada data makanan untuk kategori porsi ini.
                        </p>

                        <div
                            v-if="getItems(portion.key).length"
                            class="mt-6 rounded-xl bg-blue-50 p-5"
                        >
                            <h4 class="font-semibold text-slate-900">
                                Total nutrisi {{ portion.label.toLowerCase() }}
                            </h4>

                            <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-4">
                                <div>
                                    <p class="text-xs text-slate-500">Energi</p>
                                    <p class="mt-1 text-lg font-bold text-blue-800">
                                        {{ formatNumber(getTotal(getItems(portion.key), 'calories')) }}
                                        <span class="text-xs font-medium">kkal</span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500">Protein</p>
                                    <p class="mt-1 text-lg font-bold text-slate-800">
                                        {{ formatNumber(getTotal(getItems(portion.key), 'protein')) }} g
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500">Karbohidrat</p>
                                    <p class="mt-1 text-lg font-bold text-slate-800">
                                        {{ formatNumber(getTotal(getItems(portion.key), 'carbs')) }} g
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500">Serat</p>
                                    <p class="mt-1 text-lg font-bold text-slate-800">
                                        {{ formatNumber(getTotal(getItems(portion.key), 'fiber')) }} g
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="mt-10">
                <Link
                    href="/"
                    class="inline-flex rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    ← Kembali ke daftar menu
                </Link>
            </div>
        </section>
        <KitchenDocumentation
            :documentations="menu.kitchen_documentations || []"
        />
    </div>
</template>
