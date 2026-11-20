
<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout,
});

const props = defineProps({
    menus: {
        type: Object,
        required: true,
    },
    menuItems: {
        type: Array,
        default: () => [],
    },
});

const showForm = ref(false);
const editingId = ref(null);

const localDate = () => {
    const date = new Date();
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const form = useForm({
    menu_date: localDate(),
    title: '',
    description: '',
    items: [],
});

const addItem = (portionType = 'large') => {
    form.items.push({
        menu_item_id: '',
        portion_type: portionType,
        quantity: 1,
        unit: 'porsi',
        calories: '',
        protein: '',
        carbs: '',
        fiber: '',
    });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const selectMenuItem = (item) => {
    const food = props.menuItems.find((menuItem) => menuItem.id === Number(item.menu_item_id));
    if (!food) return;

    item.calories = food.calories ?? '';
    item.protein = food.protein ?? '';
    item.carbs = food.carbs ?? '';
    item.fiber = food.fiber ?? '';
};

const openCreateForm = () => {
    editingId.value = null;
    form.reset();
    form.menu_date = localDate();
    form.items = [];
    addItem('large');
    showForm.value = true;
};

const openEditForm = (menu) => {
    editingId.value = menu.id;

    form.menu_date = menu.menu_date?.substring(0, 10) ?? '';
    form.title = menu.title;
    form.description = menu.description ?? '';

    form.items = menu.items.map((item) => ({
        menu_item_id: item.menu_item_id,
        portion_type: item.portion_type,
        quantity: Number(item.quantity),
        unit: item.unit,
        calories: item.calories ?? '',
        protein: item.protein ?? '',
        carbs: item.carbs ?? '',
        fiber: item.fiber ?? '',
    }));

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
        form.put(`/admin/menus/${editingId.value}`, {
            onSuccess: () => closeForm(),
        });
    } else {
        form.post('/admin/menus', {
            onSuccess: () => closeForm(),
        });
    }
};

const changeStatus = (menu) => {
    const action = menu.status === 'published' ? 'unpublish' : 'publish';

    router.patch(`/admin/menus/${menu.id}/${action}`);
};

const deleteMenu = (menu) => {
    if (menu.status === 'published') {
        alert('Menu yang dipublikasikan harus dijadikan draft sebelum dihapus.');
        return;
    }

    if (!confirm(`Hapus draft "${menu.title}"? Item menu dan dokumentasi terkait juga akan dihapus.`)) {
        return;
    }

    router.delete(`/admin/menus/${menu.id}`);
};

const formatDate = (date) => {
    if (!date) return '-';

    return new Date(`${date.substring(0, 10)}T00:00:00`).toLocaleDateString(
        'id-ID',
        {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        },
    );
};

const portionLabel = (type) => {
    return {
        large: 'Porsi Besar',
        small: 'Porsi Kecil',
        toddler: 'Porsi Balita',
    }[type] ?? 'Porsi Tidak Dikenal';
};

</script>

<template>
    <Head title="Kelola Menu Harian" />

    <div class="space-y-6">
        <div
            v-if="$page.props.flash?.success"
            role="status"
            class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"
        >
            {{ $page.props.flash.success }}
        </div>
        <div
            v-if="$page.props.errors.menu"
            role="alert"
            class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
        >
            {{ $page.props.errors.menu }}
        </div>
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
∑            <div>
                <h2 class="text-lg font-semibold text-slate-800">
                    Daftar Menu Harian
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Buat dan kelola menu makanan MBG berdasarkan tanggal.
                </p>
            </div>

            <button
                type="button"
                @click="openCreateForm"
                class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
            >
                + Tambah Menu
            </button>
        </div>

        <!-- Form tambah dan edit -->
        <section
            v-if="showForm"
            class="overflow-hidden rounded-xl border border-blue-200 bg-white shadow-sm"
        >
            <div class="flex items-center justify-between border-b border-slate-100 p-5">
                <div>
                    <h3 class="font-semibold text-slate-800">
                        {{ editingId ? 'Edit Menu Harian' : 'Tambah Menu Harian' }}
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Lengkapi informasi menu dan susunan makanan.
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

            <form @submit.prevent="submit" class="space-y-6 p-5">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Tanggal Menu *
                        </label>
                        <input
                            v-model="form.menu_date"
                            type="date"
                            required
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        />
                        <p v-if="form.errors.menu_date" class="mt-1 text-xs text-red-600">
                            {{ form.errors.menu_date }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Nama Menu *
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            required
                            placeholder="Contoh: Menu Makan Siang"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        />
                        <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">
                            {{ form.errors.title }}
                        </p>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Deskripsi Menu
                    </label>
                    <textarea
                        v-model="form.description"
                        rows="3"
                        placeholder="Informasi tambahan mengenai menu hari ini..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    ></textarea>
                </div>

                <div class="border-t border-slate-100 pt-5">
                    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                        <div>
                            <h4 class="font-semibold text-slate-800">
                                Susunan Makanan
                            </h4>
                            <p class="mt-1 text-sm text-slate-500">
                                Pilih makanan, jenis porsi, dan nilai nutrisinya.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                @click="addItem('large')"
                                class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100"
                            >
                                + Porsi Besar
                            </button>

                            <button
                                type="button"
                                @click="addItem('small')"
                                class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100"
                            >
                                + Porsi Kecil
                            </button>

			<button
    type="button"
    @click="addItem('toddler')"
    class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-100"
>
    + Porsi Balita
</button>	
                        </div>
                    </div>

                    <div
                        v-if="menuItems.length === 0"
                        class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4"
                    >
                        <p class="text-sm font-medium text-amber-800">
                            Belum ada master makanan.
                        </p>
                        <p class="mt-1 text-sm text-amber-700">
                            Tambahkan data makanan terlebih dahulu melalui menu Kelola Makanan.
                        </p>
                        <a
                            href="/admin/menu-items"
                            class="mt-2 inline-block text-sm font-semibold text-amber-800 underline"
                        >
                            Buka Kelola Makanan
                        </a>
                    </div>

                    <div v-else class="mt-5 space-y-4">
                        <div
                            v-for="(item, index) in form.items"
                            :key="index"
                            class="rounded-xl border border-slate-200 p-4"
                        >
                            <div class="mb-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-xs font-semibold text-slate-600">
                                        {{ index + 1 }}
                                    </span>

                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="item.portion_type === 'large'
                                            ? 'bg-blue-50 text-blue-700'
                                            : 'bg-emerald-50 text-emerald-700'"
                                    >
                                        {{ portionLabel(item.portion_type) }}
                                    </span>
                                </div>

                                <button
                                    type="button"
                                    @click="removeItem(index)"
                                    class="text-xs font-medium text-red-500 hover:text-red-700"
                                >
                                    Hapus
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-xs font-medium text-slate-600">
                                        Nama Makanan *
                                    </label>
                                    <select
                                        v-model="item.menu_item_id"
                                        @change="selectMenuItem(item)"
                                        required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500"
                                    >
                                        <option value="">Pilih makanan</option>
                                        <option
                                            v-for="food in menuItems"
                                            :key="food.id"
                                            :value="food.id"
                                        >
                                            {{ food.name }}{{ food.category ? ` — ${food.category}` : '' }}
                                        </option>
                                    </select>
                                    <p
                                        v-if="form.errors[`items.${index}.menu_item_id`]"
                                        class="mt-1 text-xs text-red-600"
                                    >
                                        {{ form.errors[`items.${index}.menu_item_id`] }}
                                    </p>
                                </div>

                                <div>
                                    <label class="mb-2 block text-xs font-medium text-slate-600">
                                        Jenis Porsi *
                                    </label>
                                    <select
                                        v-model="item.portion_type"
                                        required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500"
                                    >
                                        <option value="large">Porsi Besar</option>
                                        <option value="small">Porsi Kecil</option>
					<option value="toddler">Porsi Balita</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-2 block text-xs font-medium text-slate-600">
                                        Jumlah *
                                    </label>
                                    <input
                                        v-model="item.quantity"
                                        type="number"
                                        min="0.01"
                                        step="0.01"
                                        required
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500"
                                    />
                                </div>

                                <div>
                                    <label class="mb-2 block text-xs font-medium text-slate-600">
                                        Satuan *
                                    </label>
                                    <input
                                        v-model="item.unit"
                                        type="text"
                                        required
                                        placeholder="porsi, gram, buah..."
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500"
                                    />
                                </div>
                            </div>

                            <div class="mt-5 border-t border-slate-100 pt-4">
                                <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Informasi Nutrisi
                                </p>

                                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                                    <div>
                                        <label class="mb-1.5 block text-xs text-slate-500">
                                            Kalori (kkal)
                                        </label>
                                        <input
                                            v-model="item.calories"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            placeholder="0"
                                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-xs text-slate-500">
                                            Protein (g)
                                        </label>
                                        <input
                                            v-model="item.protein"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            placeholder="0"
                                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-xs text-slate-500">
                                            Karbohidrat (g)
                                        </label>
                                        <input
                                            v-model="item.carbs"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            placeholder="0"
                                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-xs text-slate-500">
                                            Serat (g)
                                        </label>
                                        <input
                                            v-model="item.fiber"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            placeholder="0"
                                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p v-if="form.errors.items" class="mt-3 text-sm text-red-600">
                        {{ form.errors.items }}
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
                        :disabled="form.processing || menuItems.length === 0"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menyimpan...' : (editingId ? 'Simpan Perubahan' : 'Simpan Menu') }}
                    </button>
                </div>
            </form>
        </section>

        <!-- Daftar menu -->
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 p-5">
                <h3 class="font-semibold text-slate-800">Data Menu</h3>
                <p class="mt-1 text-sm text-slate-500">
                    Daftar menu harian yang telah dibuat.
                </p>
            </div>

            <div v-if="menus.data.length" class="divide-y divide-slate-100">
                <article
                    v-for="menu in menus.data"
                    :key="menu.id"
                    class="p-5 transition hover:bg-slate-50/70"
                >
                    <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-start">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h4 class="font-semibold text-slate-800">
                                    {{ menu.title }}
                                </h4>

                                <span
                                    class="rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="menu.status === 'published'
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'bg-amber-50 text-amber-700'"
                                >
                                    {{ menu.status === 'published' ? 'Dipublikasikan' : 'Draft' }}
                                </span>
                            </div>

                            <p class="mt-1 text-sm text-slate-500">
                                {{ formatDate(menu.menu_date) }}
                            </p>

                            <p v-if="menu.description" class="mt-2 text-sm text-slate-600">
                                {{ menu.description }}
                            </p>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span
                                    v-for="item in menu.items"
                                    :key="item.id"
                                    class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs text-slate-600"
                                >
                                    {{ item.menu_item?.name ?? 'Makanan' }}
                                    · {{ portionLabel(item.portion_type) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-wrap gap-2">
                            <button
                                type="button"
                                @click="openEditForm(menu)"
                                class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-white"
                            >
                                Edit
                            </button>

                            <button
                                type="button"
                                @click="changeStatus(menu)"
                                class="rounded-lg border px-3 py-2 text-xs font-semibold"
                                :class="menu.status === 'published'
                                    ? 'border-amber-200 text-amber-700 hover:bg-amber-50'
                                    : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50'"
                            >
                                {{ menu.status === 'published' ? 'Jadikan Draft' : 'Publikasikan' }}
                            </button>

                            <button
                                type="button"
                                @click="deleteMenu(menu)"
                                v-if="menu.status === 'draft'"
                                class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </article>
            </div>

            <div v-else class="p-12 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400">
                    ▤
                </div>
                <h4 class="mt-4 font-semibold text-slate-700">
                    Belum ada menu harian
                </h4>
                <p class="mt-1 text-sm text-slate-400">
                    Klik Tambah Menu untuk membuat menu pertama.
                </p>
            </div>

            <div
                v-if="menus.links && menus.links.length > 3"
                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 p-4"
            >
                <p class="text-xs text-slate-500">
                    Menampilkan {{ menus.from ?? 0 }}–{{ menus.to ?? 0 }}
                    dari {{ menus.total }} menu
                </p>

                <div class="flex flex-wrap gap-1">
                    <template v-for="(link, index) in menus.links" :key="index">
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

