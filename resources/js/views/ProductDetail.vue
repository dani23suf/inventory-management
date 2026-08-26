<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useInventory } from '../composables/useInventory';

const route = useRoute();

const {
    loading,
    error,
    getProduct,
    getStockReport,
} = useInventory();

const product = ref(null);
const movements = ref([]);

const selectedType = ref('');

const reportMeta = ref(null);

const loadProduct = async () => {
    try {
        const response = await getProduct(route.params.id);

        product.value = response.data;
    } catch (err) {
        console.error(err);
    }
};

const loadMovements = async () => {
    try {
        const response = await getStockReport({
            productId: route.params.id,
            type: selectedType.value || null,
        });

        movements.value = response.data;

        reportMeta.value = response.meta;
    } catch (err) {
        console.error(err);
    }
};

const loadPage = async () => {
    await Promise.all([
        loadProduct(),
        loadMovements(),
    ]);
};

/*
 * Filter realtime.
 *
 * Tidak melakukan reload browser.
 * Hanya request API baru dan update state Vue.
 */
const changeType = async () => {
    await loadMovements();
};

onMounted(() => {
    loadPage();
});
</script>

<template>

    <div class="container py-4">

        <!-- Loading -->
        <div
            v-if="loading && !product"
            class="text-center py-5"
        >
            Loading...
        </div>

        <!-- Error -->
        <div
            v-if="error"
            class="alert alert-danger"
        >
            {{ error }}
        </div>

        <template v-if="product">

            <!-- Product Header -->
            <div class="mb-4">

                <h1 class="mb-1">
                    {{ product.name }}
                </h1>

                <div class="text-muted">
                    SKU: {{ product.sku }}
                    · Unit: {{ product.unit }}
                </div>

            </div>


            <!-- Stock per Warehouse -->
            <div class="card mb-4">

                <div class="card-header">
                    <h5 class="mb-0">
                        Stock per Gudang
                    </h5>
                </div>

                <div class="card-body p-0">

                    <div
                        v-if="!product.stock_by_warehouse?.length"
                        class="p-4 text-muted"
                    >
                        Belum ada stock.
                    </div>

                    <div
                        v-else
                        class="table-responsive"
                    >

                        <table class="table table-striped mb-0">

                            <thead>

                                <tr>
                                    <th>Gudang</th>
                                    <th>Lokasi</th>
                                    <th>Stock</th>
                                </tr>

                            </thead>

                            <tbody>

                                <tr
                                    v-for="stock in product.stock_by_warehouse"
                                    :key="stock.warehouse_id"
                                >

                                    <td>
                                        {{ stock.warehouse_name }}
                                    </td>

                                    <td>
                                        {{ stock.location }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ stock.stock }}
                                        </strong>

                                        {{ product.unit }}

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            <!-- Movement History -->
            <div class="card">

                <div class="card-header">

                    <div
                        class="d-flex justify-content-between align-items-center"
                    >

                        <h5 class="mb-0">
                            Riwayat Movement
                        </h5>

                        <!-- Filter -->
                        <select
                            v-model="selectedType"
                            @change="changeType"
                            class="form-select"
                            style="width: 180px"
                        >

                            <option value="">
                                Semua Movement
                            </option>

                            <option value="in">
                                Stock In
                            </option>

                            <option value="out">
                                Stock Out
                            </option>

                            <option value="transfer">
                                Transfer
                            </option>

                        </select>

                    </div>

                </div>


                <div class="card-body p-0">

                    <!-- Loading report -->
                    <div
                        v-if="loading"
                        class="text-center py-4"
                    >
                        Loading movement...
                    </div>

                    <div
                        v-else-if="!movements.length"
                        class="text-center py-4 text-muted"
                    >
                        Tidak ada movement.
                    </div>

                    <div
                        v-else
                        class="table-responsive"
                    >

                        <table class="table table-hover mb-0">

                            <thead>

                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Warehouse</th>
                                    <th>Quantity</th>
                                    <th>Reference</th>
                                    <th>Note</th>
                                </tr>

                            </thead>

                            <tbody>

                                <tr
                                    v-for="movement in movements"
                                    :key="movement.id"
                                >

                                    <td>
                                        {{ movement.created_at }}
                                    </td>

                                    <td>

                                        <span
                                            class="badge"
                                            :class="{
                                                'bg-success':
                                                    movement.type === 'in',

                                                'bg-danger':
                                                    movement.type === 'out',

                                                'bg-primary':
                                                    movement.type === 'transfer'
                                            }"
                                        >
                                            {{ movement.type }}
                                        </span>

                                    </td>

                                    <td>
                                        {{ movement.warehouse_id }}
                                    </td>

                                    <td>
                                        {{ movement.quantity }}
                                    </td>

                                    <td>
                                        {{ movement.reference_id ?? '-' }}
                                    </td>

                                    <td>
                                        {{ movement.note ?? '-' }}
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </template>

    </div>

</template>