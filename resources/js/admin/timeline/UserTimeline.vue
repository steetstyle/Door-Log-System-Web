<template>
    <div class="col-md-12">

        <!-- dialog for show permissions -->
        <v-dialog v-model="dialogs.showCreateForm.show" absolute  max-width="660px">
            <v-card>
                <v-card-text>
                    <DayOffAdd :userId="filters.userId"/>
                </v-card-text>
            </v-card>
        </v-dialog>

        <!-- dialog for show permissions -->
        <v-dialog v-model="dialogs.showEditForm.show" absolute  max-width="660px">
            <v-card>
                <v-card-text>
                <v-btn style=" position: fixed; top: 25px;" large m15 @click="closeDialog('dayoff_edit', item)" outlined rounded color="grey" dark>{{ translate('common.back')}}</v-btn>
                 <DayOffEdit :key="dialogs.showEditForm.key" :id="dialogs.showEditForm.item !== null ? dialogs.showEditForm.item.id : null"/>
                </v-card-text>
            </v-card>
        </v-dialog>

        <!-- dialog for show permissions -->
        <v-dialog v-model="dialogs.showCreateCardLogForm.show" absolute  max-width="660px">
            <v-card>
                <v-card-text>
                    <CardLoginAdd />
                </v-card-text>
            </v-card>
        </v-dialog>

        <!-- dialog for show permissions -->
        <v-dialog v-model="dialogs.showLogEditForm.show" absolute  max-width="660px">
            <v-card>
                <v-card-text>
                <v-btn style=" position: fixed; top: 25px;" large m15 @click="closeDialog('log_edit', null)" outlined rounded color="grey" dark>{{ translate('common.back')}}</v-btn>
                 <CardLoginEdit :on_delete="closeDialog" :id="dialogs.showLogEditForm.item !== null ? dialogs.showLogEditForm.item.id : null"/>
                </v-card-text>
            </v-card>
        </v-dialog>

         <!-- dialog for show permissions -->
        <v-dialog v-model="dialogs.showdayOffList.show" absolute  max-width="660px">
            <v-card>
                <v-data-table
                    hide-default-header
                    v-bind:headers="dialogs.showdayOffList.headers"
                    :options.sync="dialogs.showdayOffList.pagination"
                    :items="dialogs.showdayOffList.items"
                    :server-items-length="dialogs.showdayOffList.items.length"
                    class="elevation-1">
                    <template v-slot:header="{props:{headers}}">
                        <thead>
                        <tr>
                            <th v-for="header in headers">
                                <div v-if="header.value=='id'" :class="`text-${header.align}`"><v-icon>mdi-vpn_key</v-icon> {{header.text}}</div>
                                <div v-else-if="header.value=='start_date'" :class="`text-${header.align}`"><v-icon>mdi-vpn_key</v-icon> {{header.text}}</div>
                                <div v-else-if="header.value=='end_date'" :class="`text-${header.align}`"><v-icon>mdi-vpn_key</v-icon> {{header.text}}</div>
                                <div v-else-if="header.value=='description'" :class="`text-${header.align}`"><v-icon>mdi-vpn_key</v-icon> {{header.text}}</div>
                                <div v-else-if="header.value=='action'" :class="`text-${header.align}`"><v-icon>mdi-vpn_key</v-icon> {{header.text}}</div>
                            </th>
                        </tr>
                        </thead>
                    </template>
                    <template v-slot:body="{items}">
                        <tbody>
                            <tr v-for="item in items" :key="item.id">
                                <td>{{ item.id }}</td>
                                <td>{{ item.start_date }}</td>
                                <td>{{ item.end_date }}</td>
                                <td>{{ item.description }}</td>
                                <td>
                                    <v-btn small @click="showDialog('dayoff_edit', item)" outlined rounded color="grey" dark>{{ translate('common.edit')}}</v-btn>
                                </td>
                            </tr>
                        </tbody>
                    </template>
                </v-data-table>
            </v-card>
        </v-dialog>

        <v-dialog v-model="dialogs.showTimeline.show" absolute>
            <v-card>
                <v-data-table
                    hide-default-header
                    v-bind:headers="dialogs.showTimeline.headers"
                    :options.sync="dialogs.showTimeline.pagination"
                    :items="fcEvents"
                    :server-items-length="fcEvents.length"
                    class="elevation-1">
                    <template v-slot:header="{props:{headers}}">
                        <thead>
                        <tr>
                            <th v-for="header in headers">
                                <div v-if="header.value=='card'" :class="`text-${header.align}`"><v-icon>mdi-vpn_key</v-icon> {{header.text}}</div>
                                <div v-else-if="header.value=='branch'" :class="`text-${header.align}`"><v-icon>mdi-vpn_key</v-icon> {{header.text}}</div>
                                <div v-else-if="header.value=='login_date'" :class="`text-${header.align}`"><v-icon>mdi-vpn_key</v-icon> {{header.text}}</div>
                                <div v-else-if="header.value=='status'" :class="`text-${header.align}`"><v-icon>mdi-vpn_key</v-icon> {{header.text}}</div>
                            </th>
                        </tr>
                        </thead>
                    </template>
                    <template v-slot:body="{items}">
                        <tbody>
                            <tr v-for="item in items" :key="item.id">
                                <td></td>
                                <td>{{ item.card }}</td>
                                <td>{{ item.branch }}</td>
                                <td>{{ convertDateToString(item.start) }}</td>
                                <td>{{ item.type }}</td>
                            </tr>
                        </tbody>
                    </template>
                </v-data-table>
            </v-card>
        </v-dialog>

     <v-divider class="py-5"/>
        <v-flex xs12>
            <v-btn @click="showDialog('create_form',{})"  color="lighten" dark>{{ translate('common.add_day_off')}}</v-btn>
            <v-btn @click="showDialog('timeline_list',{})"  color="lighten" dark>{{ translate('common.show_as_list')}}</v-btn>
            <v-btn @click="showDialog('dayoff_list',{})"  color="lighten" dark>{{ translate('common.show_day_off_list') }}</v-btn>
            <v-btn @click="showDialog('create_cardlog',{})"  color="lighten" dark>{{ translate('common.create_day_log') }}</v-btn>
        </v-flex>
        <full-calendar 
                    :events="fcEvents"   
                    @eventClick="eventClick"
                    @dayClick="dayClick" 
                    @moreClick="moreClick"
                    locale="tr" ></full-calendar>
    </div>
