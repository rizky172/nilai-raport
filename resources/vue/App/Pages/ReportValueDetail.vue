<script>
export default {
    data: function() {
        return {
            data: {},
            options: {
                teacher: [],
                class: [],
                major: [],
                lesson: [],
                semester: undefined
            },
            isShow: false
        };
    },
    mounted: function() {
      var vm = this;

      vm.load();
    },
    watch: {
        $route (to, from){
          var vm = this;

          vm.load();
        }
    },
    methods: {
        load: function() {
            var vm = this;

            if (vm.$route.params.id) {
                vm.create();
            } else {
                vm.createEmptyData();
            }

            vm.loadCategory();
            vm.loadTeacher();
        },
        createEmptyData: function() {
            var vm = this;

            var data = {
                id: null,
                teacher_id: null,
                major_id: null,
                class_id: null,
                lesson_id: null,
                semester_id: null,
                ref_no: null,

                detail: []
            };

            vm.data = Object.assign({}, data);
        },
        create: async function() {
            var vm = this;

            vm.$loading.on('loading');

            var resp = await vm.$api.ReportValue.get(vm.$route.params.id);
            if (!resp.data.is_error) {
                var data = resp.data.data;

                for (var i = 0; i < data.detail.length; i++) {
                    data.detail[i]._hash = Math.getRandomHash();
                }

                vm.data = Object.assign({}, data);

                vm.isShow = true;
            } else {
                vm.$flash.error(resp.data.message);
            }

            vm.$loading.off('loading');
        },
        submit: async function() {
            var vm = this;

            vm.$loading.on('loading');

            var data = vm._createPostData(vm.data);
            var resp = await vm.$api.ReportValue.create(data);
            vm.$ac.refresh();

            if(!resp.data.is_error) {
                if (vm.$route.params.id) {
                    vm.create();
                } else {
                    vm.$router.push({ name: 'report-value-detail', params: { id: resp.data.data }});
                }

                vm.$flash.success(resp.data.message);
            } else {
                vm.$flash.error(resp.data.message);
            }

            vm.$loading.off('loading');
        },
        _createPostData: function(data) {
            var vm = this;

            var _data = Object.assign({}, data);

            return _data;
        },
        loadCategory: async function() {
            var vm = this;

            var resp = await vm.$api.Category.all({ page: 'all', group_by:'semester'});
            var data = resp.data.data;

            var category = _.map(data, function(row) {
                return {
                    id: row.id,
                    text: row.label,
                    group_by: row.group_by
                }
            });

            vm.options.semester = _.where(category, {group_by: 'semester'});
        },
        loadTeacher: async function() {
            var vm = this;

            var resp = await vm.$api.Teacher.all({ page: 'all'});
            var data = resp.data.data;

            vm.options.teacher = _.map(data, function(row) {
                return {
                    id: row.id,
                    text: row.name
                }
            });
        },
        changeTeacher: async function(){
            var vm = this;

            if(vm.data.teacher_id){
                vm.$loading.on('loading-teacher');

                    var resp = await vm.$api.Teacher.get(vm.data.teacher_id);

                    if (!resp.data.is_error) {
                        var data = resp.data.data;
                        
                        vm.options.class = _.map(data.class_id, function(row) {
                            return {
                                id: row.id,
                                text: row.label
                            }
                        });

                        vm.options.major = _.map(data.major_id, function(row) {
                            return {
                                id: row.id,
                                text: row.label
                            }
                        });

                        vm.options.lesson = _.map(data.lesson_id, function(row) {
                            return {
                                id: row.id,
                                text: row.label
                            }
                        });

                    } else {
                        vm.$flash.error(resp.data.message);
                    }

                vm.$loading.off('loading-teacher');
            }
        },
        getReportValue: async function() {
            var vm = this;

             vm.isShow = false;

            vm.$loading.on('loading');

                var params = {
                    teacher_id: vm.data.teacher_id,
                    class_id: vm.data.class_id,
                    major_id: vm.data.major_id
                };

                var resp = await vm.$api.ReportValue.getReportValue(params);
                var data = resp.data.data;

                if(!resp.data.is_error) {

                    var arrStudent = [];
                     _.each(vm.data.detail, function(x) {
                        arrStudent.push(x.student_id);
                    });

                    _.each(data, function(x) {
                        if(!arrStudent.includes(x.id)){
                            vm.addDetail(x.id, x.name);
                        }
                    });

                    vm.isShow = true;
                } else {
                    vm.$flash.error(resp.data.message);
                }

            vm.$loading.off('loading');
        },
        addDetail: async function(studentId, name) {
            var vm = this;

            var detail = {
                id: null,
                student_id: studentId,
                name: name,
                value: 0,

                _hash: Math.getRandomHash()
            };

            vm.data.detail.push(detail);
        },
    }
}
</script>

