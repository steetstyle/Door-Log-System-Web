<template>
	<div class="component-wrap primary"  >
		<div class="d-flex flex-lg-row ">
			<div class="row">
				<v-card width="50%" class="elevation-0 borderlessCard pa-12"  elevation="0">
					<v-img
						height="200px"
						src="https://cdn.pixabay.com/photo/2020/07/12/07/47/bee-5396362_1280.jpg"
					>
						<v-app-bar
							flat
							color="rgba(0, 0, 0, 0)"
							@click="$router.push({ name: 'log.list' })"
						>
							<v-app-bar-nav-icon color="white"></v-app-bar-nav-icon>

							<v-toolbar-title class="title white--text pl-0">
								{{ translate('common.last_card_logins')}}
							</v-toolbar-title>

							<v-spacer></v-spacer>
						</v-app-bar>
					</v-img>

					<v-card-text>
						<div class="font-weight-bold ml-8 mb-2">
							{{ translate('common.from_now')}}
						</div>

						<v-timeline align-top dense>
							<v-timeline-item v-for="log in logs" :key="log.created_at" small>
								<div>
									<div class="font-weight-normal">
										<strong>{{ log.key }}</strong> @{{
											moment(log.created_at.split(".000000")[0]).format(
												"DD/MM/YYYY hh:mm:ss"
											)
										}}
									</div>
									<div>{{  log.user != null ? log.user.name : '' }}</div>@{{ log.branch != null ? log.branch.name : '' }}
								</div>
							</v-timeline-item>
						</v-timeline>
					</v-card-text>
				</v-card>
                 <v-card width="50%" class="elevation-0 borderlessCard pa-12">
					<v-img
						height="200px"
						src="https://cdn.pixabay.com/photo/2020/07/12/07/47/bee-5396362_1280.jpg"
					>
						<v-app-bar
							flat
							color="rgba(0, 0, 0, 0)"
							@click="$router.push({ name: 'cards.list' })"
						>
							<v-app-bar-nav-icon color="white"></v-app-bar-nav-icon>

							<v-toolbar-title class="title white--text pl-0">
								{{ translate('common.last_added_cards')}}
							</v-toolbar-title>

							<v-spacer></v-spacer>
						</v-app-bar>

					</v-img>

					<v-card-text>
						<div class="font-weight-bold ml-8 mb-2">
							{{ translate('common.from_now')}}
						</div>

						<v-timeline align-top dense>
							<v-timeline-item v-for="card in cards" :key="card.created_at" small>
								<div>
									<div class="font-weight-normal">
										<strong>{{ card.branch.name }}</strong> @{{
											moment(card.created_at.split(".000000")[0]).format(
												"DD/MM/YYYY hh:mm:ss"
											)
										}}
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
            timer: ''
		};
	},
	mounted() {
		const self = this;

		this.loadCardLogins(() => {});
        this.loadCards(() => {});
        
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
	}
};
</script>
