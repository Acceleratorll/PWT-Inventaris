@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content_header')
<h1>Dashboard</h1>
@stop

@section('content')
<div id="custom-target"></div>

<div class="row">
    <div class="col-md-4">
        <x-adminlte-card title="Info Barang" theme="lightblue" theme-mode="outline" icon="fas fa-chart-pie" header-class="text-uppercase rounded-bottom border-info" removable>
            <div class="justify-center" id="chart-category">
                <canvas id="categoryChart"></canvas>
            </div>
                @foreach($categories as $category)
                @if ($category->id == $unused->id)
                <div id="category_{{ $category->id }}">
                    <hr class="divider">
                    <div class="d-flex justify-content-between">
                        <span class="text-danger font-weight-bold" id="name">{{ $category->name }}</span>
                        <span class="text-danger font-weight-bold" id="qty" style="margin-right: 15px;">{{ $category->products->count() }}</span>
                    </div>
                </div>
                @else
                <div id="category_{{ $category->id }}">
                <hr class="divider">
                <div class="d-flex justify-content-between">
                    <span class="text-black" id="name">{{ $category->name }}</span>
                    <span class="text-black" id="qty" style="margin-right: 15px;">{{ $category->products->count() }}</span>
                </div>
                </div>
                @endif
                @endforeach
                <div id="categoryInfo"></div>
                <div id="category_{{ $category->id }}">
                <hr class="divider">
                <div class="d-flex justify-content-between" id="total">
                    <span class="text-black"><strong>Total Seluruh Barang</strong></span>
                    <span class="text-black" id="qty" style="margin-right: 15px;"><strong>{{ $total }}</strong></span>
                </div>
                </div>
        </x-adminlte-card>
    </div>
    <div class="col-md-8">
        <x-adminlte-card title="Info RPP Setahun" theme="lightblue" theme-mode="outline" icon="fas fa-chart-pie" header-class="text-uppercase rounded-bottom border-info" removable>
            <div class="rounded shadow justify-center" id="chart-rpp">
                <canvas id="rppChart"></canvas>
            </div>
        </x-adminlte-card>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <x-adminlte-card title="Unused Barang" icon="fas fa-trash" theme="danger" theme-mode="outline">
            <div class="d-flex justify-content-between">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table">
                        <thead class="thead-light" id="table">
                            <tr>
                                <th scope="col" class="text-center">ID</th>
                                <th scope="col" class="text-center">Name</th>
                                <th scope="col" class="text-center">Last Used</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </x-adminlte-card>
    </div>
    <div class="col-md-8">
        <x-adminlte-card title="Barang Setiap RPP" theme="lightblue" theme-mode="outline" icon="fas fa-chart-pie" header-class="text-uppercase rounded-bottom border-info" removable>
            <div class="rounded shadow justify-center" id="chart-tinta">
                <canvas id="tintaChart"></canvas>
            </div>
        </x-adminlte-card>
    </div>
</div>

<div class="row">
    <div class="col-md-4">

    </div>
    <div class="col-md-8">
        
    </div>
</div>

