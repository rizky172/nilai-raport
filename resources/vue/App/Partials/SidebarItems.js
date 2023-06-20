// parent_id = 0 for header menu
// parent_id = digit number for submenu
// parent_id = '' single menu
export default [
    {
        id: 1,
        parent_id: 0,
        header: 'TRANSAKSI',
    },
    {
        id: 16,
        parent_id: 1,
        label: 'Nilai Raport',
        name: 'report-value',
        icon: 'fa-address-book',
        permission: ['report_value_read']
    },
    {
        id: 4,
        parent_id: 0,
        header: 'LAPORAN',
    },
    {
        id: 17,
        parent_id: 4,
        label: 'Nilai Raport',
        name: 'report-report-value',
        icon: 'fa-chart-bar',
        permission: ['report_report_value_read']
    },
    {
        id: 2,
        parent_id: 0,
        header: 'MASTER DATA',
    },
    {
        id: 11,
        parent_id: 2,
        label: 'Guru',
        name: 'teacher',
        icon: 'fa-address-book',
        permission: ['teacher_read']
    },
    {
        id: 12,
        parent_id: 2,
        label: 'Siswa',
        name: 'student',
        icon: 'fa-address-book',
        permission: ['student_read']
    },
    {
        id: 13,
        parent_id: 2,
        label: 'Admin',
        name: 'admin',
        icon: 'fa-users',
        permission: ['admin_read']
    },
    {
        id: 14,
        parent_id: 2,
        label: 'List Kategori',
        name: 'list-category',
        icon: 'fa-list',
        permission: ['list_category_read']
    },
    {
        id: 15,
        parent_id: 2,
        label: 'Hak Akses',
        name: 'hak-akses',
        icon: '',
        permission: ['access_read']
    },
    {
        id: 3,
        parent_id: 0,
        header: 'Lain - Lain',
    },
    {
        id: 50,
        parent_id: 3,
        label: 'Pengaturan',
        name: 'config',
        icon: 'fa-cogs',
        permission: ['setting_read']
    },
    {
        id: 51,
        parent_id: 3,
        label: 'Log',
        name: 'log',
        icon: 'fa-history',
        permission: ['setting_read']
    },
    {
        id: 52,
        parent_id: '',
        label: 'Logout',
        name: 'logout',
        icon: 'fa-sign-out-alt'
    }
];