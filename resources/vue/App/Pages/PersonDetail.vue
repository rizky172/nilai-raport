<script>
export default {
    data: function() {
        return {
            data: {},
            options: {
                bank: [],
                sales: undefined,
                pusat: undefined,
                coa: undefined,
                industri_category: undefined
            },
            log: []
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
          vm.onRouteChange();
        }
    },
    computed: {
        title: function() {
            var vm = this;
            var prefix = 'Detail';

            if(!vm.data || !vm.data.id) {
                prefix = 'Create';
            }

            if (this.$route.query.category) {
                return prefix + ' ' + (this.$route.query.category == 'supplier' ? 'Supplier' : (this.$route.query.category == 'customer' ? 'Customer' : 'Employee'));
            }

            if (this.data.id != null) {
                return prefix + ' ' + (this.data.category == 'supplier' ? 'Supplier' : (this.data.category == 'customer' ? 'Customer' : 'Employee'));
            }
        },
        category: function() {
            var vm = this;

            if (this.$route.query.category)
                return this.$route.query.category;

            if (this.data.id != null)
                return this.data.category;
        }
    },
    methods: {
        loadLog: async function() {
          var vm = this;

          var resp = await vm.$api.Log.all({fk_id: vm.$route.params.id, table_name: 'person'});

            if (!resp.data.is_error) {
                vm.log = resp.data.data;
            } else {
                vm.$flash.error(resp.data.message);
            }

        },
        onRouteChange: function() {
            var breadcrumb = this.$route.meta.breadcrumb[1];

            var title = null;
            var params = null;

            if (this.$route.query.category) {
                title = (this.$route.query.category == 'supplier' ? 'Supplier' : (this.$route.query.category == 'customer' ? 'Customer' : 'Employee'));
                params = (this.$route.query.category == 'supplier' ? { category: 'supplier' } : (this.$route.query.category == 'customer' ? { category: 'customer' } : { category: 'employee' }));
            }

            if (this.data.id != null) {
                title = (this.data.category == 'supplier' ? 'Supplier' : (this.data.category == 'customer' ? 'Customer' : 'Employee'));
                params = (this.data.category == 'supplier' ? { category: 'supplier' } : (this.data.category == 'customer' ? { category: 'customer' } : { category: 'employee' }));
            }

            breadcrumb.title = title;
            breadcrumb.params = params;

            console.log('breadcrumb', breadcrumb);
            // Update title
            this.$routerMetaHandler.updateTitle(this.title);
            this.$routerMetaHandler.updateBreadcrumbAt(1, breadcrumb);
            // Update breadcrum index 1
        },
        load: async function() {
            //this method for formatting data based on route to avoid undefined
            var vm = this;

            if (vm.$route.params.id) {
                await vm.create();
            } else {
                vm.createEmptyData();
            }

            vm.loadBank();
            vm.loadSales();
            vm.loadPusat();
            vm.loadCoas();
            vm.loadCategory();
        },
        loadSales: function() {
            var vm = this;

            vm.$api.Employee.all({ page: 'all', marketing_only: 1, include_id: vm.data.sales_id }).then(function(resp) {
              var category = resp.data.data;

              _.each(category, function(x, index) {
                    var list = { id: x.id, text: x.name };
                    category[index].text = x.company_name + ' - ' + x.name;
              });

              vm.options.sales = _.sortBy(category, 'text');
            });
        },
        loadPusat: function() {
            var vm = this;

            vm.$api.Customer.all({page: 'all'}).then(function(resp) {
              var category = resp.data.data;
              var list = [];

              _.each(category, function(x, index) {
                    if(category[index].id != vm.$route.params.id) {
                        category[index].text = x.company_name + ' - ' + x.name;
                        list.push(category[index]);
                    }
              });

              vm.options.pusat = _.sortBy(list, 'text');
            });
        },
        loadCategory: async function() {
            var vm = this;

            var resp = await vm.$api.Category.all({ page: 'all', group_by: 'person_industry' });
            var data = resp.data.data;

            var industries = _.map(data, function(row) {
                return {
                    id: row.id,
                    text: row.label
                };
            });

            vm.options.industri_category = industries;
        },
        createEmptyData: function() {
            var vm = this;

            var data = {
                id: null,
                name: null,
                company_name: null,
                billing_period: null,
                phones: [],
                fax: [],
                address: null,
                billing_address: null,
                sales_id: null,
                person_id: null,
                notes: null,
                category: vm.$route.query.category,
                is_ppn: 1,
                coa_id: undefined,

                industri_category_id: null,
                factory: null,
                collector_name: null,

                account: [],
                _delete_account: []
            };

            vm.data = Object.assign({}, data);
            vm.onRouteChange();
        },
        create: async function() {
            var vm = this;
            vm.createEmptyData();
            vm.loadLog();

            vm.$loading.on('loading');
            var resp = await vm.$api.Person.get(vm.$route.params.id);
            if (!resp.data.is_error) {
                var data = resp.data.data;

                // Add _hast in account
                data.account = _.map(data.account, function(x) {
                    x._hash = Math.getRandomHash();

                    return x;
                });

                data._delete_account = [];
                vm.data = Object.assign({}, data);
                vm.onRouteChange();
            } else {
                vm.$flash.error(resp.data.message);
            }

            vm.$loading.off('loading');
        },
        submit: function() {
            var vm = this;

            vm.$loading.on('loading');

            var data = vm._createPostData(vm.data);
            vm.$api.Person.create(data).then(function(resp) {
                if(!resp.data.is_error) {
                    if (vm.$route.params.id) {
                        vm.create();
                    } else {
                        vm.$router.push({ name: 'person-detail', params: { id: resp.data.data.id }});
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
        },
        addPhone: function() {
            var vm = this;

            if (vm.data.phones.length < 3) {
                vm.data.phones.push(null);
            } else {
                vm.$flash.error("Maksimal 3 No. Telp");
            }
        },
        deletePhone: function(index) {
            var vm = this;

            vm.data.phones.splice(index, 1);
        },
        addFax: function() {
            var vm = this;

            if (vm.data.fax.length < 3) {
                vm.data.fax.push(null);
            } else {
                vm.$flash.error("Maksimal 3 No. Fax");
            }
        },
        deleteFax: function(index) {
            var vm = this;

            vm.data.fax.splice(index, 1);
        },
        addAccount: function() {
            var vm = this;

            if (vm.data.account.length < 3) {
                var data = {
                    id: null,
                    bank_id: null,
                    account_number: null,
                    _hash: Math.getRandomHash()
                };

                vm.data.account.push(data);
            } else {
                vm.$flash.error("Maksimal 3 No. Rekening");
            }
        },
        deleteAccount: function(account) {
            var vm = this;

            if(account.id)
                vm.data._delete_account.push(account.id);

            vm.data.account = _.filter(vm.data.account, function(x) {
                return x._hash != account._hash;
            });
        },
        loadBank: function() {
            var vm = this;

            vm.$api.Category.all({page: 'all', group_by: 'bank'}).then(function(resp) {
                var data = resp.data.data;

                var bank = _.map(data, function(row) {
                            return { id: row.id, text: row.label };
                          });

                vm.options.bank = bank;
            });
        },
        loadCoas: async function() {
            var vm = this;

            var coas = await vm.loadMoreCoas(vm.data.coa_id, 1);

            vm.options.coa = coas.options;
        },
        loadMoreCoas: async function(keyword, page) {
            console.log('loadmoreCoa', keyword, page);
            var vm = this;

            var withTrashed = vm.withTrashed;

            var resp = await vm.$api.Coa.all({
                'keyword': keyword,
                'page': page,
            });
            var coas = resp.data.data;

            // Load list coas
            var list = _.map(coas, function(row) {
                return { id: row.id, text: row.name + ' - ' + row.label, model: row };
            });

            var meta = resp.data._meta;

            return {
                options: list,
                more: meta.current_page < meta.last_page
            };
        }
    }
}
</script>
<template>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card card-primary card-outline card-tabs">
              <box-overlay v-if="$loading.get('loading')" />
              <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-two-home-tab" data-toggle="pill" href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home" aria-selected="true">Detail</a>
                  </li>
                  <li class="nav-item" v-if="$route.params.id">
                    <a class="nav-link" id="custom-tabs-two-log-tab" data-toggle="pill" href="#custom-tabs-two-log" role="tab" aria-controls="log" aria-selected="false">Log</a>
                  </li>
                </ul>
              </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-two-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-two-home" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                        <form action="#" role="form" v-on:submit.prevent="submit()">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Ref No - AUTO</label>
                                <input type="text" class="form-control" placeholder="Masukkan Nomor Ref" v-model="data.ref_no"/>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>COA</label>
                                <select2-coa v-model="data.coa_id"
                                    data-allow-clear="true"
                                    placeholder="Pilih COA"
                                    v-bind:isHeader="0"></select2-coa>
                            </div>
                            <div class="form-group col-sm-12">
                                <label>Nama(PIC)*</label>
                                <input type="text" class="form-control" id="name" placeholder="Masukkan Nama" v-model="data.name"/>
                            </div>
                            <div class="form-group col-sm-12">
                                <label>Nama Perusahaan</label>
                                <input type="text" class="form-control" id="company_name" placeholder="Masukkan Nama Perusahaan" v-model="data.company_name"/>
                            </div>
                            <div class="form-group col-sm-12">
                                <label>Alamat</label>
                                <textarea class="form-control" id="address" v-model="data.address" rows="3"/>
                            </div>
                            <div class="form-group col-sm-12" v-if="data.category == 'customer'">
                                <label>Nama Penagihan(PIC)</label>
                                <input type="text" class="form-control" placeholder="Masukkan Nama Penagihan" v-model="data.collector_name"/>
                            </div>
                            <div class="form-group col-sm-12" v-if="data.category == 'customer'">
                                <label>Alamat Penagihan</label>
                                <textarea class="form-control" id="biling_address" v-model="data.billing_address" rows="3"/>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Kota</label>
                                <input type="text" class="form-control" placeholder="Masukkan Kota" v-model="data.city"/>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Email*</label>
                                <input type="email" class="form-control" placeholder="Masukkan Email" v-model="data.email"/>
                            </div>
                            <div class="form-group" :class="{'col-sm-12' : $route.query.category != 'customer', 'col-sm-6' : $route.query.category == 'customer'}">
                                <label>NPWP</label>
                                <input type="text" class="form-control" placeholder="Masukkan NPWP" v-model="data.npwp" v-bind:disabled="!data.is_ppn"/>
                            </div>
                            <div v-if="$route.query.category == 'customer' || data.category == 'customer'" class="form-group col-sm-6">
                                <label>Sales</label>
                                <select2 data-allow-clear="true" v-bind:options="options.sales"
                                        v-model="data.sales_id"
                                        placeholder="Pilih Sales">
                                </select2>
                            </div>
                            <div class="form-group col-sm-6" v-if="(data.category != null && data.category != 'supplier') || ($route.query.category != null && $route.query.category != 'supplier')">
                                <label>Pusat</label>
                                <select2 data-allow-clear="true" v-bind:options="options.pusat"
                                        v-model="data.person_id"
                                        placeholder="Pilih Pusat">
                                </select2>
                            </div>
                            <div class="form-group col-sm-4" v-if="(data.category != null && data.category != 'supplier') || ($route.query.category != null && $route.query.category != 'supplier')">
                                <label>TOP</label>
                                <div class="input-group">
                                  <input type="text" class="form-control" placeholder="Masukkan TOP" v-model="data.billing_period" />
                                  <div class="input-group-append">
                                    <span class="input-group-text">Hari</span>
                                  </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-4" v-if="data.category != null && data.category != 'supplier'">
                                <label>Industri</label>
                                <select2 data-allow-clear="true" v-bind:options="options.industri_category"
                                        v-model="data.industri_category_id"
                                        placeholder="Pilih Ketgori Industri">
                                </select2>
                            </div>
                            <div class="form-group col-sm-4" v-if="data.category != null && data.category == 'customer'">
                                <label>Factory</label>
                                <input type="text" class="form-control" placeholder="Masukkan Factory" v-model="data.factory" />
                                <small>Akan muncul di PDF Sales Invoice</small>
                            </div>
                            <div class="form-group col-sm-12">
                                <div class="form-check">
                                    <input id="is_ppn" class="form-check-input" type="checkbox"
                                            v-model="data.is_ppn"
                                            v-bind:true-value="1"
                                            v-bind:false-value="0">
                                    <label class="form-check-label" for="is_ppn">PPN</label>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label>No. Telp</label>
                                <div class="row">
                                    <div class="col-sm-4" v-for="(tr, $index) in data.phones">
                                        <div class="input-group mt-2">
                                            <input type="text" class="form-control" id="phones" placeholder="Masukkan No. Telp" v-model="data.phones[$index]"/>
                                            <span class="input-group-append">
                                                <button title="Hapus" type="button" class="btn btn-danger" @click="deletePhone($index)"><i class="fa fa-trash"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button title="Tambah No. Telp" type="button" class="btn btn-primary mt-2" @click="addPhone"><i class="fa fa-plus"></i></button>
                            </div>
                            <div class="form-group col-sm-12">
                                <label>No. Fax</label>
                                <div class="row">
                                    <div class="col-sm-4" v-for="(tr, $index) in data.fax">
                                        <div class="input-group mt-2">
                                            <input type="text" class="form-control" id="fax" placeholder="Masukkan No. Fax" v-model="data.fax[$index]"/>
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-danger" @click="deleteFax($index)"><i class="fa fa-trash"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary mt-2" @click="addFax"><i class="fa fa-plus"></i></button>
                            </div>
                            <div class="form-group col-sm-12">
                                <label>Rekening</label>
                                <div class="row">
                                    <div class="col-sm-12" v-for="(tr, $index) in data.account">
                                        <div class="row">
                                            <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Bank</label>
                                                <select v-model="data.account[$index].bank_id"
                                                        placeholder="Pilih Bank"
                                                        class="form-control">
                                                        <option v-for="option in options.bank" v-bind:value="option.id">
                                                        {{ option.text }}
                                                        </option>
                                                </select>
                                            </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>No. Rekening</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="account_number" placeholder="Masukkan No. Rekening" v-model="data.account[$index].account_number"/>
                                                        <span class="input-group-append">
                                                            <button title="Hapus" type="button" class="btn btn-danger" @click="deleteAccount(tr)"><i class="fa fa-trash"></i></button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <button title="Tambah Rekening" type="button" class="btn btn-primary mt-2" @click="addAccount"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label>Catatan</label>
                                <textarea class="form-control" v-model="data.notes" rows="5"/>
                            </div>
                            <div class="col-sm-12 mt-3">
                                <div class="float-left">
                                    <!-- <button type="button" class="btn btn-primary" @click="download()"><i class="fas fa-print"></i> Print(PDF)</button> -->
                                </div>
                                <div class="float-right" v-if="(category == 'supplier' && $ac.hasAccess('supplier_create')) || (category == 'customer' && $ac.hasAccess('customer_create')) || (category == 'employee' && $ac.hasAccess('employee_create'))">
                                    <button type="button" class="btn btn-default" @click="load()">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </div>
                            </div>
                        </form>
                        </div>
                    <!-- Tab Log -->
                    <div class="tab-pane fade" id="custom-tabs-two-log" role="tabpanel" aria-labelledby="custom-tabs-two-log-tab" v-if="$route.params.id">
                        <div class="row">
                        <div class="col-sm-12 mt-3">
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
                        </div>
                        </div>
                    </div>
                    <!-- /Tab Log -->
                </div>
            </div>
          </div>
          </div>
        </div>
      </div>
    </section>
</template>
