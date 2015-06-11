@include('console.core.header')
@include('console.core.navbartop')
@include('console.core.navbarside')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><i class="fa fa-bar-chart-o fa-fw"></i> Global Statistics <small>coming soon...</small></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (Session::get('message') != '')
            <div class="alert alert-warning">{{{ Session::get('message') }}}</div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-responsive table-bordered">
                <tr><td><strong>Total Outbound Clicks:</strong> </td><td><span class="label label-info">{{{ number_format($count) }}}</span></td></tr>
                <tr><td><strong>Unique Outbound Clicks:</strong> </td><td><span class="label label-info">{{{ number_format($uniqueclicks) }}}</span></td></tr>
            </table>
        </div>
    </div>
</div>
@include('console.core.footer')