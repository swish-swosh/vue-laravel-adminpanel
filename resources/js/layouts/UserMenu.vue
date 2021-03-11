<template>
    <div class="user-menu ml-3">
        <b-img class="ml-5" width="45" height="45" 
            :src="getUser.user_image ? '/storage/uploads/'+getUser.user_image : '/storage/uploads/incognito.png'" 
            rounded="circle" alt="Circle image">
        </b-img>
        <b-navbar-nav class="ml-3 mr-3">
            <b-nav-item-dropdown right>
            
                <template v-slot:button-content>
                    <span>{{ getUser.name }}</span> <br>
                    <span>{{ topRoleName }}</span>
                </template>
            
                <div v-for="(item, i) in userMenu" :key="i">
                    <b-dropdown-item v-if="item.menuEntry == 'router-link'" :href="item.path">
                        <b-icon class="mr-2" :icon="item.icon" aria-hidden="true"> </b-icon><span>{{ item.name }}</span>
                    </b-dropdown-item>

                    <b-dropdown-item v-else-if="item.menuEntry == 'nav-item-click'" @click="menuClicked(item.pathName)">
                        <b-icon class="mr-2" :icon="item.icon" aria-hidden="true"> </b-icon> <span>{{ item.name }}</span>
                    </b-dropdown-item>

                    <div v-else-if="item.menuEntry == 'nav-item-divider'">
                       <b-dropdown-divider></b-dropdown-divider>
                    </div>
                </div>
            </b-nav-item-dropdown>
        </b-navbar-nav>
    </div>
</template>

<script>
    import userMenuData from '../data/userMenu.json'
    import { mapActions, mapGetters, mapMutations } from 'vuex';
    export default {
        name: 'UserMenu',
        data(){
            return {
                userMenu: userMenuData
            }
        },
        computed: {
            ...mapGetters('auth', {
                getUser: 'user',
                topRoleName: 'topRoleName'
            })
        },
        mounted() {
        },
        methods:  {
            ...mapActions('auth', {
                logout: 'logout'
            }),
            ...mapMutations('auth', {
                clearUser: 'SET_USER_AUTH_EMPTY'
            }),
            async menuClicked(item) {

                switch(item){
                    case 'logout':
                        let response = await this.logout()
                        this.$router.push({name: 'login', params: { message: response.message }}).catch(()=>{})
                    break;

                    case 'lock':
                        console.log('lock:');
                    break;
                }
            }
        }
    }
</script>