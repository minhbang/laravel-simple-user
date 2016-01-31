@extends('backend.layouts.main')
@section('content')
<div id="user-manage-tools" class="hidden">
    <div class="dataTables_toolbar">
        {!! Html::linkButton('#', trans('common.search'), ['class'=>'advanced_search_collapse','type'=>'info', 'size'=>'xs', 'icon' => 'search']) !!}
        {!! Html::linkButton('#', trans('common.all'), ['class'=>'filter-clear', 'type'=>'warning', 'size'=>'xs', 'icon' => 'list']) !!}
        {!! Html::modalButton(
            route('backend.user.create'),
            trans('user::user.create'),
            [
                'title' => trans('user::user.create'),
                'label' => trans('common.save'),
                'icon'  => 'fa-user',
            ],
            ['type'=>'success', 'size'=>'xs', 'icon' => 'plus-sign']
        ) !!}
    </div>
    <div class="bg-warning dataTables_advanced_search">
        <form class="form-horizontal" role="form">
            {!! Form::hidden('search_form', 1) !!}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('search_created_at', trans('common.created_at'), ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9">
                            {!! Form::daterange('search_created_at', [], ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('search_updated_at', trans('common.updated_at'), ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9">
                            {!! Form::daterange('search_updated_at', [], ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="ibox ibox-table">
    <div class="ibox-title">
        <h5>{!! trans('user::user.manage_title') !!}</h5>
        <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
    </div>
    <div class="ibox-content">
    {!! $table->render('_datatable') !!}
    </div>
</div>
@stop

@section('script')
<script type="text/javascript">
    function datatableDrawCallback(oTable) {
        oTable.find('a.quick-update').quickUpdate({
            url: '{{ route('backend.user.quick_update', ['user' => '__ID__']) }}',
            container: '#user-manage',
            dataTable: oTable
        });
    };
</script>
    @include(
        '_datatable_script',
        [
            'name' => trans('user::user.user'),
            'data_url' => route('backend.user.data'),
            'drawCallback' => 'window.datatableDrawCallback'
        ]
    )
@stop