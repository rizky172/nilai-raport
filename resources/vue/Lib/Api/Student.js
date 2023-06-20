export default function Student(vm) {
    this.all = function(params) {
        var url = '/api/student/list?' + $.param(params);

        return vm.get(url);
    };

    this.create = function(params) {
        var url = '/api/student/create';

        return vm.post(url, params);
    };

    this.get = function(id) {
        var url = '/api/student/detail/' + id;

        return vm.get(url);
    };

    this.delete = function(params) {
        var url = null;

        if (params.permanent != null)
            url = '/api/student/' + params.id + '/' + params.permanent;
        else
            url = '/api/student/' + params.id;

        return vm.delete(url);
    };

    this.restore = function(id){
        var url = '/api/student/restore/' + id;

        return vm.get(url);
    };

    this.export = function(params) {
        var url = '/api/student/export?' + $.param(params);

        return vm.get(url);
    };

    this.import = function(params) {
        var url = '/api/student/import';

        return vm.upload(url, params);
    };
};