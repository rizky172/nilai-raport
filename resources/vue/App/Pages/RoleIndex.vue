<script>
import MixinList from '../Mixins/MixinList.js';

export default {
  mixins: [MixinList],
  watch:{
      // Call when $route.query changed
      '$route.query': function() {
          var vm = this;

          this.load();
      }
  },
  methods: {
        // Redirect to correct url
        doSearch: function() {
            if(this.search.order_by)
                this.search.order_by = this.$orderBy.toString();

            // this.$store.dispatch('addSuccess', 'do search');
            this.$router.push({name: 'role', query: this.search});
        },
        // Search by submitting correct $search params
        _doSearch: function() {
            var vm = this;

            var postData = Object.assign({}, this.search);
            postData.order_by = this.$orderBy.toObject();

            var search = Object.assign({}, vm.search);
            search.page = 'all';
            search.group_by = 'department';

            vm.$loading.on('loading');
            vm.$api.Category.all(search).then(function(resp) {
              if (!resp.data.is_error) {
                var data = resp.data;

                vm.list = Object.assign({}, data.data);

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

              vm.$loading.off('loading');
            });
        },
        destroy: function(id) {
            var vm = this;

            vm.$loading.on('loading-delete-' + id);
            vm.$api.Category.delete(id).then(function(resp) {
                if(!resp.data.is_error) {
                    vm._doSearch();

                    vm.$flash.success(resp.data.message);
                } else {
                    vm.$flash.error(resp.data.message);
                }

                vm.$loading.off('loading-delete-' + id);
            });
        },
        clear: function() {
          var vm = this;
          vm.search = {};

          vm.doSearch();
        },
        goto: function(page) {
            this.search.page = page;
            this.doSearch();
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
              <box-overlay v-if="$loading.get('loading')" />
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-12">
                      <router-link v-if="$ac.hasAccess('category_role_create')" class="btn btn-primary" :to="{name: 'role-create'}"><i class="fas fa-plus"></i> Tambah</router-link>
                    </div>
                    <div class="col-sm-12 mt-3">
                        <div class="table-responsive">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th style="width: 10px">#</th>
                            <th>Nama</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(tr, $index) in list">
                            <td>{{ parseInt($index) + 1 }}</td>
                            <td>{{ tr.label }}</td>
                            <td>
                              <router-link v-if="$ac.hasAccess('category_role_create')" title="Ubah" class="btn btn-warning" :to="{name: 'role-detail', params: {id: tr.id}}"><i class="fas fa-pencil-alt"></i></router-link>
                              <button-confirm v-if="$ac.hasAccess('category_role_delete')" title="Hapus"
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
            </div>
          </div>
        </div>
      </div>
    </section>
</template>
