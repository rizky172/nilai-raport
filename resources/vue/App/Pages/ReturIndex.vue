<script>
import SidebarDetail from './Sidebar/SidebarReturDetail.vue';

export default {
    data: function() {
        return {
            // For search parameter
            search: {},
            // For list page
            list: {},
            // For pagination
            paginator: {},
            // For order by
            order_by: {},
            sidebarDetail: SidebarDetail,
            data: {}
        }
    },
    created: function() {
        this.$sidebar.setComponent(SidebarDetail);

        if(this.$route) {
            this.order_by = this.$route.query.order_by;
            // console.log('created', this.$route.query.order_by);
        }

        // console.log('mixin list created', this.$route.query);

        this.load();
    },
    watch:{
        // Call when $route.query changed
        '$route.query': function() {
            var vm = this;

            this.load();
        }
    },
    methods: {
        createEmptyData: function() {
            var vm = this;

            var data = {
                id: null,
                ref_no: null,
                ref_no_notes: null,
                person_id: null,
                item_id: null,
                unit_id: null,
                qty: 1,
                created: vm.$df.iso(new Date()),
                is_solved: 0,
                is_closed: 0,
                unit: '?'
            };

            vm.data = Object.assign({}, data);
        },
        // Similar with init
        load: function() {
            console.log('mixin load');
            if(!this.$route) return;

            var orderBy = null;
            // Prepare search query
            this.search = Object.assign({}, this.$route.query);
            console.log('mixin list loaded', this.search, this.$route.query);

            // Reset order by object
            this.$orderBy.reset(this.search.order_by);
            // Get order by object for API
            orderBy = this.$orderBy.toObject();
            // Delete to clean parameter for API
            delete this.search.order_by;
            // If order by is not empty
            if(orderBy)
                this.search.order_by = orderBy;

            console.log('mixin load', this.search);

            // console.log('customer.vue load');
            this._doSearch();
            this.createEmptyData();
        },
        sortBy: function(columnName) {
            this.$orderBy.setColumn(columnName);

            // Generate search params
            this.search.order_by = this.$orderBy.toString();
            console.log('sort by', this.search);
            this.doSearch();
        },
        // Redirect to correct url
        doSearch: function() {
            if(this.search.order_by)
                this.search.order_by = this.$orderBy.toString();

            // this.$store.dispatch('addSuccess', 'do search');
            this.$router.push({name: 'retur', query: this.search});
        },
        // Search by submitting correct $search params
        // Make this function async. So caller would not wait return immedietly
        _doSearch: async function() {
            var vm = this;

            var postData = Object.assign({}, this.search);
            postData.order_by = this.$orderBy.toObject();

            // Show loading
            vm.$loading.on('loading');

            // Wait until response send
            // resp is just object from .then(function(resp));
            var resp = await vm.$api.Retur.all(vm.search);

            // Use just regular ajax call
            if (!resp.data.is_error) {
              var data = resp.data;

              vm.list = Object.assign({}, _.map(data.data, function(x) {
                  x.is_show = false;
                  x.detail = [];

                  return x;
              }));

              if(parseInt(vm.search.page) > data._meta.last_page){
                  // Redirect to last_page
                  vm.search.page = data._meta.last_page;
                  vm.doSearch();
              }

              // Paginator
              vm.paginator = data._meta;
            } else {
              vm.$flash.error(resp.data.message);
            }

            // Hide loading
            vm.$loading.off('loading');
        },
        destroy: async function(id) {
            var vm = this;

            vm.$loading.on('loading-delete-' + id);

            var resp = await vm.$api.Retur.delete(id);

            if(!resp.data.is_error) {
                vm._doSearch();

                vm.$flash.success(resp.data.message);
            } else {
                vm.$flash.error(resp.data.message);
            }

            vm.$loading.off('loading-delete-' + id);
        },
        clear: function() {
          var vm = this;
          vm.search = {};

          vm.doSearch();
        },
        goto: function(page) {
            this.search.page = page;
            this.doSearch();
        },
        submit: function(data) {
            var vm = this;

            vm.$loading.on('loading');

            vm.$api.Retur.create(data).then(function(resp) {
                if(!resp.data.is_error) {
                    vm._doSearch();

                    vm.$flash.success(resp.data.message);
                } else {
                    vm.$flash.error(resp.data.message);
                }

                vm.$loading.off('loading');
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
                      <sidebar-button class="btn btn-primary" 
                        v-bind:sidebar="sidebarDetail"
                        v-model="data"
                        v-bind:success="submit"><i class="fas fa-plus"></i> Tambah</sidebar-button>
                    </div>
                    <div class="col-sm-12 mt-3">
                        <div class="table-responsive">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th style="width: 10px">#</th>
                            <th>No. Ref</th>
                            <th style="width: 100px">Supplier</th>
                            <th>Item</th>
                            <th>Unit</th>
                            <th class="text-right">Qty</th>
                            <th class="clickable" @click="sortBy('created')">Dibuat Tanggal <sortable v-bind:order="$orderBy.getClass('created')" /></th>
                            <th style="width: 150px">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(tr, $index) in list">
                              <td>{{ parseInt($index) + 1 }}</td>
                              <td>{{ tr.ref_no }}</td>
                              <td>{{ tr.supplier }}</td>
                              <td>{{ tr.item }}</td>
                              <td>{{ tr.unit }}</td>
                              <td class="text-right">{{ tr.qty }}</td>
                              <td>{{ tr.created }}</td>
                              <td>
                                <sidebar-button class="btn-warning" title="Ubah"
                                    v-bind:sidebar="sidebarDetail"
                                    v-model="list[$index]"
                                    v-bind:success="submit"><i class="fas fa-pencil-alt"></i></sidebar-button>

                                <button-confirm title="Hapus"
                                    @click="destroy(tr.id)"
                                    v-bind:disabled="$loading.get('loading-delete-' + tr.id)"
                                    v-bind:message="$confirm.delete">
                                </button-confirm>
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
