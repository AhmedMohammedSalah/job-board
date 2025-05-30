import { createRouter, createWebHistory } from "vue-router";

// Import your views
import Home from "../views/Home.vue";
import candidateHomePage from "../views/candidateHome.vue";
import Register from "../components/auth/Register.vue";
import Login from "../components/auth/Login.vue";
import Page from "../components/auth/page.vue";
import ForgetPassword from "../components/auth/ForgetPassword.vue";
import ResetPassword from "../components/auth/ResetPassword.vue";
import PendingJobsView from "../views/PendingJobsView.vue";
import inventory from "../views/InventoryView.vue";
import JobDetails from "../pages/Singel_Job-Apply_Job/JobDetails.vue";
import ApplyJob from "../pages/Singel_Job-Apply_Job/ApplyJob.vue";
import ThankYouPage from "../pages/Singel_Job-Apply_Job/ThankYouPage.vue";
import EmployersDashboard from "../components/employer/Dashboard.vue";
import CandidateLayout from "../layouts/CandidateLayout.vue";
import CandidateOverview from "../views/candidate/CandidateOverview.vue";
import candidateSettings from "../views/candidate/CandidateSettings.vue";
import RecentlyApplied from "../components/candidate/RecentlyApplied.vue";
import Myjobs from "../components/employer/MyJobs.vue";
import PostJob from "../components/employer/PostJob.vue";
import NotFound from "../pages/Common/NotFound.vue";
import FavoriteJobs from "../views/candidate/FavoriteJobs.vue";
import AdminComments from "../views/AdminComments.vue";

const routes = [
  {
    path: "/",
    name: "Home",
    component: Home,
  },
  {
    path: "/home",
    redirect: "/",
  },
  {
    path: "/candidate",
    name: "candidate",
    component: CandidateLayout,
    meta: { requiresAuth: true, role: "candidate" }, 
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
      {
        path: "/candidateHomePage",
        name: "candidateHomePage",
        component: candidateHomePage,
      },
      {
        path: "/favorite-jobs",
        name: "favorite-jobs",
        component: FavoriteJobs,
      },
    ],
  },
  {
    path: "/register",
    name: "Register",
    component: Register,
    meta: { requiresGuest: true },
  },
  {
    path: "/pending-jobs",
    name: "PendingJobs",
    component: PendingJobsView,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
    meta: { requiresGuest: true },
  },
  {
    path: "/page",
    name: "Page",
    component: Page,
  },
  {
    path: "/forgetpassword",
    name: "ForgetPassword",
    component: ForgetPassword,
    meta: { requiresGuest: true },
  },
  {
    path: "/reset-password",
    name: "ResetPassword",
    component: ResetPassword,
    meta: { requiresGuest: true },
  },
  // Admin routes
  {
    path: "/inventory",
    name: "PendingJobsView",
    component: PendingJobsView,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: "/adminComments",
    name: "adminComments",
    component: AdminComments,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  // Employer routes
  {
    path: "/EmployersDashboard",
    name: "EmployersDashboard",
    component: EmployersDashboard,
    meta: { requiresAuth: true, role: "employer" },
  },
  {
    path: "/MyJobs",
    name: "MyJobs",
    component: Myjobs,
    meta: { requiresAuth: true, role: "employer" },
  },
  {
    path: "/PostJob",
    name: "PostJob",
    component: PostJob,
    meta: { requiresAuth: true, role: "employer" },
  },
  // Public job routes
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
    meta: { requiresAuth: true, role: "candidate" },
  },
  {
    path: "/thank-you",
    name: "ThankYouPage",
    component: ThankYouPage,
  },
  // 404 page
  {
    path: "/:pathMatch(.*)*",
    name: "NotFound",
    component: NotFound,
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior() {
    return { top: 0 };
  },
});

// Authentication guard
router.beforeEach((to, from, next) => {
  const authToken = localStorage.getItem("auth_token");
  const user = JSON.parse(localStorage.getItem("user"));
  const isAuthenticated = !!authToken && !!user;
  const userRole = user?.role;

  // Prevent authenticated users from accessing guest routes
  if (to.meta.requiresGuest && isAuthenticated) {
    next("/"); // Redirect to home
    return;
  }

  // Check protected routes
  if (to.meta.requiresAuth) {
    if (!isAuthenticated) {
      next("/login");
      return;
    }

    // Admin check
    if (to.meta.requiresAdmin && userRole !== "admin") {
      next("/");
      return;
    }

    // Role-based access
    if (to.meta.role && userRole !== to.meta.role) {
      next("/");
      return;
    }
  }

  next();
});

export default router;
