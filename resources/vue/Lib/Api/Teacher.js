export default function Teacher(vm) {
    this.all = function(params) {
        var url = '/api/teacher/list?' + $.param(params);

        return vm.get(url);
    };

    this.create = function(params) {
        var url = '/api/teacher/create';

        return vm.post(url, params);
    };

    this.get = function(id) {
        var url = '/api/teacher/detail/' + id;

        return vm.get(url);
    };

    this.delete = function(params) {
        var url = null;

        if (params.permanent != null)
            url = '/api/teacher/' + params.id + '/' + params.permanent;
        else
            url = '/api/teacher/' + params.id;

        return vm.delete(url);
    };

    this.restore = function(id){
        var url = '/api/teacher/restore/' + id;

        return vm.get(url);
    };
};