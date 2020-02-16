import Vue from 'vue';
import App from './domains/app/components/App.vue';
import router from './router';
import store from './store';
import './registerServiceWorker';

import { ApiClient } from '@/domains/shared/apiClient';
import HelperFactory from '@/domains/shared/helpers/helperFactory';
import RequestFactory from '@/domains/shared/http/requestFactory/requestFactory';

// START OF FONT AWESOME ----------------------------
import { library } from '@fortawesome/fontawesome-svg-core';
import { faArrowUp } from '@fortawesome/free-solid-svg-icons';
import { faCheck } from '@fortawesome/free-solid-svg-icons';
import { faTimes } from '@fortawesome/free-solid-svg-icons';
import { faSpinner } from '@fortawesome/free-solid-svg-icons';
import { faEllipsisV } from '@fortawesome/free-solid-svg-icons';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { Environment } from '@/domains/shared/enums/Environment';
import { initialiseLogger } from '@/domains/shared/logger/getLogger';
import { setRequestFactory } from '@/domains/shared/http/requestFactory/getRequestFactory';
import { StoreProvider } from '@/domains/shared/storeProvider';
import { WebSocketManager } from '@/domains/shared/webSockets/webSocketManager';

library.add(faArrowUp);
library.add(faCheck);
library.add(faTimes);
library.add(faSpinner);
library.add(faEllipsisV);

Vue.component('font-awesome-icon', FontAwesomeIcon);
Vue.config.productionTip = false;

const BASE_URL = process.env.VUE_APP_BASE_URL || 'https://pandapoker.chapmandigital.co.uk';
const API_URL = process.env.VUE_APP_API_URL || 'https://pandapoker.chapmandigital.co.uk/api';
const apiClient = new ApiClient(API_URL);

const environmentName: keyof typeof Environment = process.env.VUE_APP_ENVIRONMENT || 'Production';
const environment: Environment = Environment[environmentName];

const helperFactory = new HelperFactory(BASE_URL, environment, store);
const requestFactory = new RequestFactory(apiClient);
const storeProvider = new StoreProvider(store);

// If the user already has a login token, update it in the request factory.
const userToken = store.state.user.token;
if (userToken && userToken.length > 0) {
  requestFactory.updateToken(userToken);
}

setRequestFactory(requestFactory);
initialiseLogger(environment);

Vue.use(apiClient);
Vue.use(requestFactory);
Vue.use(helperFactory);
Vue.use(storeProvider);

const WEB_SOCKET_HOST = process.env.VUE_APP_WEBSOCKET_HOST || 'pandapoker.chapmandigital.co.uk';
const WEB_SOCKET_PORT = process.env.VUE_APP_WEBSOCKET_PORT || 8082;

const webSocketManager = new WebSocketManager(WEB_SOCKET_HOST, WEB_SOCKET_PORT, storeProvider);
webSocketManager.init();

new Vue({
  router,
  store,
  render: (h) => h(App),
}).$mount('#app');
