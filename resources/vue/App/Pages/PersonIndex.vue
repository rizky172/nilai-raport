<script>
import MixinList from '../Mixins/MixinList.js';

export default {
  mixins: [MixinList],
  data: function() {
      return {
          routeName: 'person'
      }
  },
  watch:{
      // Call when $route.query changed
      '$route.query': function() {
          var vm = this;

          this.onRouteChange();
      }
  },
  created: function() {
      this.onRouteChange();
  },
  methods: {
        // ==========
        // Overide from mixin
        _search: async function() {
            return await this.$api.Person.all(this.search);
        },
        _delete: async function(params) {
            return await this.$api.Person.delete(params)
        },
        _restore: async function(id) {
            return await this.$api.Person.restore(id);
        },
        // ==========

        onRouteChange: function() {
            var category = this.$route.query.category;
            this.$routerMetaHandler.updateTitle(category == 'supplier' ? 'Supplier' : (category == 'customer' ? 'Customer' : 'Employee'));
        },
        clear: function() {
          var vm = this;
          vm.search = {
              category: vm.search.category
          };

          vm.doSearch();
        },
        exportXls: function() {
            var vm = this;

            vm.$loading.on('loading-export');
            vm.$api.Person.export(vm.search).then(function(resp) {
                if(!resp.data.is_error) {
                    var data = resp.data;

                    if(!data.is_error)
                        window.open(data.data.url);

                } else {
                    vm.$flash.error(resp.data.message);
                }

                vm.$loading.off('loading-export');
            });
        },
        importXls: function() {
            var el = this.$refs.fileInput;

            // Trigger element to open upload dialog
            el.click();
        },
        handleFileChange: function(e) {
            var vm = this;
            var file = e.target.files[0];
            var el = this.$refs.fileInput;
            var category = this.$route.query.category;

            // Reset to null again
            el.value = '';

            vm.$loading.on('loading-import');
            vm.$api.Person.import({file: file, category}).then(function(resp) {
                if(!resp.data.is_error) {
                    var data = resp.data;

                    if(!data.is_error)
                        vm._doSearch();

                    vm.$flash.success(resp.data.message);
                } else {
                    vm.$flash.error(resp.data.message);
                }

                vm.$loading.off('loading-import');
            });
        }
    }
}
</script>

