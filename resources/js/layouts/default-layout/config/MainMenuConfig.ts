import type { MenuItem } from "@/layouts/default-layout/config/types";

const MainMenuConfig: Array<MenuItem> = [
    {
        pages: [
            {
                heading: "Dashboard",
                name: "dashboard",
                route: "/dashboard",
                keenthemesIcon: "element-11",
            },
        ],
    },

    // WEBSITE
    {
        heading: "PELAYANAN KONSER",
        route: "/dashboard/website",
        name: "website",
        pages: [
            // MASTER
            {
                heading: "Manajemen Konser",
                route: "/dashboard/managementkonser",
                name: "managementkonser",
                keenthemesIcon: "shop",
            },
            {
                heading: "Manajemen Tiket",
                route: "/dashboard/managementtiket",
                name: "managementtiket",
                keenthemesIcon: "cheque",
            },
            {
                heading: "Pembayaran",
                route: "/dashboard/pembayaran",
                name: "pembayaran",
                keenthemesIcon: "tag",
            },
            {
                heading: "Data Riwayat Transaksi",
                route: "/dashboard/riwayattransaksi",
                name: "riwayattransaksi",
                keenthemesIcon: "directbox-default",
            },
            {
                heading: "Laporan & Analitic",
                route: "/dashboard/laporananalitic",
                name: "laporananalitic",
                keenthemesIcon: "chart-simple",
            },
            
            {
                heading: "Pengguna",
                route: "/dashboard/pengguna",
                name: "pengguna",
                keenthemesIcon: "badge",
            },
        ],
    },

    {
        heading: "Website",
        route: "/dashboard/website",
        name: "website",
        pages: [
            // MASTER
            {
                sectionTitle: "Master",
                route: "/master",
                keenthemesIcon: "cube-3",
                name: "master",
                sub: [
                    {
                        sectionTitle: "User",
                        route: "/users",
                        name: "master-user",
                        sub: [
                            {
                                heading: "Role",
                                name: "master-role",
                                route: "/dashboard/master/users/roles",
                            },
                            {
                                heading: "User",
                                name: "master-user",
                                route: "/dashboard/master/users",
                            },
                        ],
                    },
                ],
            },
            {
                heading: "Setting",
                route: "/dashboard/setting",
                name: "setting",
                keenthemesIcon: "setting-2",
            },
            {
                heading: "Banner Konser",
                route: "/dashboard/bannerkonser",
                name: "bannerkonser",
                keenthemesIcon: "picture",
            },
        ],
    },
];

export default MainMenuConfig;
