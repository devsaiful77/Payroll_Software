import "./bootstrap";

// Import Vue
import { createApp } from 'vue';
// Import Components
import ExampleComponent from './components/ExampleComponent.vue';

import { 
    AccountModulesProducts, 
    AccountModulesSalesAdd,
    AccountModulesSalesEdit,
} from '../../Modules/Account/Resources/assets/js/app';


// Create Vue Instance
const app = createApp({});

// Create Components
app.component('example-component', ExampleComponent);
app.component('account-modules-products', AccountModulesProducts);
app.component('account-modules-sale-add', AccountModulesSalesAdd);
app.component('account-modules-sale-edit', AccountModulesSalesEdit);

// mounted components 
app.mount('#app'); 
