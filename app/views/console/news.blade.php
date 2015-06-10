@include('console.core.header')
@include('console.core.navbartop')
@include('console.core.navbarside')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><i class="fa fa-newspaper-o fa-fw"></i> News Manager</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (Session::get('message') != '')
                <div class="alert alert-warning">{{{ Session::get('message') }}}</div>
            @endif
            @if (Session::get('error') != '')
                <div class="alert alert-danger">{{{ Session::get('error') }}}</div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div id="createNewsToggle" style="cursor: pointer;" class="panel-heading">
                    <i class="fa fa-newspaper-o fa-fw"></i> Add a news item <i class="fa fa-plus fa-fw"></i>
                </div>
                <div id="createNewsPanelBody" style="display: none;" class="panel-body">
                    <form role="form" action="{{ URL::route('consolecreatenews') }}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="form-group">
                            <input type="text" class="form-control" name="inputHeader" id="inputHeader" placeholder="Header..." />
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="inputBody" id="inputBody" placeholder="Body..."></textarea>
                        </div>
                        <div class="form-actions">
                            <input id="createNewsBtn" type="submit" class="btn btn-success" value="Submit" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-newspaper-o fa-fw"></i> Current News Items <span style="float: right;" class="label label-warning">{{ count($news) }}</span>
                </div>
                <div class="panel-body">
                    <div class="panel-group">
                        @foreach ($news as $n)
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    {{{ $n->header }}} <span style="float: right;">{{{ ConsoleUser::getName(Auth::consoleuser()->get()->cid) }}} <a href="{{ URL::route('consolenewsremove', $n->id) }}"><i class="fa fa-times fa-fw"></i></a></span>
                                </div>
                                <div class="panel-body">
                                    {{{ $n->body }}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('consolejs')
    $('#createNewsToggle').on('click', function() {
        $('#createNewsPanelBody').slideToggle();
    });
@endsection
@include('console.core.footer')