<template>
    <div>
        <v-card>
            <v-card-title>
                <v-icon>list</v-icon> {{ translate('branch.edit_branch')}}
            </v-card-title>
            <v-divider class="mb-2"></v-divider>
            <v-form v-model="valid" ref="branchFormEdit" lazy-validation>
                <v-container grid-list-md>
                <v-layout row wrap>
                    <v-flex xs12 sm12>
                        <v-text-field v-bind:label="translate('common.name')" v-model="name" :rules="nameRules"></v-text-field>
                    </v-flex>
                      <v-flex xs12 sm12>
                        <v-text-field v-bind:label="translate('common.tag')" v-model="tag" :rules="tagRules"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm8>
                        <v-select
                                v-bind:label="translate('common.select_users')"
                                v-bind:items="options.users"
                                v-model="selectedUser"
                                item-text="name"
                                item-value="id"
                        ></v-select>
                    </v-flex>
                  
                    <v-flex xs12 sm4>
                        <v-btn @click="addUser()" class="specialPrimary lighten-1" dark>
                            {{ translate('common.add_user')}}
                            <v-icon right>add</v-icon>
                        </v-btn>
                    </v-flex>
                    <v-flex xs12>
                        <div class="users_container">
                            <v-chip v-for="(p,k) in branch_users" :key="k" @click:close="removeUser(p)" close class="white--text" :class="{'green':(p.value==1),'red':(p.value==-1),'blue':(p.value==0)}">
                                {{p.name}}
                            </v-chip>
                            <div v-if="branch_users && branch_users.length===0">{{ translate('common.special_user_assigned') }}</div>
                        </div>
                    </v-flex>
                    <v-flex xs12>
                        <v-btn @click="save()" :disabled="!valid" color="lighten" dark>{{ translate('common.update')}}</v-btn>
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
            propBranchId: {
                required: true
            }
        },
        data() {

            const self = this;

            return {
                valid: false,
                name: '',
                nameRules: [
                    (v) => !!v || this.translate('common.name_is_required'),
                ],
                tag: '',
                tagRules: [
                    (v) => !!v || this.translate('common.tag_is_required'),
                ],
                selectedUser:null,
                branch_users: [],
                active:'',
                options: {
                    users: []
                },
                alert: {
                    show: false,
                    icon: '',
                    color: '',
                    message: ''
                },
                totalItems:0,
                pagination:{
                    totalItems:0
                }
            }
        },
        mounted() {
            console.log('components.BranchFormEdit.vue');

            const self = this;

            this.loadBranch(()=>{});
            this.loadUsers(()=>{});
            this.loadBranchUsers(()=>{});

            self.$eventBus.$on(['USER_ADDED', 'USER_DELETED'],()=>{
                self.loadUsers(()=>{});
                self.loadBranchUsers(() => {});
            });

            self.$eventBus.$on(['BRANCH_UPDATED',],()=>{
                this.loadBranch(()=>{});
            });

            this.$store.commit('setBreadcrumbs',[
                {label:this.translate('common.branches'),to:{name:'branches.list'}},
            ]);

        },
        methods: {
            save() {

                const self = this;

                let payload = {
                    name: self.name,
                    tag: self.tag,
                    email: self.email,
                    password: self.password ? self.password : null,
                    active: self.active ? moment().format('YYYY-MM-DD') : null,
                    permissions: self.permissions,
                    groups: self.groups,
                };

                self.$store.commit('showLoader');

                axios.put('/admin/branches/'+self.propBranchId,payload).then(function(response) {

                    self.$store.commit('showSnackbar',{
                       message: response.data.message,
                       color: 'success',
                       duration: 3000
                    });

                    self.$eventBus.$emit('BRANCH_UPDATED');
                    self.$store.commit('hideLoader');

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
            loadBranch(cb) {

                const self = this;

               
                axios.get('/admin/branches/' + self.propBranchId).then(function(response) {
                    let branch = response.data.data;

                    self.name = branch.name;
                    self.tag = branch.tag;

                    self.$store.commit('setBreadcrumbs',[
                        {label:this.translate('common.branches'),to:{name:'branches.list'}},
                        {label:branch.name,to:''},
                        {label:this.translate('common.edit'),to:''},
                    ]);

                    cb();
                });
            },
            loadUsers(cb) {

                const self = this;

                let params = {
                };

                axios.get('/admin/users',{params: params}).then(function(response) {
                    self.options.users = response.data.data.data;
                    self.totalItems = response.data.data.total;
                    self.pagination.totalItems = response.data.data.total;
                    (cb || Function)();
                });
            },
            loadBranchUsers(cb){
                const self = this;


                axios.get('/admin/branchuser/'+self.propBranchId).then(function(response) {
                    self.branch_users = response.data.data;
                    (cb || Function)();
                });
            },
            addUser(user){

                const self = this;

                let params = {
                    user_id:self.selectedUser,
                    branch_id: self.propBranchId
                };

                self.$store.commit('showLoader');
                axios.post('/admin/branchuser',params).then(function(response) {

                    self.$eventBus.$emit('USER_ADDED');
                    self.$store.commit('hideLoader');
                    (cb || Function)();
                });
            },
            removeUser(branch_user) {
                const self = this;
                self.$store.commit('showDialog',{
                    type: "confirm",
                    title: this.translate('common.confirm_deletion'),
                    message: this.translate('common._are_you_sure_for_delete_this_branch'),
                    okCb: ()=>{

                        let params = {
                            branch_id: self.propBranchId
                        };

                        axios.delete('/admin/branchuser/'+branch_user.id+","+self.propBranchId,params).then(function(response) {

                            self.$store.commit('showSnackbar',{
                                message: response.data.message,
                                color: 'success',
                                duration: 3000
                            });

                            self.$eventBus.$emit('USER_DELETED');

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
                    cancelCb: ()=>{
                        console.log("CANCEL");
                    }
                });
            },
        }
    }
</script>