</template>

<script>
    import fullCalendar from 'vue-fullcalendar'
    export default {
        props: {
            propUserId: {
                required: true
            }
        },

        components : {
            'full-calendar': require('vue-fullcalendar')	
        },

        computed:{
            timelineList: function () {
                return this.dialogs.showTimeline.pagination.page;
            }, 
            dayoffList: function () {
                return this.dialogs.showdayOffList.pagination.page;
            },  
            showCardLogAddForm: function () {
                return this.dialogs.showCreateCardLogForm.show;
            }, 
            showCardLogEditForm: function () {
                return this.dialogs.showCardLogEditForm.show;
            }, 
           
        },

        watch: {
            timelineList: {
                handler:function() {
                    this.retrieveTimelineListDataFromApi(false);
                },
            },
            dayoffList: {
                handler:function() {
                    this.retrieveDayOffListDataFromApi(false);
                },
            },
            
        },

        data() {
            return {
                name: '',
                nameRules: [
                    (v) => !!v || this.translate('common.name_is_required'),
                ],
                userId:'',
                fcEvents : [],
                filters: {
                    name: '',
                    userId: '',
                    email: '',
                    groupId: [],
                    groupOptions: [],
                    dayOffList: {
                        userId: '',
                        start_date: '',
                        end_date: '',
                        description: ''
                    }
                },
                totalItems: 0,
                pagination: {
                    itemsPerPage: 1000
                },
                items: [],
                dialogs: {
                    showCreateForm: {
                        items: [],
                        show: false
                    },
                    showEditForm: {
                        item: null,
                        show: false
                    },
                    showdayOffList: {
                        items: [],
                        show: false,
                        pagination: {
                            itemsPerPage: 10,
                            rowsPerPage: 10,
                            page:1
                        },
                        headers: [
                            { text: 'Id', value: 'id', align: 'left', sortable: false },
                            { text: 'Start Date', value: 'start_date', align: 'left', sortable: false },
                            { text: 'End Date', value: 'end_date', align: 'left', sortable: false },
                            { text: 'Description', value: 'description', align: 'left', sortable: false },
                            { text: this.translate('common.action'), value: 'action', align: 'left', sortable: false },
                        ]
                    
                    },
                    showCreateCardLogForm: {
                        show: false
                    },
                    showLogEditForm:{
                        item: null,
                        show: false
                    },
                    showTimeline: {
                        show: false,
                        items: [],
                         pagination: {
                            itemsPerPage: 10,
                            rowsPerPage: 10,
                            page:1
                        },
                        headers: [
                            { text: 'Id', value: 'id', align: 'left', sortable: false },
                            { text: 'Card', value: 'card', align: 'left', sortable: false },
                            { text: 'Branch', value: 'branch', align: 'left', sortable: false },
                            { text: 'Login Date', value: 'login_date', align: 'left', sortable: false },
                            { text: 'Status', value: 'status', align: 'left', sortable: false },
                            { text: 'Title', value: 'title', align: 'left', sortable: false },
                        ],
                    }
                }
            }
        },
      
        created() {

            this.retrieveTimelineListDataFromApi(true);
            this.retrieveDayOffListDataFromApi(true);
            this.$eventBus.$on(['DAYOFF_UPDATED'],()=>{
                this.retrieveDayOffListDataFromApi(false);
            });

            this.$eventBus.$on(['CARDLOGIN_DELETED', 'CARDLOGIN_UPDATED'],()=>{
                this.retrieveDayOffListDataFromApi(false);
                this.dialogs.showLogEditForm.show = false;
            });

            this.$store.commit('setBreadcrumbs',[
                {label:this.translate('common.dayoff'),to:''},
            ]);
        },

        methods: {
            retrieveTimelineListDataFromApi(isFirstRetrieve = false){

                this.filters.userId = this.userId = this.propUserId;
            
                const self = this;

                let params;
                
                if(isFirstRetrieve){
                    params = {
                        key: self.filters.key,
                        name: self.filters.name,
                        user_id: self.filters.userId,
                        start_date: self.filters.start_date,
                        end_date: self.filters.end_date,
                        branchName: self.filters.branchName,
                        page: self.pagination.page,
                        per_page: self.pagination.itemsPerPage
                    };
                }
                else{
                    params = {
                        key: self.filters.key,
                        name: self.filters.name,
                        user_id: self.filters.userId,
                        start_date: self.filters.start_date,
                        end_date: self.filters.end_date,
                        branchName: self.filters.branchName,
                        page: self.dialogs.showTimeline.pagination.page,
                        per_page: self.dialogs.showTimeline.pagination.itemsPerPage
                    };
                }

                axios.get('/admin/cardlogin',{params: params}).then((res) => {
                    let logEvents = [];
                    let counter = 0;

                    res.data.data.map((cardLoginLog) => {
                        console.log(cardLoginLog);

                        let loginDate = new Date(cardLoginLog.log);

                        let thatDay = new Date(cardLoginLog.log);
                        thatDay.setHours(0,0,0,0);

                        let minimumLoginDate = this.add_minutes(thatDay, cardLoginLog.login_time);
                        let minimumExitDate = this.add_minutes(thatDay, cardLoginLog.exit_time);
                        let max_late_Date = this.add_minutes(minimumLoginDate, cardLoginLog.max_late_time);

                        let isFirstLoginThatDay = true;

                        let todayLogs = [];

                        logEvents.forEach(logEvent => {

                            let copyLogDate = logEvent.start;
                            copyLogDate.setHours(0,0,0,0);
                            let branchName = cardLoginLog.branch != null  ? cardLoginLog.branch.name : " ";

                            if(thatDay.getTime() == copyLogDate.getTime() && logEvent.branch == branchName){
                                isFirstLoginThatDay = false;
                                todayLogs.push(logEvent);
                            }   
                        });

                        let DetermineIsLate =  isFirstLoginThatDay && todayLogs.length  < 1 ? loginDate.getTime() > max_late_Date.getTime()  : todayLogs.length  < 1 ? isFirstLoginThatDay : false;

                        var todayLogs_last_element = todayLogs[todayLogs.length - 1];

                        let title = cardLoginLog.branch != null  ? cardLoginLog.branch.name : " ";
                        title += DetermineIsLate ? "  Geç Giriş " : !DetermineIsLate && todayLogs.length < 1 ?
                                                    "  Gün İçinde İlk Giriş " : !isFirstLoginThatDay ? 
                                                    todayLogs_last_element.title.includes("Giriş") ? 
                                                    " Çıkış " : " Giriş " : " Belirlenemedi " ;

                        title += " "+loginDate;

                        let type = DetermineIsLate ? "Geç Giriş" : !DetermineIsLate && todayLogs.length < 1 ?
                                            "Gün İçinde İlk Giriş " : !isFirstLoginThatDay ? 
                                            todayLogs_last_element.title.includes("Giriş") ? 
                                            "Çıkış" : "Giriş" : "Belirlenemedi" ;

                        logEvents.push( {
                            id: counter,
                            card: cardLoginLog.key,
                            title : title ,
                            type: type,
                            start : new Date(cardLoginLog.log),
                            end : new Date(cardLoginLog.log),
                            branch : cardLoginLog.branch != null  ? cardLoginLog.branch.name : " ",
                            cssClass : [DetermineIsLate ? "toolate" : !DetermineIsLate && todayLogs.length < 1 ? 
                                                        "firstlogin" : !isFirstLoginThatDay ? todayLogs_last_element.title.includes("Giriş") ? 
                                                        "exit" : "login" : "undetermined", "id" + cardLoginLog.id]
                        });
                        counter++;
                    });

                    if(isFirstRetrieve){
                        this.fcEvents = this.dialogs.showTimeline.items =  logEvents;
                    }
                    else{
                        this.dialogs.showTimeline.items =  logEvents;
                    }                        

                });
            },
            retrieveDayOffListDataFromApi(isFirstRetrieve = false){

            
                const self = this;

                let params;
                
             
                params = {
                    user_id: self.filters.dayOffList.userId,
                    start_date: self.filters.dayOffList.start_date,
                    end_date: self.filters.dayOffList.end_date,
                    description: self.filters.dayOffList.description,
                    page: self.dialogs.showdayOffList.pagination.page,
                    per_page: self.dialogs.showdayOffList.pagination.itemsPerPage
                };

                axios.get('/admin/dayoff',{params: params}).then((res) => {
                    console.log(res.data.data);
                    this.dialogs.showdayOffList.items = res.data.data.data;        

                });
            },
            add_minutes(dt, time) {
                let splittedTime = time.split(':');
                return new Date(dt.getTime() +  splittedTime[0]*60*60000 + splittedTime[1]*60000);
            },
            dayClick (day, jsEvent) {
                console.log('dayClick', day, jsEvent);
            },
            eventClick (day, jsEvent, pos) {
                let event_class= jsEvent.path[0].className;
                let target_item_id = event_class.split("id")[1].split(" ")[0];
                axios.get('/admin/cardlogin/'+target_item_id).then((res) => {
                    this.showDialog('log_edit', res.data.data);   
                });

            },
            moreClick (day, events, jsEvent) {
                console.log('moreClick', event, jsEvent, pos)
            },
            showDialog(dialog, data) {

                const self = this;

                switch (dialog){
                    case 'create_form':
                        setTimeout(()=>{
                            self.dialogs.showCreateForm.show = true;
                        },500);
                        break;
                     case 'timeline_list':
                        setTimeout(()=>{
                            self.dialogs.showTimeline.show = true;
                        },500);
                        break;
                    case 'dayoff_list':
                        setTimeout(()=>{
                            self.dialogs.showdayOffList.show = true;
                        },500);
                        break;
                    case 'log_edit':
                        setTimeout(()=>{
                            self.dialogs.showLogEditForm.item = data;
                            self.dialogs.showLogEditForm.show = true;
                        },500);
                        break;
                    case 'dayoff_edit':
                        self.dialogs.showEditForm.item = data;
                        self.$eventBus.$emit('DAYOFF_UPDATED');
                        setTimeout(()=>{
                            self.dialogs.showEditForm.show = true;
                            self.dialogs.showLogEditForm.key=Math.random();
                        },500);
                        break;
                    case 'create_cardlog':
                         setTimeout(()=>{
                            self.dialogs.showCreateCardLogForm.show = true;
                        },500);
                        break;
                }
            },
            closeDialog(dialog, data) {

                const self = this;

                switch (dialog){
                    case 'create_form':
                            self.dialogs.showCreateForm.show = false;
                        break;
                     case 'timeline_list':
                            self.dialogs.showTimeline.show = false;
                        break;
                    case 'dayoff_list':
                            self.dialogs.showdayOffList.show = false;
                        break;
                    case 'dayoff_edit':
                            self.dialogs.showEditForm.show = false;
                        break;
                    case 'log_edit':
                            self.dialogs.showLogEditForm.show = false;
                        break;
                    case 'log_add':
                            self.dialogs.showCreateCardLogForm.show = false;
                        break;
                }
            },
        }
    }
</script>

<style >
    .comp-full-calendar{
        max-width: 100%;
        min-height: 100%;
    }
    
    .toolate{
        background-color:rgb(143, 90, 90)!important;
        height: 50px;
    }
    
    .firstlogin{
        background-color:rgb(99, 177, 99)!important;
        height: 50px;
    }
    
    .exit{
        background-color:red!important;
        height: 50px;
    }
    
    .login{
        background-color:green!important;
        height: 50px;
    }
    
    .undetermined{
        background-color:rgb(114, 110, 110)!important;
        height: 50px;
    }

    .day-cell{
        min-height:100px!important;
    }

    .full-calendar-body .dates .more-events {
        width: 500px;
    }
    .full-calendar-body .dates .dates-events .events-week .events-day .day-number {
    text-align: right;
    padding: 0px 0px 0px 0px;
    opacity: 0;
}
</style>