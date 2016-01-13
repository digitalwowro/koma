@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li class="active"><span>Tables</span></li>
            </ol>

            <h1>Tables <small>Secondary headline</small></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">Orders</h2>

                    <div id="reportrange" class="pull-right daterange-filter">
                        <i class="icon-calendar"></i>
                        <span></span> <b class="caret"></b>
                    </div>
                </header>

                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th><a href="#"><span>Order ID</span></a></th>
                                <th><a href="#" class="desc"><span>Date</span></a></th>
                                <th><a href="#" class="asc"><span>Customer</span></a></th>
                                <th class="text-center"><span>Status</span></th>
                                <th class="text-right"><span>Price</span></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <a href="#">#8002</a>
                                </td>
                                <td>
                                    2013/08/08
                                </td>
                                <td>
                                    <a href="#">Robert De Niro</a>
                                </td>
                                <td class="text-center">
                                    <span class="label label-success">Completed</span>
                                </td>
                                <td class="text-right">
                                    &dollar; 825.50
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="#">#5832</a>
                                </td>
                                <td>
                                    2013/08/08
                                </td>
                                <td>
                                    <a href="#">John Wayne</a>
                                </td>
                                <td class="text-center">
                                    <span class="label label-warning">On hold</span>
                                </td>
                                <td class="text-right">
                                    &dollar; 923.93
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="#">#2547</a>
                                </td>
                                <td>
                                    2013/08/08
                                </td>
                                <td>
                                    <a href="#">Anthony Hopkins</a>
                                </td>
                                <td class="text-center">
                                    <span class="label label-primary">Pending</span>
                                </td>
                                <td class="text-right">
                                    &dollar; 1.625.50
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="#">#9274</a>
                                </td>
                                <td>
                                    2013/08/08
                                </td>
                                <td>
                                    <a href="#">Charles Chaplin</a>
                                </td>
                                <td class="text-center">
                                    <span class="label label-danger">Cancelled</span>
                                </td>
                                <td class="text-right">
                                    &dollar; 35.34
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="#">#8463</a>
                                </td>
                                <td>
                                    2013/08/08
                                </td>
                                <td>
                                    <a href="#">Gary Cooper</a>
                                </td>
                                <td class="text-center">
                                    <span class="label label-success">Completed</span>
                                </td>
                                <td class="text-right">
                                    &dollar; 34.199.99
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <ul class="pagination pull-right">
                        <li><a href="#"><i class="fa fa-chevron-left"></i></a></li>
                        <li><a href="#">1</a></li>
                        <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">4</a></li>
                        <li><a href="#">5</a></li>
                        <li><a href="#"><i class="fa fa-chevron-right"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
