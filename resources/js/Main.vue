<template>
        <section class="body">
			<!-- top header -->
			<header class="top-header">
				<div class="logo">
						<b-nav-item href="/">my-adminpanel.com</b-nav-item>
				</div>
				<!-- search box -->
				<div class="header-right">

				<b-form inline :class="navbarRightMax ? 'pull-right mr-30 max': 'pull-right min'">
					<b-navbar>
						<!-- <b-input-group class="search">
							<template v-slot:prepend>
								<b-input-group-text ><font-awesome-icon icon="search" /></b-input-group-text>
							</template>
							<b-form-input size="lg" type="search" ></b-form-input>
						</b-input-group> -->

						<b-button-group class="alerts ml-3" size="lg" >
							<b-button v-b-popover.hover.left="'Task name due @ 5:00pm'" title="Task due">
								Tasks
							</b-button>
							<b-button>
								Resources
							</b-button>
						</b-button-group>

						<UserMenu />
					</b-navbar>
				</b-form>
			
				</div>
				<!-- end: search & user box -->
			</header>
			<!-- end: header -->

            <div class="inner-wrapper">
				<!-- navbar-left -->
				<aside :class="navbarLeftMax ? 'max': 'min'" class="navbar-left" :id="fullWidth ? 'fullwidth': ''" >
					<b-container class="navbar-left-header">
						<b-row>
							<b-col class="text-left title" cols="9" md="7">ADMIN</b-col>
							<b-col cols="3" md="5" class="menu-control-button">
								<b-icon @click="navbarLeftMax = !navbarLeftMax" :icon="navbarLeftMax ? 'list' : 'list'" aria-hidden="true"></b-icon>
							</b-col>
						</b-row>
					</b-container>
					<NavbarLeft />
				</aside>
				<!-- /navbar-left -->
				<!-- center area -->
				<section role="main" :class="navbarLeftMax ? 'navbar-left-max': 'navbar-left-min'" class="content-left">
                    <header class="page-header">
						<div class="menu-control-button sm" @click="fullWidth = !fullWidth">
							<b-icon v-if="fullWidth" icon='list' aria-hidden="true"></b-icon>
							<b-icon v-else rotate="90" icon='chevron-expand' aria-hidden="true"></b-icon>
						</div>
						<div :class="navbarRightMax ? 'pull-right mr-30': 'pull-right'">
							<Breadcrumb/>
							<div class="menu-control-button" @click="navbarRightMax = !navbarRightMax">
								<b-icon :icon="navbarRightMax ? 'caret-right-fill' : 'calendar3'" aria-hidden="true"></b-icon>
							</div>
						</div>
						<h2>{{routeName}}</h2>
					</header>
					<div class="main-page">
						<!-- vue router view for main pages-->
						<router-view></router-view>
						<!-- /vue router view for main pages -->
					</div>
				</section>
				<!-- /center area -->
			</div>

			<!-- navbar-right -->
			<aside :class="navbarRightMax ? 'max': 'min'" class="navbar-right" >
				<NavbarRight :state="navbarRightMax" @rightPanelClosed="rightPanelClosed" />
			</aside>
			<!-- /navbar-right -->
        </section>
</template>

<script>
// layout elements for main framework
import NavbarLeft from './layouts/NavbarLeft'
import NavbarRight from './layouts/NavbarRight'
import UserMenu from './layouts/UserMenu'
import Breadcrumb from './components/Breadcrumb'

// modules, helpers, mixins
import VueScreenSize from 'vue-screen-size'	// get screensize helper
import { mapGetters, mapActions } from 'vuex'
export default {
	name: 'home',
	mixins: [VueScreenSize.VueScreenSizeMixin],
	data() {
		return {
			navbarLeftMax: true, // menu's default
			fullWidth: false,
			navbarRightMax: false,
			routeName: ''
		}
	},
	components: {
		NavbarLeft,
		NavbarRight,
		UserMenu,
		Breadcrumb
	},
	methods: {
		rightPanelClosed() {
			this.navbarRightMax=false
		}
	},
    created() {
		this.routeName = this.$route.name // [0].toUpperCase() + this.$route.name.slice(1)
	},
	mounted() {
		this.$vssWidth<769 ? this.fullWidth = true : this.fullWidth = false
	}
}
</script>

<style lang="scss" scoped>

@import '~@/_variables.scss';

</style>
