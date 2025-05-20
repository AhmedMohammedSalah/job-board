import { createRouter, createWebHistory } from "vue-router";

// Import your views
import Home from "../views/Home.vue";
import Register from "../components/auth/Register.vue";
import Login from "../components/auth/Login.vue";
import Page from "../components/auth/page.vue";
import ForgetPassword from "../components/auth/ForgetPassword.vue";
import ResetPassword from "../components/auth/ResetPassword.vue";
import JobDetails from "../pages/Singel_Job-Apply_Job/JobDetails.vue";
import ApplyJob from "../pages/Singel_Job-Apply_Job/ApplyJob.vue";
import ThankYouPage from "../pages/Singel_Job-Apply_Job/ThankYouPage.vue";

import CandidateLayout from "../layouts/CandidateLayout.vue";
import CandidateOverview from "../views/candidate/CandidateOverview.vue";
import candidateSettings from "../views/candidate/CandidateSettings.vue";
import RecentlyApplied from "../components/candidate/RecentlyApplied.vue";

const routes = [
  {
    path: "/",
    name: "Home",
    component: Home,
  },
  {
    path: "/candidate",
    name: "candidate",
    component: CandidateLayout,
    children: [
      {
        path: "",
        name: "candidate-overview",
        component: CandidateOverview,
      },
      {
        path: "recentlyApplied",
        name: "recently-applied",
        component: RecentlyApplied,
      },
      {
        path: "settings",
        name: "candidate-settings",
        component: candidateSettings,
      },
    ],
  },
  {
    path: "/register",
    name: "Register",
    component: Register,
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
  },
  {
    path: "/page",
    name: "Page",
    component: Page,
  },
  {
    path: "/forget-password",
    name: "ForgetPassword",
    component: ForgetPassword,
  },
  {
    path: "/reset-password",
    name: "ResetPassword",
    component: ResetPassword,
  },
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
  {
    path: "/thank-you",
    name: "ThankYouPage",
    component: ThankYouPage,
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior() {
    return { top: 0 };
  },
});

export default router;