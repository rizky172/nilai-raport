<script>
import MixinList from '../Mixins/MixinList.js';

export default {
    mixins: [MixinList],
    data: function() {
        return {
            routeName: 'report-report-value',
            options: {
                student: [],
            },
            student: []
        }
    },
    mounted: function() {
        var vm = this;


    if (!vm.search.student_id)
        vm.search.student_id = "";

        vm.loadStudent();
    },
    methods: {
        // ==========
        // Overide from mixin
        _search: async function() {
            var vm = this;

            var resp = await this.$api.Report.ReportValue.all(this.search);
            vm.getStudent();

            return resp;    
        },
        // ==========
        loadStudent: async function() {
            var vm = this;

            var resp = await vm.$api.Student.all({ page: 'all'});
            var data = resp.data.data;

            vm.options.student = _.map(data, function(row) {
                return {
                    id: row.id,
                    text: row.text
                }
            });
        },
        getStudent: async function(){
            var vm = this;

            if(vm.search.student_id){
                vm.$loading.on('loading');

                    var resp = await vm.$api.Student.get(vm.search.student_id);

                    if (!resp.data.is_error) {
                        var data = resp.data.data;
                    } else {
                        vm.$flash.error(resp.data.message);
                    }

                vm.$loading.off('loading');
            }
        },
    }
}
</script>

<template>
    <section class="content">
        <div class="container-fluid">
            <wv-row>
                <wv-col>
                    <wv-card title="Pencarian">
                        <form action="#" role="form" v-on:submit.prevent="doSearch()">
                            <wv-row>
                                <wv-select2
                                    label="Siswa"
                                    placeholder="Pilih Siswa"
                                    :col="12"
                                    v-bind:options="options.student"
                                    v-model="search.student_id">
                                </wv-select2>
                                <wv-col class="text-right">
                                    <wv-cancel-button v-on:click="clear()"></wv-cancel-button>
                                    <wv-submit-button>Cari</wv-submit-button>
                                </wv-col>
                            </wv-row>
                        </form>
                    </wv-card>
                </wv-col>

                <wv-col v-show="list.length > 0">
                    <wv-card v-bind:loading="$loading.get('loading')">
                        <wv-row>
                            <wv-col class="mt-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px">#</th>
                                                <th>Mata Pelajaran</th>
                                                <th>Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(tr, $index) in list" :key="$index">
                                                <td>{{ parseInt($index) + 1 }}</td>
                                                <td>{{ tr.lesson }}</td>
                                                <td>{{ tr.value }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </wv-col>
                        </wv-row>
                    </wv-card>
                </wv-col>
            </wv-row>
        </div>
    </section>
</template>
