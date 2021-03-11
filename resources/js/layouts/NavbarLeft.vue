<template>
  <b-navbar type="custom">         
      <b-navbar-nav class="navbar-left">
        <div v-for="(item, i) in navBarLeft" :key="i">
          <div v-if="item.menuEntry == 'router-link'">
            <router-link class="router-link" :to="{ path:item.path }" v-slot="{ href, isExactActive }">
              <b-nav-item :active="isExactActive" :href="href">
                <b-icon :icon="item.icon" aria-hidden="true"> </b-icon> <span>{{ item.name }}</span>
              </b-nav-item>
            </router-link>
          </div>

          <div v-else-if="item.menuEntry == 'nav-item-click'">
            <b-nav-item @click="menuClicked(item.pathName)">
              <b-icon :icon="item.icon" aria-hidden="true"> </b-icon> <span>{{ item.name }}</span>
            </b-nav-item>
          </div>

          <div v-else-if="item.menuEntry == 'submenu-toggle'">
            <div class="mt-3 submenu">
              <div class="submenu-toggle" @click="toggleCollapsedState(item.pathName)">
                <b-icon :icon="item.icon" aria-hidden="true"></b-icon>
                <span>{{item.name}}</span>
                <b-icon :icon="item.pathName == expandedSubmenu ? 'caret-up-fill':'caret-down-fill'"></b-icon>
              </div>
             
                <b-collapse class="submenu-items" :id="item.pathName" :visible="expandedSubmenu == item.pathName">
                <div v-if="item.children.length">
                  <div v-for="(child, j) in item.children" :key="j">
                    <div v-if="child.menuEntry == 'router-link'">
                      <router-link :to="{ path:child.path }" v-slot="{ href, isActive }">
                        <b-nav-item :active="isActive" :href="href">
                          <b-icon :icon="child.icon" aria-hidden="true"> </b-icon> <span>{{ child.name }}</span>
                        </b-nav-item>
                      </router-link>
                    </div>
                    <div v-else-if="child.menuEntry == 'nav-item-click'">
                      <b-nav-item @click="menuClicked(child.pathName)">
                        <b-icon :icon="child.icon" aria-hidden="true"> </b-icon> <span>{{ child.name }}</span>
                      </b-nav-item>
                    </div>
                  </div>
                </div>
              </b-collapse>
            </div>
          </div>
        </div>
      </b-navbar-nav>
  </b-navbar>
</template>

<script>
    import navBarLeftData from '../data/navBarLeft.json'
    import { mapActions, mapGetters, mapMutations } from 'vuex'
    export default {
        name: 'NavbarLeft',
        data() {
          return {
            navBarLeft: navBarLeftData,
            expandedSubmenu: ''
          }
        },
        computed: {
        ...mapGetters('auth', {
          getAccessToken: 'accessToken'
        })
        },
        created(){
          this.expandedSubmenu = this.$route.fullPath.split("/")[1]
        },
        methods: {
          ...mapActions('auth',{
            logout: 'logout'
          }),
          ...mapMutations('users', {
            clearUser: 'SET_USER'
          }),
          menuClicked(item) {
            switch(item){
              case 'logout':
                let self = this
                this.logout(this.getAccessToken).then(function(response){
                  if(response.status === 200) {
                    self.clearUser(null)
                    self.$router.push({name: 'login'}).catch(()=>{})
                  }
                  else
                  console.log(response.message+' in logout')
                })
              break;
              case 'lock':
                console.log('lock:')
              break;
            }
          },
          toggleCollapsedState(pathName){
            this.expandedSubmenu == pathName ? this.expandedSubmenu = '' : this.expandedSubmenu = pathName
          },

          // orderedMenu: function (menu, by) { todo
          // }
        }
    }
</script>

<style lang="scss" scoped>

@import '~@/_variables.scss';

.navbar {
  margin: 0;
  font-size: 1.4rem;
  align-items: flex-start;
}

.b-icon.bi {
  vertical-align: bottom;
}

.sub-menu .nav-link {
  margin-left: 3.2rem;
}

.nav-item {
  margin: 1rem 0 0 0;
}

.navbar-nav {
  display: block;
}

.submenu-toggle {
  cursor: pointer;
  margin-left: 0.5rem;
}

</style>