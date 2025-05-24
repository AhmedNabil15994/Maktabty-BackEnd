@extends('apps::dashboard.layouts.app')
@section('title', __('service::dashboard.services.routes.update'))
@section('content')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <a href="{{ url(route('dashboard.home')) }}">{{ __('apps::dashboard.home.title') }}</a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="{{ url(route('dashboard.services.index')) }}">
                            {{ __('service::dashboard.services.routes.index') }}
                        </a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="#">{{ __('service::dashboard.services.routes.update') }}</a>
                    </li>
                </ul>
            </div>

            <h1 class="page-title"></h1>

            <div class="row">
                <form id="updateForm" page="form" class="form-horizontal form-row-seperated" method="post"
                    enctype="multipart/form-data" action="{{ route('dashboard.services.update', $service->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="col-md-12">

                        {{-- RIGHT SIDE --}}
                        <div class="col-md-3">
                            <div class="panel-group accordion scrollable" id="accordion2">
                                <div class="panel panel-default">
                                    {{-- <div class="panel-heading">
                                        <h4 class="panel-title"><a class="accordion-toggle"></a></h4>
                                    </div> --}}
                                    <div id="collapse_2_1" class="panel-collapse in">
                                        <div class="panel-body">
                                            <ul class="nav nav-pills nav-stacked">
                                                <li class="active">
                                                    <a href="#global_setting" data-toggle="tab">
                                                        {{ __('service::dashboard.services.form.tabs.general') }}
                                                    </a>
                                                </li>
                                                <li class="">
                                                    <a href="#categories" id="categoriesTab" data-toggle="tab">
                                                        {{ __('service::dashboard.services.form.tabs.categories') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PAGE CONTENT --}}
                        <div class="col-md-9">
                            <div class="tab-content">

                                {{-- UPDATE FORM --}}
                                <div class="tab-pane active fade in" id="global_setting">

                                    <ul class="nav nav-pills">
                                        @foreach (config('translatable.locales') as $k => $code)
                                            <li class="{{ $code == locale() ? 'active' : '' }}">
                                                <a id="{{ $k }}-general-tab" data-toggle="tab"
                                                    aria-controls="general-tab-{{ $k }}"
                                                    href="#general-tab-{{ $k }}"
                                                    aria-expanded="{{ $code == locale() ? 'true' : 'false' }}">{{ $code }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content px-1 pt-1">

                                        @foreach (config('translatable.locales') as $k => $code)
                                            <div role="tabpanel" class="tab-pane {{ $code == locale() ? 'active' : '' }}"
                                                id="general-tab-{{ $k }}"
                                                aria-expanded="{{ $code == locale() ? 'true' : 'false' }}"
                                                aria-labelledby="{{ $k }}-general-tab">

                                                <div class="col-md-10">
                                                    <div class="form-group">
                                                        <label class="col-md-2">
                                                            {{ __('service::dashboard.services.form.title') }}
                                                            - {{ $code }}
                                                        </label>
                                                        <div class="col-md-9">
                                                            <input type="text" name="title[{{ $code }}]"
                                                                class="form-control" data-name="title.{{ $code }}"
                                                                value="{{ $service->getTranslation('title', $code) }}">
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        @endforeach


                                        <div class="col-md-10">

                                            <div class="form-group">
                                                <label class="col-md-2">
                                                    {{ __('service::dashboard.services.form.image') }}
                                                </label>
                                                <div class="col-md-9">
                                                    @include('core::dashboard.shared.file_upload', [
                                                        'image' => $service->image,
                                                    ])
                                                    <div class="help-block"></div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-2">
                                                    {{ __('service::dashboard.services.form.status') }}
                                                </label>
                                                <div class="col-md-9">
                                                    <input type="checkbox" class="make-switch" id="test"
                                                        data-size="small" name="status"
                                                        {{ $service->status == 1 ? ' checked="" ' : '' }}>
                                                    <div class="help-block"></div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="tab-pane fade in" id="categories">
                                    <div id="categoriesTabContent">
                                        <h3 class="page-title">
                                            {{ __('service::dashboard.services.form.tabs.categories') }}
                                        </h3>
                                        <div id="jstree">
                                            @include('service::dashboard.tree.services.edit', [
                                                'mainCategories' => $mainCategories,
                                            ])
                                        </div>
                                        <div class="form-group">
                                            <input type="hidden" name="service_category_id" id="root_category"
                                                value="" data-name="service_category_id">
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- END UPDATE FORM --}}

                            </div>
                        </div>

                        {{-- PAGE ACTION --}}
                        <div class="col-md-12">
                            <div class="form-actions">
                                @include('apps::dashboard.layouts._ajax-msg')
                                <div class="form-group">
                                    <button type="submit" id="submit" class="btn btn-lg green">
                                        {{ __('apps::dashboard.general.edit_btn') }}
                                    </button>
                                    <a href="{{ url(route('dashboard.services.index')) }}" class="btn btn-lg red">
                                        {{ __('apps::dashboard.general.back_btn') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('scripts')

    <script>
        $(function() {
            $('#jstree').jstree();

            $('#jstree').on("changed.jstree", function(e, data) {
                $('#root_category').val(data.selected);
            });
            $('span.select2-container').width('100%');
        });
    </script>

@endsection
