import store from '../store'

// redirect if not authenticated
export default (to, from, next) => {

    if (store.getters['auth/accessToken'] === null) {
        next({ name: 'login' });
    }
}

