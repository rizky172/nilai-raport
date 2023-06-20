<script>
import MixinList from '../Mixins/MixinList.js';

export default {
    mixins: [MixinList],
    data: function() {
        return {
            routeName: 'report-value',
        }
    },
    mounted: function() {
        var vm = this;
    },
    methods: {
        // ==========
        // Overide from mixin
        _search: async function() {
            return await this.$api.ReportValue.all(this.search);
        },
        _delete: async function(params) {
            return await this.$api.ReportValue.delete(params)
        },
        // ==========
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
                                <wv-text
                                    label="Kata Kunci"
                                    placeholder="Masukan Kata Kunci"
                                    v-bind:col="12"
                                    v-model="search.keyword"></wv-text>
                                <wv-col class="text-right">
                                    <wv-cancel-button v-on:click="clear()"></wv-cancel-button>
                                    <wv-submit-button>Cari</wv-submit-button>
                                </wv-col>
                            </wv-row>
                        </form>
                    </wv-card>
                </wv-col>

                <wv-col>
                    <wv-card v-bind:loading="$loading.get('loading')">
                        <wv-row>
                            <wv-col>
                                <router-link
                                    class="btn btn-primary"
                                    :to="{name: 'report-value-create'}"
                                    v-if="$ac.hasAccess('report_value_create')">
                                    <i class="fas fa-plus"></i> Tambah</router-link>
                            </wv-col>
                            <wv-col class="mt-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px">#</th>
                                                <th>No Ref</th>
                                                <th>Guru</th>
                                                <th>Kelas</th>
                                                <th>Jurusan</th>
                                                <th>Mata Pelajaran</th>
                                                <th>Semester</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(tr, $index) in list" :key="$index">
                                                <td>{{ parseInt($index) + 1 }}</td>
                                                <td>{{ tr.ref_no }}</td>
                                                <td>{{ tr.teacher }}</td>
                                                <td>{{ tr.class }}</td>
                                                <td>{{ tr.major }}</td>
                                                <td>{{ tr.lesson }}</td>
                                                <td>{{ tr.semester }}</td>
                                                <td>
                                                    <router-link
                                                        v-if="$ac.hasAccess('report_value_read')"
                                                        title="Ubah"
                                                        class="btn btn-warning"
                                                        :to="{name: 'report-value-detail', params: {id: tr.id}}">
                                                        <i class="fas fa-pencil-alt"></i></router-link>
                                                    <button-confirm
                                                        v-if="$ac.hasAccess('report_value_delete')"
                                                        title="Hapus"
                                                        @click="destroy(tr.id)"
                                                        v-bind:disabled="$loading.get('loading-delete-' + tr.id)"
                                                        v-bind:message="$confirm.delete">
                                                    </button-confirm>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </wv-col>
                            <wv-col>
                                <paging v-bind:model="paginator" v-on:goto="goto"/>
                            </wv-col>
                        </wv-row>
                    </wv-card>
                </wv-col>
            </wv-row>
        </div>
    </section>
</template>
