<template>
	<div class="component-wrap primary"  >
		<div class="d-flex flex-lg-row ">
			<div class="row">
				<v-app-bar 
				width="100%"
					flat
					color="secondary"
					centered
					>

					<v-toolbar-title class="title white--text pl-0">
						{{ translate('common.infos')}}
					</v-toolbar-title>

					<v-spacer></v-spacer>
				</v-app-bar>
				<v-card
					class="mx-auto ma-8 rounded-lg"
					color="secondary"
					max-height=150
					min-width="30%"
					outlined
					>
					<v-list-item three-line>
						<v-list-item-content>
							<div class="overline mb-4 white--text">
								{{ translate('common.info')}}
							</div>
							<v-list-item-title class="headline mb-1 white--text" >
								{{ translate('common.users_count')}}: <h3 class="white--text">{{ infos.branches_count}}</h3>
							</v-list-item-title> 
						</v-list-item-content>
						
					</v-list-item>
				</v-card>
				<v-card
					class="mx-auto ma-8 rounded-lg"
					max-height=150
					min-width="30%"
					color="secondary"
					outlined
					>
					<v-list-item three-line>
						<v-list-item-content>
							<div class="overline mb-4 white--text">
								{{ translate('common.info')}}
							</div>
							<v-list-item-title class="headline mb-1 white--text">
								{{ translate('common.cards_count')}}: <h3 class="white--text">{{ infos.branches_count}}</h3>
							</v-list-item-title> 
						</v-list-item-content>
						
					</v-list-item>
				</v-card>
				<v-card
					class="mx-auto ma-8 rounded-lg"
					max-height=150
					min-width="30%"
					color="secondary"
					outlined
					>
					<v-list-item three-line>
						<v-list-item-content>
							<div class="overline mb-4 white--text">
								{{ translate('common.info')}}
							</div>
							<v-list-item-title class="headline mb-1 white--text">
								{{ translate('common.branches_count')}}: <h3 class="white--text">{{ infos.branches_count}}</h3>
							</v-list-item-title> 
						</v-list-item-content>
						
					</v-list-item>
				</v-card>
				<v-app-bar width="100%"
					flat
					color="secondary"
					centered
					>

					<v-toolbar-title class="title white--text pl-0">
						{{ translate('common.last_added_infos')}}
					</v-toolbar-title>

					<v-spacer></v-spacer>
				</v-app-bar>
				<v-card width="50%"  class="elevation-0 borderlessCard pa-12"  elevation="0">
					
						<v-app-bar
							flat
							color="secondary"
							@click="$router.push({ name: 'log.list' })"
						>
							<v-app-bar-nav-icon color="white"></v-app-bar-nav-icon>

							<v-toolbar-title class="title white--text pl-0">
								{{ translate('common.last_card_logins')}}
							</v-toolbar-title>

							<v-spacer></v-spacer>
						</v-app-bar>

					<v-card-text>
						<div class="font-weight-bold ml-8 mb-2">
							{{ translate('common.from_now')}}
						</div>

						<v-timeline align-top dense>
							<v-timeline-item v-for="log in logs" :key="log.created_at" small>
								<div>
									<div class="font-weight-normal">
										<strong>{{ log.key }}</strong> @{{ convertDateToString( log.updated_at) }}
									</div>
									<div>{{  log.user != null ? log.user.name : '' }}</div>@{{ log.branch != null ? log.branch.name : '' }}
								</div>
							</v-timeline-item>
						</v-timeline>
					</v-card-text>
				</v-card>
                 <v-card width="50%" class="elevation-0 borderlessCard pa-12">
				
						<v-app-bar
							flat
							color="secondary"
							@click="$router.push({ name: 'cards.list' })"
						>
							<v-app-bar-nav-icon color="white"></v-app-bar-nav-icon>

							<v-toolbar-title class="title white--text pl-0">
								{{ translate('common.last_added_cards')}}
							</v-toolbar-title>

							<v-spacer></v-spacer>
						</v-app-bar>

				

					<v-card-text>
						<div class="font-weight-bold ml-8 mb-2">
							{{ translate('common.from_now')}}
						</div>

						<v-timeline align-top dense>
							<v-timeline-item v-for="card in cards" :key="card.created_at" small>
								<div>
									<div class="font-weight-normal">
										<strong>{{ card.branch.name }}</strong> @{{ convertDateToString( card.created_at) }}
									</div>
									<div>{{ card.user.name }}</div>
								</div>
							</v-timeline-item>
						</v-timeline>
					</v-card-text>
				</v-card>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
			logs: [],
			cards: [],
			infos: [],
            timer: ''
		};
	},
	mounted() {
		const self = this;

		this.loadCardLogins(() => {});
		this.loadCards(() => {});
		this.getInfos(() => {});
        
        this.timer = setInterval(() => {
            this.loadCardLogins(() => {});
            this.loadCards(() => {});
        }, 20000);

		self.$store.commit("setBreadcrumbs", [
			{ label: this.translate('common.dashboard'), name: "Dashboard" }
		]);
	},
	methods: {
		loadCardLogins(cb) {
			const self = this;
			let params = {
				key: "",
				name: "",
				start_date: "",
				end_date: "",
				branchName: "",
				page: 0,
				per_page: 10
			};
			axios.get("/admin/cardlogin", params).then(function(response) {
				self.logs = params.per_page ? response.data.data.slice(response.data.data.total,params.per_page) : response.data.data;

				(cb || Function)();
			});
        },
        loadCards(cb) {

                const self = this;

                let params = {
                    key: '',
                    page: 0,
                    per_page: 10
                };

                axios.get('/admin/cards',{params: params}).then(function(response) {
					self.cards = params.per_page ? response.data.data.slice(response.data.data.total,params.per_page) : response.data.data;

                    (cb || Function)();
                });
		},
		getInfos(cb){

			const self = this;

			let params = {

			};
			axios.post('api/info', {params: params}).then(function(response){

				self.infos = response.data.data;
				console.log(self.infos);

				(cb || Function)();
			});
		}
	}
};
</script>
