<template>
    <div>
        <nav class="navbar">
            <div class="container">
                <div class="navbar-brand">
                    Inventory Management
                </div>

                <div class="navbar-menu">
                    <router-link to="/products">
                        Products
                    </router-link>

                    <router-link to="/stock/in">
                        Stock In
                    </router-link>
                </div>
            </div>
        </nav>

        <main class="container content">
            <router-view />
        </main>
    </div>
</template>

<style scoped>
.navbar {
    border-bottom: 1px solid #ddd;
    padding: 16px 0;
}

.container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 20px;
}

.navbar .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar-brand {
    font-size: 20px;
    font-weight: 700;
}

.navbar-menu {
    display: flex;
    gap: 20px;
}

.navbar-menu a {
    text-decoration: none;
    color: #333;
}

.navbar-menu a.router-link-active {
    font-weight: 700;
}

.content {
    padding-top: 30px;
}
</style>