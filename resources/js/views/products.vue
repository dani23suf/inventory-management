<script setup>
import { onMounted, ref } from 'vue';
import { useInventory } from '../composables/useInventory';

const products = ref([]);

const {
    getProducts,
    loading,
    error,
} = useInventory();

const fetchProducts = async () => {
    try {
        const response = await getProducts();

        products.value = response.data;
    } catch (err) {
        console.error(err);
    }
};

const getTotalStock = (product) => {
    return product.stocks.reduce(
        (total, stock) => total + Number(stock.quantity),
        0
    );
};

onMounted(() => {
    fetchProducts();
});
</script>

<template>
    <div>

        <!-- Header -->
        <div class="page-header">

            <div>
                <h1>Products</h1>
                <p>Daftar produk dan total stok.</p>
            </div>

            <router-link
                to="/stock/in"
                class="btn btn-primary"
            >
                + Stock In
            </router-link>

        </div>


        <!-- Loading -->
        <div
            v-if="loading"
            class="loading"
        >
            Loading products...
        </div>


        <!-- Error -->
        <div
            v-else-if="error"
            class="alert alert-error"
        >
            {{ error }}

            <button @click="fetchProducts">
                Coba Lagi
            </button>
        </div>


        <!-- Products -->
        <div
            v-else
            class="table-wrapper"
        >

            <table>

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Unit</th>
                        <th>Total Stock</th>
                        <th>Detail</th>
                    </tr>
                </thead>

                <tbody>

                    <tr
                        v-for="(product, index) in products"
                        :key="product.id"
                    >

                        <td>
                            {{ index + 1 }}
                        </td>

                        <td>
                            <strong>
                                {{ product.name }}
                            </strong>
                        </td>

                        <td>
                            {{ product.sku }}
                        </td>

                        <td>
                            {{ product.unit }}
                        </td>

                        <td>
                            <strong>
                                {{ getTotalStock(product) }}
                            </strong>

                            {{ product.unit }}
                        </td>

                        <td>

                            <router-link
                                :to="{
                                    name: 'products.detail',
                                    params: {
                                        id: product.id
                                    }
                                }"
                                class="btn btn-primary"
                            >
                                Detail
                            </router-link>

                        </td>

                    </tr>


                    <tr v-if="products.length === 0">

                        <td
                            colspan="6"
                            class="empty"
                        >
                            Belum ada product.
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>
</template>

<style scoped>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.page-header h1 {
    margin: 0 0 5px;
}

.page-header p {
    margin: 0;
    color: #666;
}

.btn {
    display: inline-block;
    padding: 10px 16px;
    border-radius: 6px;
    text-decoration: none;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.loading {
    padding: 30px;
    text-align: center;
}

.alert {
    padding: 15px;
    border-radius: 6px;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
}

.alert-error button {
    margin-left: 10px;
    padding: 5px 10px;
    cursor: pointer;
}

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 14px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

th {
    background: #f5f5f5;
}

.empty {
    text-align: center;
    color: #777;
}
</style>