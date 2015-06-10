<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>VATSIM Virtual Airline System 1.0</title>
    {{ HTML::style('bootstrap/css/bootstrap.css') }}
    {{ HTML::style('css/flat-ui.css') }}
    {{ HTML::style('css/custom.css') }}
    {{ HTML::style('css/plugins/font-awesome/css/font-awesome.css') }}
    <!-- Vatsim Favicon -->
    <link rel="shortcut icon" href="images/favicon.ico">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
    <script src="{{ URL::to('/') }}/js/html5shiv.js"></script>
    <![endif]-->
</head>
<body>
<div id="container" class="container">
    @if (Session::get('topmessage') != "")
        <div style="margin-top: 35px;" class="row">
            <div class="span12">
                <div class="alert alert-warning">{{{ Session::get('topmessage') }}}</div>
            </div>
        </div>
    @endif
    <div class="demo-headline">
        <h1 id="title" class="demo-logo">
            <!--<div class="logo"></div>-->
            Maintenance
            <small>VASOPS 1.0</small>
        </h1>
    </div>
    <div class="row">
        <div class="span12">
            <pre>
                <strong><i class="fa fa-code fa-fw"></i> Status Log: </strong><br />
                <span style="font-style: italic">0848z: </span>Estimated time of completion updated to 1125z.
                <span style="font-style: italic">0725z: </span>Site taken offline for maintenance. Estimated time of completion now 0825z.
                                                                            <strong>-Colin Schoen (1095510)</strong>
            </pre>
        </div>
    </div>
@include('core.footer')
