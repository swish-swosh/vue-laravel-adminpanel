<template>
    <b-breadcrumb>
        <b-breadcrumb-item href="/">
        <b-icon icon="house-fill" scale="1.25" shift-v="1.25" aria-hidden="true"></b-icon>
        Home
        </b-breadcrumb-item>
        <div v-for="(item, n) in items" :key="n">
            <b-breadcrumb-item :href="item.href">&nbsp;/&nbsp;{{item.text}}</b-breadcrumb-item>
        </div>
    </b-breadcrumb>
</template>

<script>

export default {
	name: 'home',
	data() {
		return {
			items: []
		}
	},
	watch: {
		'$route' (to, from) {
			// update breadcrumbs to route changes...
			let pathItems = to.fullPath.split("/")
			this.setPath(pathItems, this.items = [])
		}
	},
    created() {
		// build breadcrumb on create
		let pathItems = this.$route.fullPath.split("/")
		this.setPath(pathItems, this.items)
	},
	methods: {
		setPath(pathItems, items){
			// set children, ignore root, home is added statically in template
			for(let n=1, path = '/', active = ''; n<pathItems.length; n++){
				path += pathItems[n] + '/'
				items.push(
				{
					href: path,
					text: pathItems[n],
					active: active
				})
			}
		}
	}
}
</script>


<style lang="scss" scoped>

@import '~@/_variables.scss';

</style>
