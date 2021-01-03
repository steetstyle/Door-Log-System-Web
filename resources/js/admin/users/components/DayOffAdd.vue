<template>
    <div class="component-wrap">

        <!-- form -->
        <v-card>
            <v-card-title>
                <v-icon>vpn_key</v-icon> Create Day Off 
            </v-card-title>
            <v-divider class="mb-2"></v-divider>
            <v-form v-model="valid" ref="dayoffFormAdd" lazy-validation>
                <v-container grid-list-md>
                    <v-layout row wrap >
                        <v-flex xs12 md6>
                            <div class="body-2 white--text">Dayoff Details</div>
                        </v-flex>
                          <v-flex xs12 md12  align-center justify-center>
                            <v-date-picker ma12 filled landscape prepend-icon="search" label="Date Range" range v-model="dates" ></v-date-picker>
                            <v-flex xs12 md12>
                                <v-select
                                    label="Select Day Off Type"
                                    v-bind:items="options.dayofftypes"
                                    v-model="selectedDayOffType"
                                    item-text="title"
                                    :rules="selectedDayOffTypeRules"
                                    item-value="key">
                                </v-select>
                                <v-textarea label="Description" v-model="description" :rules="descriptionRules"></v-textarea>
                            </v-flex>
                            <v-btn @click="save()" :loading="isLoading" :disabled="!valid || isLoading" color="lighten" dark>Save</v-btn>
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
        props: {
            propUserId: {
                required: false
            }
        },
        data() {
            return {
                valid: false,
                isLoading: false,
                dates: ['2019-09-10', '2019-09-20'],
                user_id:'',
                description: '',
                descriptionRules: [
                    (v) => !!v || 'Description is required',
                ],
                options:{
                    dayofftypes:[
                        'Normal',
                        'Offical'
                    ]
                },
                selectedDayOffType:'',
                selectedDayOffTypeRules:[
                    (v) => !!v || 'Day Off Type is required',
                ],
            }
        },
        mounted() {
            this.$store.commit('setBreadcrumbs',[
                {label:'Users',to:{name:'users.list'}},
                {label:'DayOffs',to:{name:'users.dayoff.list'}},
                {label:'Create',name:''},
            ]);

            this.user_id = this.$attrs.userId != undefined ? this.$attrs.userId : this.propUserId;
        },
        watch: {
            permissionKey(v) {
                if(v) this.permissionKey = v.replace(' ','.').toLowerCase();
            },
            title(v) {
                if(v) this.permissionKey = v.replace(' ','.').toLowerCase();
            }
        },
        methods: {
            save() {

                const self = this;

                if(!(self.dates.length > 0)){
                     self.$store.commit('showSnackbar',{
                            message: "Select at least one Date",
                            color: 'error',
                            duration: 3000
                        });
                    return;
                }

                let payload = {
                   start_date: self.dates[1] != null ? self.dates[1] > self.dates[0] ? self.dates[0] : self.dates[1] : self.dates[1],
                   end_date: self.dates[1] != null ? self.dates[1] > self.dates[0] ? self.dates[1] : self.dates[0] : self.dates[0],
                   type: self.selectedDayOffType,
                   user_id: (self.user_id),
                   description: self.description,
                };

                console.log(payload);

                self.isLoading = true;

                axios.post('/admin/dayoff',payload).then(function(response) {

                    self.$store.commit('showSnackbar',{
                        message: response.data.message,
                        color: 'success',
                        duration: 3000
                    });
                    self.$eventBus.$emit('DAYOFF_ADDED');

                    // reset
                    self.$refs.dayoffFormAdd.reset();
                    self.permissions = [];
                    self.isLoading = false;

                }).catch(function (error) {

                    self.isLoading = false;
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
            }
        }
    }
</script>