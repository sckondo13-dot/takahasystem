

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import { initSiteSelector } from './site-selector';

initSiteSelector();

import { initInvoiceSiteSelector } from './invoice-site-selector';

initInvoiceSiteSelector();

