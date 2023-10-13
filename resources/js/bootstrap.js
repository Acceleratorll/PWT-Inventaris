import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

import $ from "jquery";
import DataTable from "datatables.net-dt";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import Chart from "chart.js/auto";
import Swal from "sweetalert2";
import moment from "moment";

import "sweetalert2/src/sweetalert2.scss";
import "datatables.net-select-bs4";
import "datatables.net-searchpanes-bs4";
import "datatables.net-responsive-dt";

window.$ = $;
window.DataTable = DataTable;
window.Swal = Swal;
window.Chart = Chart;
window.Pusher = Pusher;
window.moment = moment;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});
