export default function Person(vm) {
    this.all = function(params) {
        var url = '/api/person/list?' + $.param(params);

        return vm.get(url);
    };
};