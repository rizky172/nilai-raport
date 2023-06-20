export default function Report(vm) {

    this.ReportValue = {
        all: function(params) {
            var url = '/api/report/report-value/list?' + $.param(params);

            return vm.get(url);
        },
    };

};