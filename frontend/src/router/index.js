import { createRouter, createWebHistory } from "vue-router";

// Import your views

import Home from "../views/Home.vue";
import Register from "../components/auth/Register.vue";
import Login from "../components/auth/Login.vue";
import page from "../components/auth/page.vue";
import ForgetPassword from "../components/auth/ForgetPassword.vue";
import ResetPassword from "../components/auth/ResetPassword.vue";
import JobDetails from "../pages/Singel_Job-Apply_Job/JobDetails.vue";
import ApplyJob from "../pages/Singel_Job-Apply_Job/ApplyJob.vue";
import ThankYouPage from "../pages/Singel_Job-Apply_Job/ThankYouPage.vue";

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
  //Marwa Nasser
  {
    path: "/job-details/:id",
    name: "JobDetails",
    component: JobDetails,
    props: true,
  },
  {
    path: "/apply",
    name: "ApplyJob",
    component: ApplyJob,
    props: (route) => ({
      jobId: route.query.jobId,
      jobTitle: route.query.jobTitle,
    }),
  },
  { path: "/thank-you", name: "ThankYouPage", component: ThankYouPage },
  //Marwa Nasser
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
