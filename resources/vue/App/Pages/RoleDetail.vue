<script>
export default {
    data: function() {
        return {
            data: {}
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
            //this method for formatting data based on route to avoid undefined
            var vm = this;

            if (vm.$route.params.id) {
                vm.create();
            } else {
                vm.createEmptyData();
            }
        },
        createEmptyData: function() {
        var vm = this;

        var data = {
                id: null,
                name: null,
                label: null,
                notes: null,
                group_by: 'department'
            };

            vm.data = Object.assign({}, data);
        },
        create: function() {
        var vm = this;
            vm.createEmptyData();

            vm.$loading.on('loading');
            vm.$api.Category.get(vm.$route.params.id).then(function(resp) {
                if (!resp.data.is_error) {
                    var data = resp.data.data;

                    vm.data = Object.assign({}, data);

                } else {
                    vm.$flash.error(resp.data.message);
                }

                vm.$loading.off('loading');
            });
        },
        submit: function() {
            var vm = this;

            vm.$loading.on('loading');

            var data = vm._createPostData(vm.data);
            vm.$api.Category.create(data).then(function(resp) {
                if(!resp.data.is_error) {
                    if (vm.$route.params.id) {
                        vm.create();
                    } else {
                        vm.$router.push({ name: 'role-detail', params: { id: resp.data.data } });
                    }

                    vm.$flash.success(resp.data.message);
                } else {
                    vm.$flash.error(resp.data.message);
                }

                vm.$loading.off('loading');
            });
        },
        _createPostData: function(data) {
            var vm = this;

            return data;
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
              <div class="card-header">
                <h3 class="card-title">Role</h3>
              </div>
              <form action="#" role="form" v-on:submit.prevent="submit()">
                <div class="card-body">
                  <div class="row">
                    <div class="form-group col-sm-12">
                      <label>Index</label>
                      <input type="text" class="form-control" id="index" placeholder="Masukkan Index" v-model="data.name"/>
                      Harus unique dan huruf kecil semua. Pisahkan menggunakan '-'. Contoh finance, manager-logistic
                    </div>
                    <div class="form-group col-sm-12">
                      <label>Nama</label>
                      <input type="text" class="form-control" id="name" placeholder="Masukkan Nama Jabatan" v-model="data.label"/>
                    </div>
                    <div class="form-group col-sm-12">
                      <label>Notes</label>
                      <textarea class="form-control" id="notes" rows="3" placeholder="Masukkan Catatan ..." v-model="data.notes"></textarea>
                    </div>
                  </div>
                </div>
                <div class="card-footer text-right" v-if="$ac.hasAccess('category_role_create')">
                  <button type="button" class="btn btn-default" @click="load()">Batal</button>
                  <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
</template>
