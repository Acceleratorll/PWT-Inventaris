import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

import DataTable from "datatables.net-dt";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import Chart from "chart.js/auto";
import Swal from "sweetalert2";
import select2 from "select2";
import moment from "moment";
import "datatables.net-searchpanes-bs4";
import "datatables.net-responsive-dt";

window.moment = moment;
window.select2 = select2;
window.Swal = Swal;
window.DataTable = DataTable;
window.Chart = Chart;
window.Pusher = Pusher;
window.Swal = Swal;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});
