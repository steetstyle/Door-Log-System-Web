<template>
    <div>
        <v-card>
            <v-card-title>
                <v-icon>person</v-icon> {{ translate('users.create_user')}}
            </v-card-title>
            <v-divider></v-divider>
            <v-form v-model="valid" ref="userFormAdd" lazy-validation>
                <v-container grid-list-md>
                    <v-layout row wrap>
                    <v-flex xs12 sm6>
                        <v-text-field v-bind:label="translate('common.first_name')" v-model="name" :rules="nameRules"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm6>
                        <v-text-field v-bind:label="translate('common.email')" v-model="email" :rules="emailRules"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm6>
                        <v-text-field v-bind:label="translate('common.password')" type="password" v-model="password" :rules="passwordRules"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm6>
                        <v-text-field v-bind:label="translate('common.confirm_password')" type="password" v-model="passwordConfirm" :rules="passwordConfirmRules"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm6>
                        <v-switch v-bind:label="translate('common.pre_activate_account')" v-model="active"></v-switch>
                    </v-flex>
                    <v-flex xs12><v-spacer></v-spacer></v-flex>
                    <v-flex xs12>
                        <h1 class="title"><v-icon>vpn_key</v-icon> {{ translate('common.special_permissions') }}</h1>
                        <v-alert color="info" icon="info" :value="true">
                            {{ translate('common.special_permissions_detail') }} 
                        </v-alert>
                        <v-divider></v-divider>
                    </v-flex>
                    <v-flex xs12 sm4>
                        <v-select
                                v-bind:label="translate('common.select_permission')"
                                v-bind:items="options.permissions"
                                v-model="selectedPermission"
                                item-text="title"
                                item-value="key"
                        ></v-select>
                    </v-flex>
                    <v-flex xs12 sm4>
                        <v-select
                                v-bind:label="translate('common.permission_value')"
                                v-bind:items="options.permissionValues"
                                v-model="selectedPermissionValue"
                                item-text="label"
                                item-value="value"
                        ></v-select>
                    </v-flex>
                    <v-flex xs12 sm4>
                        <v-btn @click="addSpecialPermission()" class="specialPrimary lighten-1" dark>
                           {{ translate('common.add_permission') }}
                            <v-icon right>add</v-icon>
                        </v-btn>
                    </v-flex>
                    <v-flex xs12>
                        <div class="permissions_container">
                            <v-chip v-for="(p,k) in permissions" :key="k" @click:close="removePermission(k)" class="white--text" :class="{'green':(p.value==1),'red':(p.value==-1),'blue':(p.value==0)}">
                                <v-avatar v-if="p.value==-1" class="red darken-4" v-bind:title="translate('common.deny')">
                                    <v-icon>block</v-icon>
                                </v-avatar>
                                <v-avatar v-if="p.value==1" class="green darken-4" v-bind:title="translate('common.allow')">
                                    <v-icon>check_circle</v-icon>
                                </v-avatar>
                                <v-avatar v-if="p.value==0" class="blue darken-4" v-bind:title="translate('common.inherit')">
                                    <v-icon>swap_horiz</v-icon>
                                </v-avatar>
                                {{p.title}}
                            </v-chip>
                            <div v-if="permissions.length===0" class="info pa-2">{{ translate('common.no_special_permissions_assigned') }}</div>
                        </div>
                    </v-flex>
                    <v-flex xs12><v-spacer></v-spacer></v-flex>
                    <v-flex xs12>
                        <h1 class="title"><v-icon>people</v-icon> {{ translate('common.groups') }}</h1>
                        <v-divider></v-divider>
                    </v-flex>
                    <v-flex xs12>
                        <v-switch v-for="(g,k) in options.groups" :key="k" v-bind:label="g.name" v-model="groups[g.id]"></v-switch>
                    </v-flex>
                    <v-flex xs12>
                        <v-btn @click="save()" :disabled="!valid" color="lighten" dark>{{ translate('common.save') }}</v-btn>
                    </v-flex>
                </v-layout>
                </v-container>
            </v-form>
        </v-card>
    </div>
</template>

<script>
    export default {
        data() {

            const self = this;

            return {
                valid: false,
                name: '',
                nameRules: [
                    (v) => !!v || this.translate('common.name_is_required'),
                ],
                email: '',
                emailRules: [
                    (v) => !!v || this.translate('common.email_is_required'),
                    (v) => /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(v) || this.translate('common.email_must_be_valid')
                ],
                password: '',
                passwordRules: [
                    (v) => !!v || this.translate('common.password_is_required'),
                    (v) => v && v.length >= 8 || this.translate('common.password_must_be_atleast_8_characters'),
                ],
                passwordConfirm: '',
                passwordConfirmRules: [
                    (v) => !(v!==self.password) || this.translate('common.password_do_not_match'),
                ],
                permissions: [],
                groups: [],
                active:'',
                options: {
                    permissions: [],
                    permissionValues:[
                        {label:this.translate('common.allow'), value:1},
                        {label:this.translate('common.deny'), value:-1},
                        {label:this.translate('common.inherit'), value:0},
                    ],
                    groups: []
                },
                selectedPermission: {},
                selectedPermissionValue: 0,

                alert: {
                    show: false,
                    icon: '',
                    color: '',
                    message: ''
                }
            }
        },
        mounted() {
            console.log('components.UserFormAdd.vue');

            const self = this;

            self.loadPermissions(cb=>{});
            self.loadGroups(cb=>{});

            self.$store.commit('setBreadcrumbs',[
                {label:this.translate('common.users'),to:{name:'users.list'}},
                {label:this.translate('common.create'),to:''},
            ]);
        },
        methods: {
            removePermission(i) {

                const self = this;

                self.permissions.splice(i,1);
            },
            save() {

                const self = this;

                let payload = {
                    name: self.name,
                    email: self.email,
                    password: self.password,
                    active: self.active ? moment().format('YYYY-MM-DD') : null,
                    permissions: self.permissions,
                    groups: self.groups,
                };

                self.$store.commit('showLoader');

                axios.post('/admin/users',payload).then(function(response) {

                    self.$store.commit('showSnackbar',{
                       message: response.data.message,
                       color: 'success',
                       duration: 3000
                    });

                    self.$eventBus.$emit('USER_ADDED');
                    self.$store.commit('hideLoader');

                    // reset
                    self.$refs.userFormAdd.reset();
                    self.permissions = [];

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
            addSpecialPermission() {
                const self = this;

                _.each(self.options.permissions,(p)=>{

                    if(self.selectedPermission===p.key) {

                        if(!self.existsInPermissions(self.selectedPermission)) {
                            p.value = self.selectedPermissionValue;
                            self.permissions.push(p);
                        }
                    }
                });
            },
            existsInPermissions(permissionKey) {
                const self = this;
                let found = false;
                _.each(self.permissions,(p)=>{
                    if(p.key===permissionKey) found = true;
                });
                return found;
            },
            loadPermissions(cb) {

                const self = this;

                let params = {
                    paginate: 'no'
                };

                axios.get('/admin/permissions',{params: params}).then(function(response) {
                    self.options.permissions = response.data.data;
                    cb();
                });
            },
            loadGroups(cb) {

                const self = this;

                let params = {
                    paginate: 'no'
                };

                axios.get('/admin/groups',{params: params}).then(function(response) {
                    self.options.groups = response.data.data;

                    _.each(self.options.groups,g=>{
                        g.selected = false;
                    });

                    cb();
                });
            }
        }
    }
</script>