<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';

const router = useRouter();

const products = ref([]);
const warehouses = ref([]);

const form = ref({
    product_id: '',
    warehouse_id: '',
    quantity: '',
    reference_id: '',
    note: '',
});

const errors = ref({});

const loading = ref(false);
const submitting = ref(false);
const fetchError = ref(null);
const successMessage = ref(null);

const fetchData = async () => {
    loading.value = true;
    fetchError.value = null;

    try {
        const [productsResponse, warehousesResponse] =
            await Promise.all([
                api.get('/products'),
                api.get('/warehouses'),
            ]);

        products.value = productsResponse.data.data;
        warehouses.value = warehousesResponse.data.data;

    } catch (err) {
        console.error(err);

        fetchError.value =
            err.response?.data?.message ||
            'Gagal mengambil data.';
    } finally {
        loading.value = false;
    }
};

const submit = async () => {
    submitting.value = true;
    errors.value = {};
    successMessage.value = null;

    try {
        await api.post('/stock/in', {
            product_id: form.value.product_id,
            warehouse_id: form.value.warehouse_id,
            quantity: form.value.quantity,
            reference_id: form.value.reference_id || null,
            note: form.value.note || null,
        });

        successMessage.value =
            'Stock berhasil ditambahkan.';

        form.value = {
            product_id: '',
            warehouse_id: '',
            quantity: '',
            reference_id: '',
            note: '',
        };

    } catch (err) {
        console.error(err);

        if (err.response?.status === 422) {
            errors.value =
                err.response.data.errors || {};
        } else {
            errors.value = {
                general: [
                    err.response?.data?.message ||
                    'Terjadi kesalahan.'
                ]
            };
        }
    } finally {
        submitting.value = false;
    }
};

const hasError = (field) => {
    return errors.value[field]?.length > 0;
};

onMounted(() => {
    fetchData();
});
</script>

<template>
    <div>
        <div class="page-header">
            <div>
                <h1>Stock In</h1>
                <p>Tambahkan stock ke warehouse.</p>
            </div>

            <router-link
                to="/products"
                class="btn btn-secondary"
            >
                ← Products
            </router-link>
        </div>

        <!-- Loading Fetch -->
        <div
            v-if="loading"
            class="loading"
        >
            Loading data...
        </div>

        <!-- Fetch Error -->
        <div
            v-else-if="fetchError"
            class="alert alert-error"
        >
            {{ fetchError }}

            <button @click="fetchData">
                Coba Lagi
            </button>
        </div>

        <!-- Form -->
        <form
            v-else
            @submit.prevent="submit"
            class="form"
        >

            <!-- General Error -->
            <div
                v-if="errors.general"
                class="alert alert-error"
            >
                {{ errors.general[0] }}
            </div>

            <!-- Success -->
            <div
                v-if="successMessage"
                class="alert alert-success"
            >
                {{ successMessage }}
            </div>

            <!-- Product -->
            <div class="form-group">
                <label>
                    Product
                </label>

                <select
                    v-model="form.product_id"
                    :class="{ 'input-error': hasError('product_id') }"
                >
                    <option value="">
                        -- Pilih Product --
                    </option>

                    <option
                        v-for="product in products"
                        :key="product.id"
                        :value="product.id"
                    >
                        {{ product.name }}
                        ({{ product.sku }})
                    </option>
                </select>

                <small
                    v-if="hasError('product_id')"
                    class="error-text"
                >
                    {{ errors.product_id[0] }}
                </small>
            </div>

            <!-- Warehouse -->
            <div class="form-group">
                <label>
                    Warehouse
                </label>

                <select
                    v-model="form.warehouse_id"
                    :class="{ 'input-error': hasError('warehouse_id') }"
                >
                    <option value="">
                        -- Pilih Warehouse --
                    </option>

                    <option
                        v-for="warehouse in warehouses"
                        :key="warehouse.id"
                        :value="warehouse.id"
                    >
                        {{ warehouse.name }}
                        - {{ warehouse.location }}
                    </option>
                </select>

                <small
                    v-if="hasError('warehouse_id')"
                    class="error-text"
                >
                    {{ errors.warehouse_id[0] }}
                </small>
            </div>

            <!-- Quantity -->
            <div class="form-group">
                <label>
                    Quantity
                </label>

                <input
                    v-model="form.quantity"
                    type="number"
                    min="1"
                    placeholder="Masukkan quantity"
                    :class="{ 'input-error': hasError('quantity') }"
                >

                <small
                    v-if="hasError('quantity')"
                    class="error-text"
                >
                    {{ errors.quantity[0] }}
                </small>
            </div>

            <!-- Reference -->
            <div class="form-group">
                <label>
                    Reference ID
                    <span>(optional)</span>
                </label>

                <input
                    v-model="form.reference_id"
                    type="number"
                    placeholder="Reference ID"
                    :class="{ 'input-error': hasError('reference_id') }"
                >

                <small
                    v-if="hasError('reference_id')"
                    class="error-text"
                >
                    {{ errors.reference_id[0] }}
                </small>
            </div>

            <!-- Note -->
            <div class="form-group">
                <label>
                    Note
                    <span>(optional)</span>
                </label>

                <textarea
                    v-model="form.note"
                    rows="4"
                    placeholder="Catatan"
                    :class="{ 'input-error': hasError('note') }"
                ></textarea>

                <small
                    v-if="hasError('note')"
                    class="error-text"
                >
                    {{ errors.note[0] }}
                </small>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                class="btn btn-primary btn-submit"
                :disabled="submitting"
            >
                <span v-if="submitting">
                    Menyimpan...
                </span>

                <span v-else>
                    Tambah Stock
                </span>
            </button>

        </form>
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

.form {
    max-width: 600px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 7px;
}

.form-group label span {
    font-weight: normal;
    color: #777;
}

input,
select,
textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    box-sizing: border-box;
}

textarea {
    resize: vertical;
}

.input-error {
    border-color: #dc2626;
}

.error-text {
    display: block;
    margin-top: 5px;
    color: #dc2626;
}

.alert {
    padding: 12px 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
}

.loading {
    padding: 30px;
    text-align: center;
}

.btn {
    display: inline-block;
    padding: 10px 16px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-secondary {
    background: #e5e7eb;
    color: #111827;
}

.btn-submit {
    width: 100%;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>