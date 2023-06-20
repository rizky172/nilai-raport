<script>
export default {
    data: function() {
        return {
            data: {},
            options: {
                class: undefined,
                major: undefined
            },
            log: [],
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
        },
        createEmptyData: function() {
            var vm = this;

            var data = {
                id: null,
                major_id: null,
                class_id: null,
                ref_no: null,
                nis: null,
                name: null,
                email: null,
                phone: null,
                address: null,

                username: null,
                password: null,
                retype_password: null,
            };

            vm.data = Object.assign({}, data);
        },
        create: async function() {
            var vm = this;

            vm.loadLog();

            vm.$loading.on('loading');

            var resp = await vm.$api.Student.get(vm.$route.params.id);
            if (!resp.data.is_error) {
                var data = resp.data.data;

                vm.data = Object.assign({}, data);

            } else {
                vm.$flash.error(resp.data.message);
            }

            vm.$loading.off('loading');
        },
        submit: async function() {
            var vm = this;
            vm.$loading.on('loading');

            var data = vm._createPostData(vm.data);
            var resp = await vm.$api.Student.create(data);
            vm.$ac.refresh();

            if(!resp.data.is_error) {
                if (vm.$route.params.id) {
                    vm.create();
                } else {
                    vm.$router.push({ name: 'student-detail', params: { id: resp.data.data }});
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
        loadLog: async function() {
            var vm = this;

            var resp = await vm.$api.Log.all({fk_id: vm.$route.params.id, table_name: 'person'});

            if (!resp.data.is_error) {
                vm.log = resp.data.data;
            } else {
                vm.$flash.error(resp.data.message);
            }
        },
        loadCategory: async function() {
            var vm = this;

            var resp = await vm.$api.Category.all({ page: 'all', group_by:'major,class'});
            var data = resp.data.data;

            var category = _.map(data, function(row) {

                return {
                    id: row.id,
                    text: row.label,
                    group_by: row.group_by
                }
            });

            vm.options.major = _.where(category, {group_by: 'major'});
            vm.options.class = _.where(category, {group_by: 'class'});
        },
    }
}
</script>

<template>
    <section class="content">
        <div class="container-fluid">
            <wv-row>
                <wv-col>
                    <wv-tab-group :loading="$loading.get('loading')">
                        <wv-tab title="Detail">
                            <form action="#" role="form" v-on:submit.prevent="submit()">
                                <wv-row>
                                    <wv-text
                                        label="No. Ref - AUTO"
                                        placeholder="Masukan Ref No"
                                        :col="6"
                                        :disabled="true"
                                        v-model="data.ref_no"></wv-text>
                                    <wv-text
                                        type="number"
                                        label="NIS"
                                        placeholder="Masukan NIS"
                                        :col="6"
                                        v-bind:disabled="!$ac.hasAccess('student_full_access')"
                                        :required="true"
                                        v-model="data.nis"></wv-text>
                                    <wv-text
                                        label="Nama"
                                        placeholder="Masukan Nama"
                                        :col="4"
                                        :required="true"
                                        v-bind:disabled="!$ac.hasAccess('student_full_access')"
                                        v-model="data.name"></wv-text>
                                    <wv-text
                                        type="email"
                                        label="Email"
                                        placeholder="Masukan Email"
                                        :col="4"
                                        :required="true"
                                        v-bind:disabled="!$ac.hasAccess('student_full_access')"
                                        v-model="data.email"></wv-text>
                                    <wv-text
                                        label="No. Hp"
                                        placeholder="Masukan No. HP"
                                        :col="4"
                                        v-bind:disabled="!$ac.hasAccess('student_full_access')"
                                        v-model="data.phone"></wv-text>
                                    <wv-select2
                                        label="Kelas"
                                        placeholder="Pilih Kelas"
                                        :col="6"
                                        :required="true"
                                        v-bind:options="options.class"
                                        v-bind:disabled="!$ac.hasAccess('student_full_access')"
                                        v-model="data.class_id">
                                    </wv-select2>
                                    <wv-select2
                                        label="Jurusan"
                                        placeholder="Pilih Jurusan"
                                        :col="6"
                                        :required="true"
                                        v-bind:options="options.major"
                                        v-bind:disabled="!$ac.hasAccess('student_full_access')"
                                        v-model="data.major_id">
                                    </wv-select2>
                                    <wv-textarea
                                        label="Alamat"
                                        placeholder="Masukan Alamat"
                                        :col="12"
                                        v-model="data.notes"></wv-textarea>
                                    <wv-col>
                                        <div class="float-right">
                                            <wv-cancel-button v-on:click="load()"></wv-cancel-button>
                                            <wv-submit-button></wv-submit-button>
                                        </div>
                                    </wv-col>
                                </wv-row>
                            </form>
                        </wv-tab>
                        <wv-tab title="Informasi Login">
                            <form action="#" role="form" v-on:submit.prevent="submit()">
                                <wv-row>
                                    <wv-text
                                        label="Username"
                                        placeholder="Masukkan Username"
                                        v-bind:col="12"
                                        v-bind:required="true"
                                        v-model="data.username"></wv-text>
                                    <wv-text
                                        type="password"
                                        label="Password"
                                        v-bind:col="6"
                                        placeholder="Masukkan Password"
                                        v-model="data.password"></wv-text>
                                    <wv-text
                                        type="password"
                                        label="Ketik Ulang Password"
                                        placeholder="Masukkan Ulang Password"
                                        v-bind:col="6"
                                        v-model="data.retype_password"></wv-text>
                                    <wv-col>
                                        <div class="text-right">
                                            <wv-cancel-button v-on:click="load()"></wv-cancel-button>
                                            <wv-submit-button></wv-submit-button>
                                        </div>
                                    </wv-col>
                                </wv-row>
                            </form>
                        </wv-tab>
                        <wv-tab title="Log" v-bind:isShow="data.id ? true : false">
                            <form action="#" role="form" v-on:submit.prevent="submit()">
                                <wv-row>
                                    <wv-col class="mt-3">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 170px">Tanggal</th>
                                                        <th>Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr :key="index" v-for="(tr, index) in log">
                                                        <td>{{ tr.created }}</td>
                                                        <td><span v-html="tr.notes"></span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </wv-col>
                                </wv-row>
                            </form>
                        </wv-tab>
                    </wv-tab-group>
                </wv-col>
            </wv-row>
        </div>
    </section>
</template>
