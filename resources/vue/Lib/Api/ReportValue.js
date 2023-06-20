export default function ReportValue(vm) {
    this.all = function(params) {
        var url = '/api/report-value/list?' + $.param(params);

        return vm.get(url);
    };

    this.create = function(params) {
        var url = '/api/report-value/create';

        return vm.post(url, params);
    };

    this.get = function(id) {
        var url = '/api/report-value/detail/' + id;

        return vm.get(url);
    };

    this.delete = function(params) {
        var url = null;

        url = '/api/report-value/' + params.id;

        return vm.delete(url);
    };

    this.getReportValue = function(params) {
        var url = '/api/report-value/get-report-value?' + $.param(params);

        return vm.get(url);
    };
};