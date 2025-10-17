import { createRouter, createWebHistory } from "vue-router";
import Landing from "../views/Landing.vue";
import Auth from "../views/Auth.vue";
import Dashboard from "../views/Dashboard.vue";
import Empresas from "../views/Empresas.vue";
import Categorias from "../views/Categorias.vue";
import Productos from "../views/Productos.vue";
import Inventario from "../views/Inventario.vue";
import Reportes from "../views/Reportes.vue";
import Usuarios from "../views/Usuarios.vue";
import Configuracion from "../views/Configuracion.vue";
import NotFound from "../views/NotFound.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: "/", name: "landing", component: Landing },
    { path: "/auth", name: "auth", component: Auth },

    { path: "/dashboard", name: "dashboard", component: Dashboard, meta: { requiresAuth: true } },
    { path: "/empresas", name: "empresas", component: Empresas, meta: { requiresAuth: true } },
    { path: "/categorias", name: "categorias", component: Categorias, meta: { requiresAuth: true } },
    { path: "/productos", name: "productos", component: Productos, meta: { requiresAuth: true } },
    { path: "/inventario", name: "inventario", component: Inventario, meta: { requiresAuth: true } },
    { path: "/reportes", name: "reportes", component: Reportes, meta: { requiresAuth: true } },
    { path: "/usuarios", name: "usuarios", component: Usuarios, meta: { requiresAuth: true } },
    { path: "/configuracion", name: "configuracion", component: Configuracion, meta: { requiresAuth: true } },

    { path: "/:pathMatch(.*)*", name: "not-found", component: NotFound },
  ],
  scrollBehavior: (to, from, savedPosition) => {
    return { top: 0 };
  },
});

export default router;

// Simple auth guard based on localStorage token
router.beforeEach((to, from, next) => {
  if (to.meta?.requiresAuth) {
    let token = '';
    try { token = localStorage.getItem('onoxfri_token') || ''; } catch {}
    if (!token) return next({ name: 'auth' });
  }
  next();
});
