<template>
    <div class="component-wrap">

        <!-- form -->
        <v-card>
            <v-card-title>
                <v-icon>vpn_key</v-icon> Edit Day Off 
            </v-card-title>
            <v-divider class="mb-2"></v-divider>
            <v-form v-model="valid" ref="dayoffFormAdd" lazy-validation>
                <v-container grid-list-md>
                    <v-layout row wrap >
                        <v-flex xs12 md6>
                            <div class="body-2 white--text">{{ translate('dayoff.detail') }}</div>
                        </v-flex>
                          <v-flex xs12 md12  align-center justify-center>
                            <v-date-picker ma12 filled landscape prepend-icon="search" v-bind:label="translate('common.date_range')"  range v-model="dates" ></v-date-picker>
                            <v-flex xs12 md12>
                                <v-select
                                    v-bind:label="translate('common.select_day_off_type')"
                                    v-bind:items="options.dayofftypes"
                                    v-model="selectedDayOffType"
                                    item-text="title"
                                    :rules="selectedDayOffTypeRules"
                                    item-value="key">
                                </v-select>
                                <v-textarea v-bind:label="translate('common.description')" v-model="description" :rules="descriptionRules"></v-textarea>
                            </v-flex>
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
        props: {
            propUserId: {
                required: false
            }
        },
        data() {
            return {
                valid: false,
                isLoading: false,
                dates: [],
                user_id:'',
                description: '',
                descriptionRules: [
                    (v) => !!v || this.translate('common.description_is_required'),
                ],
                options:{
                    dayofftypes:[
                        'Normal',
                        'Resmi',
                        'Günlük',
                        'Hatfalık',
                        'Yıllık',
                        'Saatlik'
                    ]
                },
                selectedDayOffType:'',
                selectedDayOffTypeRules:[
                    (v) => !!v || this.translate('common.day_off_type_is_required'),
                ],
            }
        },
        created() {
            this.$store.commit('setBreadcrumbs',[
                {label:this.translate('common.users'),to:{name:'users.list'}},
                {label:this.translate('common.dayoffs'),to:{name:'users.dayoff.list'}},
                {label:this.translate('common.edit'),name:''},
            ]);

            this.id = this.$attrs.id != undefined ? this.$attrs.id : this.propId;
            this.loadDayOff(()=>{});

            this.$eventBus.$on(['DAYOFF_UPDATED'],()=>{
                this.loadDayOff(()=>{});
            });

        },
        computed: {
            FormSelfID: function () {
                return this.$attrs.id;
            }, 
        },
        watch: {
            permissionKey(v) {
                if(v) this.permissionKey = v.replace(' ','.').toLowerCase();
            },
            title(v) {
                if(v) this.permissionKey = v.replace(' ','.').toLowerCase();
            },
            FormSelfID(v) {
                this.loadDayOff(() => {});
            }
        },
        methods: {
            loadDayOff(cb) {

                const self = this;

                // reset first
                self.groups = [];

                axios.get('/admin/dayoff/' + self.FormSelfID).then(function(response) {
                    let DayOff = response.data.data;

                    self.dates = [
                        DayOff.start_date,
                        DayOff.end_date
                    ];
                    self.user_id = DayOff.user_id;
                    self.description = DayOff.description;
                    self.selectedDayOffType = DayOff.type;
                    
                    self.$store.commit('setBreadcrumbs',[
                        {label:'DayOff',to:''},
                    ]);

                    cb();
                });
            },
            save() {

                const self = this;

                if(!(self.dates.length > 0)){
                     self.$store.commit('showSnackbar',{
                            message: this.translate('common.select_at_least_one_date'),
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

                axios.put('/admin/dayoff/'+self.FormSelfID,payload).then(function(response) {

                    self.$store.commit('showSnackbar',{
                        message: response.data.message,
                        color: 'success',
                        duration: 3000
                    });
                    self.$eventBus.$emit('DAYOFF_UPDATED');

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