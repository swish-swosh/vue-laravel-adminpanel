
import Vue from 'vue';

// setup vue router and middleware
import VueRouter from 'vue-router';
Vue.use(VueRouter);

// authentication is handles by backend / through store interceptors
// import AuthMiddleware from './middleware/auth';

import Auth from './pages/Auth';
import Login from './components/forms/Login';
import CreateAccount from './components/forms/CreateAccount';
import PasswordReset from './components/forms/PasswordReset';
import EmailPasswordRecovery from './components/forms/EmailPasswordRecovery';
import EmailResendVerification from './components/forms/EmailResendVerification';

import Home from './pages/Home';

import MyProfile from './pages/MyProfile'; 

import Lock from './pages/Lock';

import Tasks from './pages/Tasks';
import Task from './components/forms/Task';

import Monitors from './pages/Monitors';
import MonitorTabs from './components/tabs/MonitorTabs';

import Users from './pages/Users';
import User from './components/forms/User';

import Access from './pages/Access';

const router = new VueRouter({

    mode: 'history',
    routes: [{
        path: '/',
        name: 'home',
        component: Home
      },
      {
        path: '/auth',
        name: 'auth',
        component: Auth,
        children: [{
          path: 'create-account',
          name: 'createAccount',
          component: CreateAccount
        },{
          path: 'login',
          name: 'login',
          component: Login,
          props: true // used for sending extra info to user login (like a verification link has been send)
        },{
          path: 'verification-expired',
          name: 'verificationExpired',
          component: EmailResendVerification
        },{
          path: 'password-forgot',
          name: 'emailPasswordRecovery',
          component: EmailPasswordRecovery  // enter email were a recovery link will be send to
        },{
          path: 'password-reset',
          name: 'PasswordReset',
          component: PasswordReset         // password update form 
        }]
      },
      {
        path: '/lock',
        name: 'lock',
        component: Lock
      },
      {
        path: '/my-profile',
        name: 'myProfile',
        component: MyProfile
      },
      {
        path: '/access',
        name: 'access',
        component: Access
      },
      {
        path: '/users',
        name: 'users',
        component: Users,
        props: true,
        children: [{
          path: '/users/:id?',
          name: 'user',
          component: User,
          props: true
        }]
      },
      {
        path: '/tasks',
        name: 'tasks',
        component: Tasks,
        props: true,
        children: [{
          path: '/tasks/:id?',
          name: 'task',
          component: Task,
          props: true
        }]
      },
      {
        path: '/monitors',
        name: 'monitors',
        component: Monitors,
        props: true,
        children: [{
          path: '/monitors/:id?/:tab?',
          name: 'monitor',
          component: MonitorTabs,
          props: true
        }]
    }]
})

export default router