@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('css/chart.css') }}">
    @vite(['resources/css/app.css'])
    @stop
    
    @section('js')
    
    @vite(['resources/js/app.js'])
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            
            let tChart;
            let rChart;
            let cChart;
            let labels;
            let datas;
            
            var pusher = new Pusher('50e5010495e7225dbb70', {
                cluster: 'ap1'
            });
            
            
            var addChart = pusher.subscribe('public.add.chart.1')
            .bind("add.chart", (data) => {
                console.log(data);

                addDataChart(data);
            });

            var updateChart = pusher.subscribe('public.update.chart.1')
            .bind("update.chart", (data) => {
                console.log(data);

                updateDataChart(data);
            });
            
            var deleteChart = pusher.subscribe('public.delete.chart.1')
            .bind("delete.chart", (data) => {
                console.log(data);

                deleteDataChart(data);
            });
        
            var dataUpdated = pusher.subscribe('public.update.data.1')
            .bind("update.data", (data) => {
                console.log(data);
                
                if(data.name == 'Category'){
                    changeCategoryInfo(data);
                }else if(data.name == 'Product'){
                    affectedByProduct(data);
                }

                toastUpdateData(data);
            });

            var dataAdded = pusher.subscribe('public.data.added.1')
            .bind("data.added", (data) => {
                console.log(data);
                
                if(data.name == 'Category'){
                    changeCategoryInfo(data);
                }else if(data.name == 'Product'){
                    affectedByProduct(data);
                }
                
                toastAddData(data);
            });

            var dataDeleted = pusher.subscribe('public.deleted.data.1')
            .bind("deleted.data", (data) => {
                console.log(data);
                
                if(data.name == 'Category'){
                    changeCategoryInfo(data);
                }else if(data.name == 'Product'){
                    affectedByProduct(data);
                }
                
                toastDeleteData(data);
            });

            function getRandomColor(){
                const letters = '0123456789ABCDEF';
                let color = '#';
                for (let i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }
            
            function tintaChart() {
                $.ajax({
                    url: '/json/chart/tinta',
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        console.log(data.datasets);
                        
                        const ctx = document.getElementById("tintaChart");
                        
                        if (tChart) {
                            tChart.destroy();
                        }
                        
                        tChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: data.datasets,
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    }
                })
            }
            
            function rppChart(){
                $.ajax({    
                    url: '/json/chart/rpp',
                    method:'GET',
                    dataType: 'json',
                    success: function(data){
                        console.log(data);
                        labels = data.labels,
                        datas = data.datas;
                        const ctx = document.getElementById("rppChart");
                        
                        if(rChart){
                            rChart.destroy();
                        }
                        
                        rChart = new Chart(ctx,{
                            type: 'line',
                            data:{
                                labels: labels,
                                datasets: [{
                                    label: 'Total RPP',
                                    data: datas,
                                    fill: true,
                                    borderColor: 'rgb(75, 192, 192)',
                                    tension: 0.1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                responsive: true
                            }
                        })
                    }
                })
            }
            
            function categoryChart(){
                $.ajax({    
                    url: '/json/chart/category',
                    method:'GET',
                    dataType: 'json',
                    success: function(data){
                        console.log(data);
                        labels = data.labels,
                        datas = data.datas;
                        const ctx = document.getElementById("categoryChart");
                        
                        if(cChart){
                            cChart.destroy();
                        }
                        
                        cChart = new Chart(ctx,{
                            type: 'doughnut',
                            data:{
                                labels: labels,
                                datasets: [{
                                    label: 'Total Product',
                                    data: datas,
                                    fill: true,
                                    backgroundColor: ['#33FF57', '#FFAA33', '#FF0000', '#3366FF', '#FF33E9', '#FF5733', '#33FFF7', '#FF3366', '#33FF33'],
                                    tension: 0.1
                                }]
                            },
                            maintainAspectRatio: false,
                        })
                    }
                })
            }
            
            // Category Details
            function changeCategoryInfo(data) {
                var categoryToUpdate = document.getElementById('category_' + data.data.id);
                
                if(data.data.context == 'create'){
                    var categoryInfoContainer = document.getElementById('categoryInfo');
                
                    var categoryDiv = document.createElement('div');
                    categoryDiv.className = 'd-flex justify-content-between';
                    
                    categoryDiv.id = 'category_' + data.data.id;
                    
                    var categoryNameSpan = document.createElement('span');
                    categoryNameSpan.className = 'text-black';
                    categoryNameSpan.textContent = data.data.name;
                    categoryNameSpan.id = 'name';
                    
                    var categoryCountSpan = document.createElement('span');
                    categoryCountSpan.className = 'text-black';
                    categoryCountSpan.style.marginRight = '15px';
                    categoryCountSpan.textContent = data.data.qty;
                    categoryCountSpan.id = 'qty';
                    
                    categoryDiv.appendChild(categoryNameSpan);
                    categoryDiv.appendChild(categoryCountSpan);
                    
                    categoryInfoContainer.appendChild(categoryDiv);
                }else if(data.data.context == 'update'){
                    var nameElement = categoryToUpdate.querySelector('#name');
                    if(nameElement){
                        nameElement.textContent = data.data.newName;
                    }
                }else if(data.data.context == 'delete'){
                    console.log("Category to delete: ", categoryToUpdate);
                    
                    categoryToUpdate.remove();
                    console.log("Category removed");
                }
            }

            // Product
            function affectedByProduct(data) {
                var categoryToUpdate = document.getElementById('category_' + data.data.id);
                var totalToUpdate = document.getElementById('total');
                if (categoryToUpdate) {
                    var quantityElement = categoryToUpdate.querySelector('#qty');
                    var quantityOfTotalElement = totalToUpdate.querySelector('#qty');
                    if(data.data.context == 'create'){
                        if (quantityElement && quantityOfTotalElement) {
                            quantityElement.textContent = parseInt(quantityElement.textContent) + 1;
                            console.log(quantityElement.textContent);
                            quantityOfTotalElement.textContent = parseInt(quantityOfTotalElement.textContent) + 1;
                        }
                        
                        console.log(cChart);

                        var labelIndex = cChart.data.labels.indexOf(data.data.name);
                        if (labelIndex !== -1) {
                            cChart.data.datasets[0].data[labelIndex] = data.data.qty;
                            cChart.update();
                            console.log('Successfully updated');
                        }
                    }else if(data.data.context == 'update'){
                        if (quantityElement && quantityOfTotalElement) {
                            quantityElement.textContent = data.data.qty;
                            console.log(quantityElement.textContent);
                        }
                        
                        var labelIndex = cChart.data.labels.indexOf(data.data.name);
                        if (labelIndex !== -1) {
                            cChart.data.datasets[0].data[labelIndex] = data.data.qty;
                            cChart.update();
                            console.log('Successfully updated');
                        }
                    }else if(data.data.context == 'delete'){
                        if (quantityElement && quantityOfTotalElement) {
                            quantityElement.textContent = parseInt(quantityElement.textContent) - 1;
                            console.log(quantityElement.textContent);
                            quantityOfTotalElement.textContent = parseInt(quantityOfTotalElement.textContent) - 1;
                        }

                        var labelIndex = cChart.data.labels.indexOf(data.data.name);
                        if (labelIndex !== -1) {
                            cChart.data.datasets[0].data[labelIndex] = data.data.qty;
                            cChart.update();
                            console.log('cChart Successfully updated');
                        }
                    }
                }
            }

            // CHART
            function updateDataChart(data){
                var chartInstance = null;
                if(data.chart == 'cChart'){
                    chartInstance = cChart;
                }else if(data.chart == 'tChart'){
                    chartInstance = tChart;
                }else if(data.chart == 'rChart'){
                    chartInstance = rChart;
                }

                console.log('Data Label ', data.data.name, 'QTY : ', data.data.qty);
                var labelIndex = chartInstance.data.labels.indexOf(data.data.name);
                console.log('Label Index ', labelIndex);
                if (labelIndex !== -1) {
                    if(data.chart == 'rChart'){
                        chartInstance.data.datasets[0].data[labelIndex] = data.data.qty;
                        console.log('Updated chart data', chartInstance.data.datasets[0].data[labelIndex]);
                        // if(data.data.context == 'delete'){
                        // }else if(data.data.context == 'add'){
                        //     chartInstance.data.datasets[index].data[labelIndex] = data.data.qty;
                        // }
                    }else{
                        if(data.data.newName){
                            chartInstance.data.labels[labelIndex] = data.data.newName;
                            
                        }else if(data.data.qty){
                            data.data.qty.forEach((qty, index) => {
                                chartInstance.data.datasets[index].data[labelIndex] = qty;
                            });
                        }
                    }
                    chartInstance.update();
                }
            }
        
            function addDataChart(data){
                var chartInstance = null;
                if(data.chart == 'cChart'){
                    chartInstance = cChart;
                }else if(data.chart == 'tChart'){
                    chartInstance = tChart;
                }

                console.log(data.data.qty);

                chartInstance.data.labels.push(data.data.name);
                data.data.qty.forEach((qty, index) => {
                    chartInstance.data.datasets[index].data.push(qty);
                });

                chartInstance.update();
            }

            function deleteDataChart(data){
                var chartInstance = null;

                if(data.chart == 'cChart'){
                    chartInstance = cChart;
                }else if(data.chart == 'tChart'){
                    chartInstance = tChart;
                }else if(data.chart == 'rChart'){
                    chartInstance = rChart;
                }
                
                var labelIndex = chartInstance.data.labels.indexOf(data.data.name);

                if (labelIndex !== -1) {
                    chartInstance.data.labels.splice(labelIndex, 1);
                    chartInstance.data.datasets[0].data.splice(labelIndex, 1);
                    chartInstance.update();
                    console.log(data.data.name + ' removed from chart');
                }
            }
            
            // TOAST
            function toastDeleteData(data){
                Swal.fire({
                    position: 'top-end',
                    type: 'warning',
                    title: data.name+' Deleted',
                    text: data.name+' dengan nama "'+data.data.name+'" just Deleted !',
                    showConfirmButton: false,
                    timer: 3300,
                    timerProgressBar: true
                })
            }
            
            function toastUpdateData(data){
                Swal.fire({
                    position: 'top-end',
                    type: 'success',
                    title: data.name+' Updated',
                    text: data.name+' dengan nama "'+data.data.name+'" just Updated !',
                    showConfirmButton: false,
                    timer: 3300,
                    timerProgressBar: true
                })
            }

            function toastAddData(data){
                Swal.fire({
                    position: 'top-end',
                    type: 'success',
                    title: data.name+' Added',
                    text: data.name+' dengan nama "'+data.data.name+'" just Added !',
                    showConfirmButton: false,
                    timer: 3300,
                    timerProgressBar: true
                })
            }

            function toastAmount(data){
                var type, title, text;
                
                if (data.amount < (0.1 * data.max_amount)) {
                    type = 'error';
                    title = data.name + ' Stocknya kurang';
                    text = data.name + ' dengan nama "' + data.data.name + '" dari 10% !';
                } else if(data.amount < (0.3 * data.max_amount)){
                    type = 'warning';
                    title = data.name + ' Stocknya kurang';
                    text = data.name + ' dengan nama "' + data.data.name + '" dari 30% !';
                }
                
                Swal.fire({
                    position: 'center',
                    type: type,
                    title: title,
                    text: text,
                    showConfirmButton: false,
                    showCloseButton: true,
                    confirmButtonText: 'Redirect',
                    confirmButtonColor: '#3085d6',
                    cancelButtonText: 'Close',
                    timer: 3300,
                    timerProgressBar: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/product';
                    }
                });
            }
            
            $(function() {
                categoryChart();
                rppChart();
                tintaChart();
                
                $('#table').DataTable({
                    processing: true,
                    serverSide: true,
                    lengthChange: false,
                    paginate: true,
                    pageLength: 5,
                    ajax: '{{ route('get-unused-products') }}',
                    columns: [
                        { data: 'id', name: 'id' },
                        { data: 'name', name: 'name' },
                        { data: 'last_used', name: 'last_used' },
                    ],
                });
            });
            
        });
    </script>
@stop

