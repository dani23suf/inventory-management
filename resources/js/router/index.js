import { createRouter, createWebHistory } from 'vue-router';

import Products from '../views/Products.vue';
import StockIn from '../views/StockIn.vue';
import ProductDetail from '../views/ProductDetail.vue';


const routes = [
    {
        path: '/',
        redirect: '/products',
    },
    {
        path: '/products',
        name: 'products',
        component: Products,
    },
    {
        path: '/stock/in',
        name: 'stock-in',
        component: StockIn,
    },
     {
        path: '/products/:id',
        name: 'products.detail',
        component: ProductDetail,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;