<template>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card card-default">
              <div class="card-header">
                <h3 class="card-title">Pencarian</h3>
              </div>
              <form action="#" role="form" v-on:submit.prevent="doSearch()">
                <div class="card-body">
                  <div class="row">
                    <div class="form-group col-sm-12">
                      <label>Kata Kunci</label>
                      <input type="text" class="form-control" id="keyword" placeholder="Masukkan Kata Kunci" v-model="search.keyword"/>
                    </div>
                  </div>
                </div>
                <div class="card-footer text-right">
                  <button type="button" class="btn btn-default" @click="clear">Batal</button>
                  <button type="submit" class="btn btn-primary">Cari</button>
                </div>
              </form>
            </div>
          </div>

          <div class="col-md-12">
            <div class="card card-default">
              <box-overlay v-if="$loading.get('loading')" />
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-12">
                      <template v-if="$route.query.category == 'supplier' && $ac.hasAccess('supplier_create')">
                        <router-link class="btn btn-primary" :to="{name: 'person-create', query: {category: $route.query.category}}"><i class="fas fa-plus"></i> Tambah</router-link>
                      </template>
                      <template v-if="$route.query.category == 'customer' && $ac.hasAccess('customer_create')">
                        <router-link class="btn btn-primary" :to="{name: 'person-create', query: {category: $route.query.category}}"><i class="fas fa-plus"></i> Tambah</router-link>
                      </template>
                      <template v-if="$route.query.category == 'employee' && $ac.hasAccess('employee_create')">
                        <router-link class="btn btn-primary" :to="{name: 'person-create', query: {category: $route.query.category}}"><i class="fas fa-plus"></i> Tambah</router-link>
                      </template>
                      <button class="btn btn-primary" v-if="$ac.hasAccess('customer_create') && $route.query.category == 'customer' || $ac.hasAccess('supplier_create') && $route.query.category == 'supplier'" @click="exportXls" :disabled="$loading.get('loading-export')"><i class="fas fa-file-excel"></i> Export(xls) <spin v-if="$loading.get('loading-export')"></spin></button>
                      <button class="btn btn-primary" v-if="$ac.hasAccess('customer_create') && $route.query.category == 'customer' || $ac.hasAccess('supplier_create') && $route.query.category == 'supplier'" @click="importXls" :disabled="$loading.get('loading-import')"><i class="fas fa-file-excel"></i> Import(xls) <spin v-if="$loading.get('loading-import')"></spin></button>
                      <input type="file" style="display: none;" @change="handleFileChange" ref="fileInput">
                      <router-link class="btn btn-danger float-right" :to="{name: 'person', query: {page: 1, deleted: 1, category: search.category}}" v-if="search.deleted == 0 || search.deleted == null"><i class="fa fa-trash"></i> Sampah</router-link>
                      <router-link class="btn btn-success float-right" :to="{name: 'person', query: {page: 1, deleted: 0, category: search.category}}" v-if="search.deleted == 1"><i class="fa fa-list"></i> List</router-link>
                    </div>
                    <div class="col-sm-12 mt-3">
                        <div class="table-responsive">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th style="width: 10px">#</th>
                            <th>No Ref</th>
                            <th>Nama</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(tr, $index) in list">
                            <td>{{ parseInt($index) + 1 }}</td>
                            <td>{{ tr.ref_no }}</td>
                            <td>
                              {{ tr.company_name }} - {{ tr.name }}<br>
                              <span v-if="$route.query.category == 'customer'" class="small text-gray">Sales: {{ tr.sales }}</span>
                            </td>
                            <td>
                                <template v-if="$route.query.category == 'supplier'">
                                  <div v-if="search.deleted == 0 || search.deleted == null">
                                      <template v-if="$ac.hasAccess('supplier_read')">
                                        <router-link title="Ubah" class="btn btn-warning" :to="{name: 'person-detail', params: {id: tr.id}}"><i class="fas fa-pencil-alt"></i></router-link>
                                      </template>

                                      <template v-if="$ac.hasAccess('supplier_delete')">
                                        <button-confirm title="Hapus"
                                            @click="destroy(tr.id)"
                                            v-bind:disabled="$loading.get('loading-delete-' + tr.id)"
                                            v-bind:message="$confirm.delete">
                                        </button-confirm>
                                      </template>
                                  </div>
                                  <div v-if="search.deleted == 1">
                                    <template v-if="$ac.hasAccess('supplier_delete')">
                                        <button-confirm title="Dipulihkan"
                                            @click="restore(tr.id)"
                                            v-bind:disabled="$loading.get('loading-delete-' + tr.id)"
                                            v-bind:message="$confirm.restore"
                                            v-bind:icon="'fas fa-trash-restore'"
                                            v-bind:color="'btn-success'">
                                        </button-confirm>
                                        <button-confirm title="Hapus Permanen"
                                            @click="destroy(tr.id, 'permanent')"
                                            v-bind:disabled="$loading.get('loading-delete-' + tr.id)"
                                            v-bind:message="$confirm.deletePermanent">
                                        </button-confirm>
                                    </template>
                                  </div>
                                </template>

                                <template v-if="$route.query.category == 'customer'">
                                  <div v-if="search.deleted == 0 || search.deleted == null">
                                      <template v-if="$ac.hasAccess('customer_read')">
                                        <router-link title="Ubah" class="btn btn-warning" :to="{name: 'person-detail', params: {id: tr.id}}"><i class="fas fa-pencil-alt"></i></router-link>
                                      </template>

                                      <template v-if="$ac.hasAccess('customer_delete')">
                                        <button-confirm title="Hapus"
                                            @click="destroy(tr.id)"
                                            v-bind:disabled="$loading.get('loading-delete-' + tr.id)"
                                            v-bind:message="$confirm.delete">
                                        </button-confirm>
                                      </template>
                                  </div>
                                  <div v-if="search.deleted == 1">
                                    <template v-if="$ac.hasAccess('customer_delete')">
                                      <button-confirm title="Dipulihkan"
                                          @click="restore(tr.id)"
                                          v-bind:disabled="$loading.get('loading-delete-' + tr.id)"
                                          v-bind:message="$confirm.restore"
                                          v-bind:icon="'fas fa-trash-restore'"
                                          v-bind:color="'btn-success'">
                                      </button-confirm>
                                      <button-confirm title="Hapus Permanen"
                                          @click="destroy(tr.id, 'permanent')"
                                          v-bind:disabled="$loading.get('loading-delete-' + tr.id)"
                                          v-bind:message="$confirm.deletePermanent">
                                      </button-confirm>
                                    </template>
                                  </div>
                                </template>
                                <template v-if="$route.query.category == 'employee'">
                                  <div v-if="search.deleted == 0 || search.deleted == null">
                                      <template v-if="$ac.hasAccess('employee_create')">
                                        <router-link title="Ubah" class="btn btn-warning" :to="{name: 'person-detail', params: {id: tr.id}}"><i class="fas fa-pencil-alt"></i></router-link>
                                      </template>

                                      <template v-if="$ac.hasAccess('employee_delete')">
                                        <button-confirm title="Hapus"
                                            @click="destroy(tr.id)"
                                            v-bind:disabled="$loading.get('loading-delete-' + tr.id)"
                                            v-bind:message="$confirm.delete">
                                        </button-confirm>
                                      </template>
                                  </div>
                                  <div v-if="search.deleted == 1">
                                    <template v-if="$ac.hasAccess('employee_delete')">
                                      <button-confirm title="Dipulihkan"
                                          @click="restore(tr.id)"
                                          v-bind:disabled="$loading.get('loading-delete-' + tr.id)"
                                          v-bind:message="$confirm.restore"
                                          v-bind:icon="'fas fa-trash-restore'"
                                          v-bind:color="'btn-success'">
                                      </button-confirm>
                                      <button-confirm title="Hapus Permanen"
                                          @click="destroy(tr.id, 'permanent')"
                                          v-bind:disabled="$loading.get('loading-delete-' + tr.id)"
                                          v-bind:message="$confirm.deletePermanent">
                                      </button-confirm>
                                    </template>
                                  </div>
                                </template>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer clearfix">
                  <paging v-bind:model="paginator" v-on:goto="goto"/>
                </div>
            </div>
          </div>
        </div>
      </div>
    </section>
</template>
