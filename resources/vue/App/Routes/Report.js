// Pages
import ReportReportValueIndex from '../Pages/ReportReportValueIndex.vue';

export default [
	{
        name: 'report-report-value',
        path: 'report/report-value',
        component: ReportReportValueIndex,
        meta: {
            breadcrumb: [
                { title: 'Home', url: '/' },
                { title: null, active: true }
            ],
            pageTitle: 'Laporan Nilai Raport'
        }
    },
]