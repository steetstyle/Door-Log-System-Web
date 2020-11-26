<template>
    <div class="component-wrap">
      <!-- search -->
        <v-card class="pt-3">
            <div class="d-flex flex-lg-row flex-sm-column">
              <div class="flex-grow-2 pa-1">
                    <v-date-picker filled prepend-icon="search" label="Rabg By Start" v-model="filters.start_date"></v-date-picker>
                </div>
                <div class="flex-grow-1 pa-2">
                    <center>
                    <h1 style="font-size:50px;"> FILTERS</h1>
                    </center>
                    <v-text-field filled prepend-icon="search" label="Filter By Name" v-model="filters.name"></v-text-field>
                    <v-text-field filled prepend-icon="search" label="Filter By Key" v-model="filters.key"></v-text-field>
                    <v-text-field filled prepend-icon="search" label="Filter By Branch" v-model="filters.branchName"></v-text-field>
                     <v-btn block @click="clearFilters()" class="specialPrimary lighten-1" dark>
                        CLEAR FILTERS
                        <v-icon right dark>remove</v-icon>
                    </v-btn>
                </div>
                <div class="flex-grow-2 pa-1">
                    <v-date-picker filled prepend-icon="search" color="green lighten-1" label="Range By End" v-model="filters.end_date"></v-date-picker>
                </div>
               
            </div>
        </v-card>
        <!-- /search -->
        <v-divider class="pb-2"/>
        <!-- data table -->
        <v-data-table
                hide-default-header
                v-bind:headers="headers"
                :options.sync="pagination"
                :items="items"
                :server-items-length="totalItems"
                class="elevation-1">
            <template v-slot:header="{props:{headers}}">
                <thead>
                <tr>
                    <th v-for="header in headers">
                        <div v-if="header.value=='name'" :class="`text-${header.align}`"><v-icon>mdi-person</v-icon> {{header.text}}</div>
                       <div v-else :class="`text-${header.align}`">{{header.text}}</div>
                    </th>
                </tr>
                </thead>
            </template>
            <template v-slot:body="{items}">
                <tbody>
                    <tr v-for="item in items" :key="item.id">
                        <td>
                            <div class="ml-n1 my-1 d-flex justify-space-between align-content-space-around flex-wrap">
                                <v-btn @click="$router.push({name:'cards.create',params:{key: item.key}})" class="ma-1" small outlined icon color="info">
                                    <v-icon small>mdi-pencil</v-icon>
                                </v-btn>
                            </div>
                        </td>
                        <td>{{ item.id }}</td>
                        <td>{{ item.key }}</td>
                        <td>{{ item.branch != null ? item.branch.name : '' }}</td>
                        <td>{{ item.user != null  ? item.user.name: '' }}</td>
                        <td>{{ item.created_at }}</td>
                        <td>{{ item.updated_at }}</td>
                    </tr>
                </tbody>
            </template>
        </v-data-table>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                headers: [
                    { text: 'Action', value: false, align: 'left', sortable: false },
                    { text: 'ID', value: 'id', align: 'left', sortable: true },
                    { text: 'Key', value: 'key', align: 'left', sortable: false },
                    { text: 'Place', value: 'place', align: 'left', sortable: true },
                    { text: 'Card User Name', value: 'first_name', align: 'left', sortable: true },
                    { text: 'Created At', value: 'created_at', align: 'left', sortable: true },
                    { text: 'Updated At', value: 'updated_at', align: 'left', sortable: true },
                ],
                items: [],
                totalItems: 0,
                pagination: {
                    itemsPerPage: 10
                },

                filters: {
                    key: '',
                    name: '',
                    start_date:'',
                    end_date:'',
                    branchName: ''
                },

                dialogs: {
                    showPermissions: {
                        items: [],
                        show: false
                    }
                }
            }
        },
        mounted() {
            const self = this;

            self.loadCardLogins(()=>{});

            self.$store.commit('setBreadcrumbs',[
                            {label:'Card Logins',name:''}
            ]);
        },
        watch: {
            pagination: {
                handler () {
                    this.loadCardLogins(()=>{});
                },
            },
            'filters.key':_.debounce(function(){
                const self = this;
                self.loadCardLogins(()=>{});
            },700),
            'filters.name':_.debounce(function(){
                const self = this;
                self.loadCardLogins(()=>{});
            },700),
            'filters.start_date':_.debounce(function(){
                const self = this;
                self.loadCardLogins(()=>{});
            },700),
            'filters.end_date':_.debounce(function(){
                const self = this;
                self.loadCardLogins(()=>{});
            },700),
            'filters.branchName':_.debounce(function(){
                const self = this;
                self.loadCardLogins(()=>{});
            },700),
            
        },
        methods: {
            clearFilters() {

                const self = this;

                self.filters =  {
                    key: '',
                    name: '',
                    start_date:'',
                    end_date:'',
                    branchName: ''
                };
                
            },
            loadCardLogins(cb) {

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

                axios.get('/admin/cardlogin',{params: params}).then(function(response) {
                    self.items = response.data.data;
                    self.totalItems = response.data.data.total;
                    self.pagination.totalItems = response.data.data.total;
                    (cb || Function)();
                });
            },
        }
    }
</script>
