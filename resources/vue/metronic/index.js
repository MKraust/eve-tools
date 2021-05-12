//
// 3rd-Party Plugins JavaScript Includes
//

//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
////  Mandatory Plugins Includes(do not remove or change order!)  ////
//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////

// Jquery - jQuery is a popular and feature-rich JavaScript library. Learn more: https://jquery.com/
window.jQuery = window.$ = require('jquery');

// Bootstrap - The most popular framework uses as the foundation. Learn more: http://getbootstrap.com
require('bootstrap');

// Popper.js - Tooltip & Popover Positioning Engine used by Bootstrap. Learn more: https://popper.js.org
window.Popper = require('popper.js').default;

// Wnumb - Number & Money formatting. Learn more: https://refreshless.com/wnumb/
window.wNumb = require('wnumb');

// Moment - Parse, validate, manipulate, and display dates and times in JavaScript. Learn more: https://momentjs.com/
window.moment = require('moment');

// ES6-Shim - ECMAScript 6 compatibility shims for legacy JS engines.  Learn more: https://github.com/paulmillr/es6-shim
require(`es6-shim/es6-shim.min.js`);

// Perfect-Scrollbar - Minimalistic but perfect custom scrollbar plugin.  Learn more:  https://github.com/mdbootstrap/perfect-scrollbar
window.PerfectScrollbar = require('perfect-scrollbar/dist/perfect-scrollbar');

// jQuery BlockUI - The jQuery BlockUI Plugin lets you simulate synchronous behavior when using AJAX: http://malsup.com/jquery/block/
require('block-ui/jquery.blockUI.js');

//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
///  Optional Plugins Includes(you can remove or add)  ///////////////
//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////

// Sweetalert2 - a beautiful, responsive, customizable and accessible (WAI-ARIA) replacement for JavaScript's popup boxes: https://sweetalert2.github.io/
window.Swal = window.swal = require('sweetalert2/dist/sweetalert2.min.js');
require('@/metronic/js/vendors/plugins/sweetalert2.init.js');

// Keenthemes' plugins
window.KTUtil = require('@/metronic/js/components/util.js');
window.KTApp = require('@/metronic/js/components/app.js');
window.KTCard = require('@/metronic/js/components/card.js');
window.KTCookie = require('@/metronic/js/components/cookie.js');
window.KTDialog = require('@/metronic/js/components/dialog.js');
window.KTHeader = require('@/metronic/js/components/header.js');
window.KTImageInput = require('@/metronic/js/components/image-input.js');
window.KTMenu = require('@/metronic/js/components/menu.js');
window.KTOffcanvas = require('@/metronic/js/components/offcanvas.js');
window.KTScrolltop = require('@/metronic/js/components/scrolltop.js');
window.KTToggle = require('@/metronic/js/components/toggle.js');
window.KTWizard = require('@/metronic/js/components/wizard.js');
require('@/metronic/js/components/datatable/core.datatable.js');
require('@/metronic/js/components/datatable/datatable.checkbox.js');

// Metronic layout base js
window.KTLayoutAside = require('@/metronic/js/layout/base/aside.js');
window.KTLayoutAsideMenu = require('@/metronic/js/layout/base/aside-menu.js');
window.KTLayoutAsideToggle = require('@/metronic/js/layout/base/aside-toggle.js');
window.KTLayoutBrand = require('@/metronic/js/layout/base/brand.js');
window.KTLayoutContent = require('@/metronic/js/layout/base/content.js');
window.KTLayoutFooter = require('@/metronic/js/layout/base/footer.js');
window.KTLayoutHeader = require('@/metronic/js/layout/base/header.js');
window.KTLayoutHeaderMenu = require('@/metronic/js/layout/base/header-menu.js');
window.KTLayoutHeaderTopbar = require('@/metronic/js/layout/base/header-topbar.js');
window.KTLayoutStickyCard = require('@/metronic/js/layout/base/sticky-card.js');
window.KTLayoutStretchedCard = require('@/metronic/js/layout/base/stretched-card.js');
window.KTLayoutSubheader = require('@/metronic/js/layout/base/subheader.js');

// Metronic layout extended js
window.KTLayoutChat = require('@/metronic/js/layout/extended/chat.js');
window.KTLayoutDemoPanel = require('@/metronic/js/layout/extended/demo-panel.js');
window.KTLayoutExamples = require('@/metronic/js/layout/extended/examples.js');
window.KTLayoutQuickActions = require('@/metronic/js/layout/extended/quick-actions.js');
window.KTLayoutQuickCartPanel = require('@/metronic/js/layout/extended/quick-cart.js');
window.KTLayoutQuickNotifications = require('@/metronic/js/layout/extended/quick-notifications.js');
window.KTLayoutQuickPanel = require('@/metronic/js/layout/extended/quick-panel.js');
window.KTLayoutQuickSearch = require('@/metronic/js/layout/extended/quick-search.js');
window.KTLayoutQuickUser = require('@/metronic/js/layout/extended/quick-user.js');
window.KTLayoutScrolltop = require('@/metronic/js/layout/extended/scrolltop.js');
window.KTLayoutSearch = window.KTLayoutSearchOffcanvas = KTLayoutSearchInline = require('@/metronic/js/layout/extended/search.js');

require('@/metronic/js/layout/initialize.js');
