import { createApp } from "vue";
import "./style.css";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import App from "./App.vue";
import router from "./router/index.js";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

import { library } from "@fortawesome/fontawesome-svg-core";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import { createPinia } from "pinia";
// Import specific icons you want to use
import { 
  faBuilding, 
  faMapMarkerAlt, 
  faMoneyBillWave, 
  faClock,
  faArrowRight,
  // Add other icons you need
} from '@fortawesome/free-solid-svg-icons'

// Add icons to the library
library.add(
  faBuilding, 
  faMapMarkerAlt, 
  faMoneyBillWave, 
  faClock,
  faArrowRight
  // Add other icons here
)
const app = createApp(App);
app.component("font-awesome-icon", FontAwesomeIcon);

app.use(createPinia());
app.use(router);

app.mount("#app");
