import { ref } from 'vue';
import axios from 'axios';

export function useInventory() {
    const loading = ref(false);
    const error = ref(null);

    const getProduct = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get(`/api/products/${id}`);

            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message
                ?? 'Gagal mengambil data product.';

            throw err;
        } finally {
            loading.value = false;
        }
    };

    const getStockReport = async ({
        productId,
        warehouseId = null,
        type = null,
        page = 1,
    }) => {
        loading.value = true;
        error.value = null;

        try {
            const params = {
                product_id: productId,
                page,
            };

            if (warehouseId) {
                params.warehouse_id = warehouseId;
            }

            /*
             * Backend report kita sebelumnya belum menerima
             * parameter type, jadi parameter ini dikirim jika
             * backend sudah mendukung filter type.
             */
            if (type) {
                params.type = type;
            }

            const response = await axios.get(
                '/api/stock/report',
                { params }
            );

            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message
                ?? 'Gagal mengambil stock report.';

            throw err;
        } finally {
            loading.value = false;
        }
    };

    const getWarehouses = async () => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get('/api/warehouses');

            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message
                ?? 'Gagal mengambil warehouse.';

            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
        loading,
        error,
        getProduct,
        getStockReport,
        getWarehouses,
    };
}