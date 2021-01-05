<template>
    <div>
        <v-card>
            <v-card-title>
                <v-icon>list</v-icon> {{ translate('card.new_card')}}
            </v-card-title>
            <v-divider class="mb-2"></v-divider>
            <v-form v-model="valid" ref="cardFormAdd" lazy-validation>
                <v-container grid-list-md>
                <v-layout row wrap>
                    <v-flex xs12 sm12>
                        <v-text-field label="Key" v-model="key" :rules="keyRules"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm8>
                        <v-select
                                v-bind:label="translate('common.select_branches')"
                                v-bind:items="options.branches"
                                v-model="selectedBranch"
                                item-text="name"
                                item-value="id"
                        ></v-select>
                    </v-flex>
                     <v-flex xs12 sm8>
                        <v-select
                                v-bind:label="translate('common.select_user')"
                                v-bind:items="options.users"
                                v-model="selectedUser"
                                item-text="name"
                                item-value="id"
                        ></v-select>
                    </v-flex>
                  
                    <v-flex xs12>
                        <v-btn @click="create()" :disabled="!valid" color="lighten" dark>{{ translate('common.create') }}</v-btn>
                    </v-flex>
                </v-layout>
            </v-container>
            </v-form>
        </v-card>
    </div>
</template>

<script>
    export default {
        props: {
            propKey: {
                required: false
            }
        },
        data() {

            const self = this;

            return {
                valid: false,
                key: '',
                keyRules: [
                    (v) => !!v || this.translate('common.key_is_required'),
                ],
                selectedUser:null,
                selectedBranch:null,
                users: [],
                branches: [],
                active:'',
                options: {
                    users: [],
                    branches: [],
                },
                alert: {
                    show: false,
                    icon: '',
                    color: '',
                    message: ''
                },
                branches_totalItems:0,
                users_totalItems:0,
                pagination:{
                    users_page:0,
                    users_itemsPerPage:0,
                    users_totalItems:0,
                    branches_page:0,
                    branches_itemsPerPage:0,
                    branches_totalItems:0,
                }
            }
        },
        mounted() {
            console.log('components.BranchFormEdit.vue');

            const self = this;

            this.loadBranches(()=>{});
            this.loadUsers(()=>{});

            self.key = self.propKey;

            this.$store.commit('setBreadcrumbs',[
                {label:this.translate('card.title'),to:{name:'cards.list'}},
                {label:this.translate('card.new_card'),to:''},
            ]);

        },
        methods: {
            create(){
                const self = this;

                let payload = {
                    key: self.key,
                    user_id: self.selectedUser,
                    branch_id: self.selectedBranch,
                };

                self.$store.commit('showLoader');

                axios.post('/admin/cards',payload).then(function(response) {

                    self.$store.commit('showSnackbar',{
                       message: response.data.message,
                       color: 'success',
                       duration: 3000
                    });

                    self.$eventBus.$emit('CARD_ADDED');
                    self.$store.commit('hideLoader');

                    // reset
                    self.$refs.cardFormAdd.reset();

                }).catch(function (error) {

                    self.$store.commit('hideLoader');

                    if (error.response) {
                        self.$store.commit('showSnackbar',{
                            message: error.response.data.message,
                            color: 'error',
                            duration: 3000
                        });
                    } else if (error.request) {
                        console.log(error.request);
                    } else {
                        console.log('Error', error.message);
                    }
                });
            },
             loadBranches(cb) {

                const self = this;

                let params = {
                    page: self.pagination.branches_page,
                    per_page: self.pagination.branches_itemsPerPage
                };

                axios.get('/admin/branches',{params: params}).then(function(response) {
                    self.options.branches  = response.data.data.data;
                    self.branches_totalItems = response.data.data.total;
                    self.pagination.branches_totalItems = response.data.data.total;
                    (cb || Function)();
                });
            },
            loadUsers(cb) {

                const self = this;

                let params = {
                    page: self.pagination.users_page,
                    per_page: self.pagination.users_itemsPerPage
                };

                axios.get('/admin/users',{params: params}).then(function(response) {
                    self.options.users = response.data.data.data;
                    self.users_totalItems = response.data.data.total;
                    self.pagination.users_totalItems = response.data.data.total;
                    (cb || Function)();
                });
            },
        }
    }
</script>