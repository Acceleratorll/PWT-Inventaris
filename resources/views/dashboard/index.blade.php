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
            <div class="justify-content-between">
                <canvas id="categoryChart" height="100"></canvas>
            </div>
                @foreach($categories as $category)
                @if ($category->id == 3)
                <div id="category_{{ $category->id }}">
                    <hr class="divider">
                    <div class="d-flex justify-content-between">
                        <span class="text-danger font-weight-bold">{{ $category->name }}</span>
                        <span class="text-danger font-weight-bold" id="qty" style="margin-right: 15px;">{{ $category->products->count() }}</span>
                    </div>
                </div>
                @else
                <div id="category_{{ $category->id }}">
                <hr class="divider">
                <div class="d-flex justify-content-between">
                    <span class="text-black">{{ $category->name }}</span>
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
            <div class="rounded shadow">
                <canvas id="rppChart" height="100"></canvas>
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
            <div class="rounded shadow">
                <canvas id="tintaChart" height="100"></canvas>
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
            
            
            var addDataChart = pusher.subscribe('public.add.chart.1')
            .bind("add.chart", (data) => {
                console.log(data);
                addChartData(data);
            });

            var updateDataChart = pusher.subscribe('public.update.chart.1')
            .bind("update.chart", (data) => {
                console.log(data);

                var chart = data.chart;
                if(chart == 'cChart'){
                    chart = cChart;
                    console.log('Label Index ', cChart.data.labels);
                    var labelIndex = cChart.data.labels.indexOf(data.data.name);
                    console.log('Label Index ', labelIndex);
                    
                    if (labelIndex !== -1) {
                        cChart.data.datasets[0].data[labelIndex] = data.data.qty;
                        console.log(cChart.data.datasets[0].data[labelIndex]);
                        
                        cChart.update();
                        console.log('Successfully updated');
                    }
                    
                    updateCategoryChartData(data);
                }else if(chart == 'tChart'){
                    chart = tChart;
                }else if(chart == 'rChart'){
                    chart = rChart;
                }

                
            });
            
            var deleteChart = pusher.subscribe('public.delete.chart.1')
            .bind("delete.chart", (data) => {
                console.log(data);
                deleteCategoryChartData(data);
            });
        
            var dataAdded = pusher.subscribe('public.data.added.1')
            .bind("data.added", (data) => {
                console.log(data);
                
                if(data.name == 'Category'){
                    addCategoryInfo(data);
                }

                toastAddData(data);
            });

            var dataDeleted = pusher.subscribe('public.deleted.data.1')
            .bind("deleted.data", (data) => {
                console.log(data);
                
                if(data.name == 'Category'){
                    var categoryToDelete = document.getElementById('category_' + data.data.id);
                    console.log("Category to delete: ", categoryToDelete);
                    
                    if (categoryToDelete) {
                        categoryToDelete.remove();
                        console.log("Category removed");
                    }
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
                        console.log(data);
                        
                        const ctx = document.getElementById("tintaChart");
                        
                        if (tChart) {
                            tChart.destroy();
                        }
                        
                        tChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: data.datasets, // Use the datasets from the response
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
                                }
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
                        })
                    }
                })
            }
            
            function addCategoryInfo(newCategoryData) {
                var categoryInfoContainer = document.getElementById('categoryInfo');
                
                var categoryDiv = document.createElement('div');
                categoryDiv.className = 'd-flex justify-content-between';
                
                var categoryId = 'category_' + newCategoryData.data.id;
                categoryDiv.id = categoryId;
                console.log(newCategoryData);
                
                var categoryNameSpan = document.createElement('span');
                categoryNameSpan.className = 'text-black';
                categoryNameSpan.textContent = newCategoryData.data.name;
                
                var categoryCountSpan = document.createElement('span');
                categoryCountSpan.className = 'text-black';
                categoryCountSpan.style.marginRight = '15px';
                categoryCountSpan.textContent = newCategoryData.data.count;
                
                categoryDiv.appendChild(categoryNameSpan);
                categoryDiv.appendChild(categoryCountSpan);
                
                categoryInfoContainer.appendChild(categoryDiv);
            }
            
            function addChartData(data){
                var chart = data.chart;
                
                var chartInstance = null;
                if (chart === 'cChart') {
                    chartInstance = cChart;
                } else if (chart === 'tChart') {
                    chartInstance = tChart;
                } else if (chart === 'rChart') {
                    chartInstance = rChart;
                }
                
                if (chartInstance) {
                    chartInstance.data.labels.push(data.label);
                    chartInstance.data.datasets[0].data.push(data.data);
                    chartInstance.update();
                }
            }

            function updateCategoryChartData(data) {
                var categoryToUpdate = document.getElementById('category_' + data.data.id);
                var totalToUpdate = document.getElementById('total');
                if (categoryToUpdate) {
                    var quantityElement = categoryToUpdate.querySelector('#qty');
                    var quantityOfTotalElement = totalToUpdate.querySelector('#qty');
                    if (quantityElement && quantityOfTotalElement && data.data.context == 'create') {
                        quantityElement.textContent = parseInt(quantityElement.textContent) + 1;
                        console.log(quantityElement.textContent);
                        quantityOfTotalElement.textContent = parseInt(quantityOfTotalElement.textContent)+1;
                    }
                }
            }

            function deleteCategoryChartData(data){
                var chart = data.chart;
                
                var chartInstance = null;
                if (chart === 'cChart') {
                    chartInstance = cChart;
                } else if (chart === 'tChart') {
                    chartInstance = tChart;
                } else if (chart === 'rChart') {
                    chartInstance = rChart;
                }
                console.log('Chart Instance ',chartInstance);
                
                if (chartInstance) {
                    var dataIndex = chartInstance.data.labels.indexOf(data.label);
                    if (dataIndex !== -1) {
                        chartInstance.data.labels.splice(dataIndex, 1);
                        chartInstance.data.datasets[0].data.splice(dataIndex, 1);
                        chartInstance.update();
                        console.log('data removed from chart');
                    }
                }
            }
            
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
            
            function toastAddData(data){
                Swal.fire({
                    position: 'top-end',
                    type: 'success',
                    title: data.name+' Deleted',
                    text: data.name+' dengan nama "'+data.data.name+'" just Added !',
                    showConfirmButton: false,
                    timer: 3300,
                    timerProgressBar: true
                })
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
                    pageLength: 8,
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

