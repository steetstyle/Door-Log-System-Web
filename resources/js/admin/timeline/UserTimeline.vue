<template>
    <div class="col-md-12">
        <full-calendar :events="fcEvents"  locale="en" ></full-calendar>
    </div>
</template>

<script>
    import fullCalendar from 'vue-fullcalendar'
    export default {
        props: {
            propName: {
                required: true
            }
        },
        components : {
            'full-calendar': require('vue-fullcalendar')	
        },
        data() {
            return {
                name: '',
                nameRules: [
                    (v) => !!v || 'Name is required',
                ],
                fcEvents : [],
                filters: {
                    name: '',
                    email: '',
                    groupId: [],
                    groupOptions: []
                },
                totalItems: 0,
                pagination: {
                    itemsPerPage: 1000
                },
                items: [] 
            }
        },
      
        created() {

            this.filters.name = this.name = this.propName;
            
            const self = this;

            let params = {
                key: self.filters.key,
                name: self.filters.name,
                start_date: self.filters.start_date,
                end_date: self.filters.end_date,
                branchName: self.filters.branchName,
                page: self.pagination.page,
                per_page: self.pagination.itemsPerPage
            };

            axios.get('/admin/cardlogin',{params: params}).then((res) => {
                let logEvents = [];

                res.data.data.map((cardLoginLog) => {

                    let loginDate = new Date(cardLoginLog.created_at);

                    let thatDay = new Date(cardLoginLog.created_at);
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
                    title += DetermineIsLate ? " Geç Giriş" : !DetermineIsLate && todayLogs.length < 1 ? "Gün İçinde İlk Giriş" : !isFirstLoginThatDay ? todayLogs_last_element.title.includes("Giriş") ? "Çıkış" : "Giriş" : "Belirlenemedi" ;

                    logEvents.push( {
                        title : title ,
                        start : new Date(cardLoginLog.created_at),
                        end : new Date(cardLoginLog.created_at),
                        branch : cardLoginLog.branch != null  ? cardLoginLog.branch.name : " ",
                        cssClass : [DetermineIsLate ? "toolate" : !DetermineIsLate && todayLogs.length < 1 ? "firstlogin" : !isFirstLoginThatDay ? todayLogs_last_element.title.includes("Giriş") ? "exit" : "login" : "undetermined",]
                    });
                });
                this.fcEvents = logEvents;
            });
        },

        methods: {
            add_minutes(dt, time) {
                let splittedTime = time.split(':');
                return new Date(dt.getTime() +  splittedTime[0]*60*60000 + splittedTime[1]*60000);
            },
            dayClick (day, jsEvent) {
                console.log('dayClick', day, jsEvent)
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
        min-height:200px!important;
    }
</style>