<template>
    <section class="content">
        <div class="container-fluid">
            <wv-row>
                <wv-col>
                    <wv-card title="Detail">
                        <form action="#" role="form" v-on:submit.prevent="getReportValue()">
                            <wv-row>
                                <wv-text
                                    label="No. Ref - AUTO"
                                    placeholder="Masukan Ref No"
                                    :col="6"
                                    :disabled="true"
                                    v-model="data.ref_no"></wv-text>
                                <wv-select2
                                    label="Guru"
                                    placeholder="Pilih Guru"
                                    :col="6"
                                    @change="changeTeacher()"
                                    :required="true"
                                    v-bind:options="options.teacher"
                                    v-model="data.teacher_id">
                                </wv-select2>
                                <wv-select2
                                    label="Kelas"
                                    placeholder="Pilih Kelas"
                                    :col="6"
                                    :required="true"
                                    v-bind:disabled="$loading.get('loading-teacher') || !data.teacher_id"
                                    v-bind:options="options.class"
                                    v-model="data.class_id">
                                </wv-select2>
                                <wv-select2
                                    label="Jurusan"
                                    placeholder="Pilih Jurusan"
                                    :col="6"
                                    :required="true"
                                    v-bind:disabled="$loading.get('loading-teacher') || !data.teacher_id"
                                    v-bind:options="options.major"
                                    v-model="data.major_id">
                                </wv-select2>
                                <wv-col>
                                    <div class="float-right">
                                        <wv-submit-button>Cari</wv-submit-button>
                                    </div>
                                </wv-col>
                            </wv-row>
                        </form>
                    </wv-card>
                </wv-col>

                <wv-col v-show="isShow">
                    <wv-card title="Nilai Raport" v-bind:loading="$loading.get('loading')">
                        <form action="#" role="form" v-on:submit.prevent="submit()">
                            <wv-row>
                                <wv-select2
                                    label="Mata Pelajaran"
                                    placeholder="Pilih Mata Pelajaran"
                                    :col="6"
                                    :required="true"
                                    v-bind:options="options.lesson"
                                    v-bind:disabled="$loading.get('loading-teacher') || !data.teacher_id"
                                    v-model="data.lesson_id">
                                </wv-select2>
                                <wv-select
                                    label="Semester"
                                    placeholder="Pilih Semester"
                                    :col="6"
                                    :required="true"
                                    v-bind:options="options.semester"
                                    v-model="data.semester_id">
                                </wv-select>
                                <wv-col class="mt-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10px">#</th>
                                                    <th class="text-center">Siswa</th>
                                                    <th class="text-center col-w-200">Nilai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(tr, $index) in data.detail">
                                                    <td>{{ parseInt($index) + 1 }}</td>
                                                    <td>{{ tr.name }}</td>
                                                    <td class="text-center">
                                                        <input type="number" class="form-control col-w-200" placeholder="Masukkan Nilai" v-model="tr.value" step="0.01">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </wv-col>
                                <wv-col>
                                    <div class="float-right">
                                        <wv-submit-button></wv-submit-button>
                                    </div>
                                </wv-col>
                            </wv-row>
                        </form>
                    </wv-card>
                </wv-col>
            </wv-row>
        </div>
    </section>
</template>
