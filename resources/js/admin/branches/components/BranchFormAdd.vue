<template>
    <div class="component-wrap">

        <!-- form -->
        <v-card>
            <v-card-title>
                <v-icon>vpn_key</v-icon> {{ translate('branch.new_branch')}}
            </v-card-title>
            <v-divider class="mb-2"></v-divider>
            <v-form v-model="valid" ref="branchFormAdd" lazy-validation>
                <v-container grid-list-md>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <div class="body-2 white--text">{{ translate('branch.detail')}}</div>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field v-bind:label="translate('branch.name')" v-model="name" :rules="nameRules"></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field v-bind:label="translate('branch.tag')" v-model="tag" :rules="tagRules"></v-text-field>
                        </v-flex>
                     
                        <v-flex xs12>
                            <v-btn @click="save()" :loading="isLoading" :disabled="!valid || isLoading" color="lighten" dark>{{ translate('common.save') }}</v-btn>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </v-card>
        <!-- /form -->

    </div>
</template>

<script>
    export default {
        data() {
            return {
                valid: false,
                isLoading: false,
                name: '',
                nameRules: [
                    (v) => !!v || this.translate('common.name_is_required'),
                ],
                tag: '',
                tagRules: [
                    (v) => !!v || this.translate('common.tag_is_required'),
                ],
            
            }
        },
        mounted() {
            this.$store.commit('setBreadcrumbs',[
                {label:this.translate('common.branches'),to:{name:'branches.list'}},
            ]);
        },
        watch: {
            name(v) {
                if(v) this.permissionKey = v.replace(' ','.').toLowerCase();
            }
        },
        methods: {
            save() {

                const self = this;

                let payload = {
                    name: self.name,
                    tag: self.tag,
                };

                self.isLoading = true;

                axios.post('/admin/branches',payload).then(function(response) {

                    self.$store.commit('showSnackbar',{
                        message: response.data.message,
                        color: 'success',
                        duration: 3000
                    });
                    self.$eventBus.$emit('BRANCH_ADDED');

                    // reset
                    self.permissions = [];
                    self.isLoading = false;

                }).catch(function (error) {

                    self.isLoading = false;
                    self.$store.commit('hideLoader');
                    console.log(error.response);
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
            }
        }
    }
</script>