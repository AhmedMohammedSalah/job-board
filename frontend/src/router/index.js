import { createRouter, createWebHistory } from "vue-router";

// Import your views

import Home from "../views/Home.vue";
import candidateHomePage from "../views/candidateHome.vue"
import Register from "../components/auth/Register.vue";
import Login from "../components/auth/Login.vue";
import page from "../components/auth/page.vue";
import ForgetPassword from "../components/auth/ForgetPassword.vue";
import ResetPassword from "../components/auth/ResetPassword.vue";
import PendingJobsView from '../views/PendingJobsView.vue'
import inventory from "../views/InventoryView.vue"

const routes = [
  {
    path: "/",
    name: "Home",
    component: Home,
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/home',
    name: 'Home',
    component: Home,
  },{
  path: '/page',
  name: 'Page',
  component: page,
  },
  {
    path: "/ForgetPassword",
    name: "ForgetPassword",
    component: ForgetPassword,
  }
  ,{
    path: "/reset-password",
    name: "ResetPassword",
    component: ResetPassword,

  },
   ,{
    path: "/candidateHomePage",
    name: "candidateHomePage",
    component: candidateHomePage,

  },
    {
    path: '/inventory',
    name: 'inventory',
    component: inventory
  },
    {
    path: '/pending-jobs',
    name: 'PendingJobs',
    component: PendingJobsView,
    meta: { requiresAuth: true, requiresAdmin: true }
  },

];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior() {
    // Always scroll to top when navigating
    return { top: 0 };
  },
});

export default router;
