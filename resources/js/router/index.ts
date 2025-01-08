import {
    createRouter,
    createWebHistory,
    type RouteRecordRaw,
} from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useConfigStore } from "@/stores/config";
import NProgress from "nprogress";
import "nprogress/nprogress.css";

declare module "vue-router" {
    interface RouteMeta {
        pageTitle?: string;
        permission?: string;
    }
}

const routes: Array<RouteRecordRaw> = [
    {
        path: "/",
        name: "landing",
        component: () => import("@/landing/Index.vue"),
        meta: {
            pageTitle: "Landing Page",
        },
    },
    {
        path: "/riwayat-konser",
        name: "riwayat-konser",
        component: () => import("@/landing/riwayat-konser.vue"),
        meta: {
            pageTitle: "Riwayat Konser",
        },
    },
    {
        path: "/kontak",
        name: "kontak",
        component: () => import("@/landing/kontak.vue"),
        meta: {
            pageTitle: "Kontak",
        },
    },
    {
        path: "/detail-konser/:uuid",
        name: "detail-konser",
        component: () => import("@/landing/detail-konser.vue"),
        meta: {
            pageTitle: "Detail Konser",
        },
    },
    {
        path: "/dashboard",
        redirect: "/dashboard",
        component: () => import("@/layouts/default-layout/DefaultLayout.vue"),
        meta: {
            middleware: "auth",
        },
        children: [
            {
                path: "/dashboard",
                name: "dashboard",
                component: () => import("@/pages/dashboard/Index.vue"),
                meta: {

                },
            },
            {
                path: "/dashboard/profile",
                name: "dashboard.profile",
                component: () => import("@/pages/dashboard/profile/Index.vue"),
                meta: {
                    pageTitle: "Profile",
                    breadcrumbs: ["Profile"],
                },
            },
            {
                path: "/dashboard/managementkonser",
                name: "dashboard.managementkonser",
                component: () => import("@/pages/dashboard/managementkonser/Index.vue"),
                meta: {
                    pageTitle: "Management Konser",
                    breadcrumbs: ["Management Konser"],
                },
            },
            {
                path: "/dashboard/managementtiket",
                name: "dashboard.managementtiket",
                component: () => import("@/pages/dashboard/managementtiket/Index.vue"),
                meta: {
                    pageTitle: "Management Tiket",
                    breadcrumbs: ["Management Tiket"],
                },
            },
            {
                path: "/dashboard/pembayaran",
                name: "dashboard.pembayaran",
                component: () => import("@/pages/dashboard/pembayaran/Index.vue"),
                meta: {
                    pageTitle: "Pembayaran",
                    breadcrumbs: ["Pembayaran"],
                },
            },
            {
                path: "/dashboard/laporananalitic",
                name: "dashboard.laporananalitic",
                component: () => import("@/pages/dashboard/laporananalitic/Index.vue"),
                meta: {
                    pageTitle: "Laporan & Analitic",
                    breadcrumbs: ["Laporan & Analitic"],
                },
            },
            {
                path: "/dashboard/pengguna",
                name: "dashboard.pengguna",
                component: () => import("@/pages/dashboard/pengguna/Index.vue"),
                meta: {
                    pageTitle: "Pengguna",
                    breadcrumbs: ["Pengguna"],
                },
            },
            {
                path: "/dashboard/setting",
                name: "dashboard.setting",
                component: () => import("@/pages/dashboard/setting/Index.vue"),
                meta: {
                    pageTitle: "Website Setting",
                    breadcrumbs: ["Website", "Setting"],
                },
            },

            // MASTER
            {
                path: "/dashboard/master/users/roles",
                name: "dashboard.master.users.roles",
                component: () =>
                    import("@/pages/dashboard/master/users/roles/Index.vue"),
                meta: {
                    pageTitle: "User Roles",
                    breadcrumbs: ["Master", "Users", "Roles"],
                },
            },
            {
                path: "/dashboard/master/users",
                name: "dashboard.master.users",
                component: () =>
                    import("@/pages/dashboard/master/users/Index.vue"),
                meta: {
                    pageTitle: "Users",
                    breadcrumbs: ["Master", "Users"],
                },
            },
        ],
    },
    {
        path: "/",
        component: () => import("@/layouts/AuthLayout.vue"),
        children: [
            {
                path: "/sign-in",
                name: "sign-in",
                component: () => import("@/pages/auth/sign-in/Index.vue"),
                meta: {
                    pageTitle: "Sign In",
                    middleware: "guest",
                },
            },
            {
                path: "/sign-up",
                name: "sign-up",
                component: () => import("@/pages/auth/sign-up/Index.vue"),
                meta: {
                    pageTitle: "Sign Up",
                    middleware: "guest",
                },
            },
        ],
    },
    {
        path: "/",
        component: () => import("@/layouts/SystemLayout.vue"),
        children: [
            {
                // the 404 route, when none of the above matches
                path: "/404",
                name: "404",
                component: () => import("@/pages/errors/Error404.vue"),
                meta: {
                    pageTitle: "Error 404",
                },
            },
            {
                path: "/500",
                name: "500",
                component: () => import("@/pages/errors/Error500.vue"),
                meta: {
                    pageTitle: "Error 500",
                },
            },
        ],
    },
    {
        path: "/:pathMatch(.*)*",
        redirect: "/404",
    },
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
    scrollBehavior(to) {
        // If the route has a hash, scroll to the section with the specified ID; otherwise, scroll to the top of the page.
        if (to.hash) {
            return {
                el: to.hash,
                top: 80,
                behavior: "smooth",
            };
        } else {
            return {
                top: 0,
                left: 0,
                behavior: "smooth",
            };
        }
    },
});

router.beforeEach(async (to, from, next) => {
    if (to.name) {
        // Start the route progress bar.
        NProgress.start();
    }

    const authStore = useAuthStore();
    const configStore = useConfigStore();

    // current page view title
    if (to.meta.pageTitle) {
        document.title = `${to.meta.pageTitle} - ${import.meta.env.VITE_APP_NAME
            }`;
    } else {
        document.title = import.meta.env.VITE_APP_NAME as string;
    }

    // reset config to initial state
    configStore.resetLayoutConfig();

    // verify auth token before each page change
    if (!authStore.isAuthenticated) await authStore.verifyAuth();

    // before page access check if page requires authentication
    if (to.meta.middleware == "auth") {
        if (authStore.isAuthenticated) {
            if (
                to.meta.permission &&
                !authStore.user.permission.includes(to.meta.permission)
            ) {
                next({ name: "404" });
            } else if (to.meta.checkDetail == false) {
                next();
            }

            next();
        } else {
            next({ name: "sign-in" });
        }
    } else if (to.meta.middleware == "guest" && authStore.isAuthenticated) {
        next({ name: "dashboard" });
    } else {
        next();
    }
});

router.afterEach(() => {
    // Complete the animation of the route progress bar.
    NProgress.done();
});

export default router;
