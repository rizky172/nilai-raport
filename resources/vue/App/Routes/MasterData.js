// Pages
import AdminDetail from '../Pages/AdminDetail.vue';
import AdminIndex from '../Pages/AdminIndex.vue';
import CategoryIndex from '../Pages/CategoryIndex.vue';
import CategoryList from '../Pages/CategoryList.vue';
import HakAksesDetail from '../Pages/HakAksesDetail.vue';
import HakAksesIndex from '../Pages/HakAksesIndex.vue';

import StudentIndex from '../Pages/StudentIndex.vue';
import StudentDetail from '../Pages/StudentDetail.vue';
import TeacherIndex from '../Pages/TeacherIndex.vue';
import TeacherDetail from '../Pages/TeacherDetail.vue';
import ReportValueIndex from '../Pages/ReportValueIndex.vue';
import ReportValueDetail from '../Pages/ReportValueDetail.vue';

export default [
    {
        name: 'admin',
        path: 'account',
        component: AdminIndex,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: null, active: true }
            ],
            pageTitle: 'Admin'
        }
    },
    {
        name: 'admin-create',
        path: 'account/create',
        component: AdminDetail,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: 'Admin', url: '/account' },
                { title: null, active: true }
            ],
            pageTitle: 'Create Admin'
        }
    },
    {
        name: 'admin-detail',
        path: 'account/detail/:id',
        component: AdminDetail,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: 'Admin', url: '/account' },
                { title: null, active: true }
            ],
            pageTitle: 'Detail Admin'
        }
    },
    {
        name: 'hak-akses',
        path: 'hak-akses',
        component: HakAksesIndex,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: null, active: true }
            ],
            pageTitle: 'Hak Akses'
        }
    },
    {
        name: 'hak-akses-detail',
        path: 'hak-akses/detail/:id',
        component: HakAksesDetail,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: 'Hak Akses', url: '/hak-akses' },
                { title: null, active: true }
            ],
            pageTitle: 'Detail Hak Akses'
        }
    },
    {
        name: 'hak-akses-create',
        path: 'hak-akses/create',
        component: HakAksesDetail,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: 'Hak Akses', url: '/hak-akses' },
                { title: null, active: true }
            ],
            pageTitle: 'Create Hak Akses'
        }
    },
    {
        name: 'list-category',
        path: 'category',
        component: CategoryList,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: null, active: true }
            ],
            pageTitle: 'List Kategori'
        }
    },
    {
        name: 'lesson',
        path: 'category/lesson',
        component: CategoryIndex,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: 'List Kategori', url: '/category' },
                { title: null, active: true }
            ],
            pageTitle: 'Mata Pelajaran'
        }
    },
    {
        name: 'major',
        path: 'category/major',
        component: CategoryIndex,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: 'List Kategori', url: '/category' },
                { title: null, active: true }
            ],
            pageTitle: 'Jurusan'
        }
    },
    {
        name: 'class',
        path: 'category/class',
        component: CategoryIndex,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: 'List Kategori', url: '/category' },
                { title: null, active: true }
            ],
            pageTitle: 'Kelas'
        }
    },
    {
        name: 'student',
        path: 'student',
        component: StudentIndex,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: null, active: true }
            ],
            pageTitle: 'Siswa'
        }
    },
    {
        name: 'student-create',
        path: 'student/create',
        component: StudentDetail,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: 'Siswa', url: '/student' },
                { title: null, active: true }
            ],
            pageTitle: 'Buat Siswa'
        }
    },
    {
        name: 'student-detail',
        path: 'student/detail/:id',
        component: StudentDetail,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: 'Siswa', url: '/student' },
                { title: null, active: true }
            ],
            pageTitle: 'Detail Siswa'
        }
    },
    {
        name: 'teacher',
        path: 'teacher',
        component: TeacherIndex,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: null, active: true }
            ],
            pageTitle: 'Guru'
        }
    },
    {
        name: 'teacher-create',
        path: 'teacher/create',
        component: TeacherDetail,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: 'Guru', url: '/teacher' },
                { title: null, active: true }
            ],
            pageTitle: 'Buat Guru'
        }
    },
    {
        name: 'teacher-detail',
        path: 'teacher/detail/:id',
        component: TeacherDetail,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: 'Guru', url: '/teacher' },
                { title: null, active: true }
            ],
            pageTitle: 'Detail Guru'
        }
    },
    {
        name: 'report-value',
        path: 'report-value',
        component: ReportValueIndex,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: null, active: true }
            ],
            pageTitle: 'Nilai Raport'
        }
    },
    {
        name: 'report-value-create',
        path: 'report-value/create',
        component: ReportValueDetail,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: 'Nilai Raport', url: '/report-value' },
                { title: null, active: true }
            ],
            pageTitle: 'Buat Nilai Raport'
        }
    },
    {
        name: 'report-value-detail',
        path: 'report-value/detail/:id',
        component: ReportValueDetail,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: 'Nilai Raport', url: '/report-value' },
                { title: null, active: true }
            ],
            pageTitle: 'Detail Nilai Raport'
        }
    },
]