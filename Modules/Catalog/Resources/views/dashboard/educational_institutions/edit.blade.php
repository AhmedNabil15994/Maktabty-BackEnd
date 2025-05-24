@extends('apps::dashboard.layouts.app')
@section('title', __('catalog::dashboard.educational_institutions.routes.update'))
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
                        <a href="{{ url(route('dashboard.educational_institutions.index')) }}">
                            {{ __('catalog::dashboard.educational_institutions.routes.index') }}
                        </a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="#">{{ __('catalog::dashboard.educational_institutions.routes.update') }}</a>
                    </li>
                </ul>
            </div>

            <h1 class="page-title"></h1>

            <div class="row">
                <form id="updateForm" page="form" class="form-horizontal form-row-seperated" method="post"
                    enctype="multipart/form-data"
                    action="{{ route('dashboard.educational_institutions.update', $institution->id) }}">
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
                                                        {{ __('catalog::dashboard.educational_institutions.form.tabs.general') }}
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
                                    {{--                                    <h3 class="page-title">{{__('catalog::dashboard.educational_institutions.form.tabs.general')}}</h3> --}}

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
                                                            {{ __('catalog::dashboard.educational_institutions.form.title') }}
                                                            - {{ $code }}
                                                        </label>
                                                        <div class="col-md-9">
                                                            <input type="text" name="title[{{ $code }}]"
                                                                class="form-control" data-name="title.{{ $code }}"
                                                                value="{{ $institution->getTranslation('title', $code) }}">
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        @endforeach


                                        <div class="col-md-10">

                                            <div class="form-group">
                                                <label class="col-md-2">
                                                    {{ __('catalog::dashboard.educational_institutions.form.sort') }}
                                                </label>
                                                <div class="col-md-9">
                                                    <input type="number" name="sort" class="form-control"
                                                        data-name="sort" value="{{ $institution->sort }}">
                                                    <div class="help-block"></div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-2">
                                                    {{ __('catalog::dashboard.educational_institutions.form.image') }}
                                                </label>
                                                <div class="col-md-9">
                                                    @include('core::dashboard.shared.file_upload', [
                                                        'image' => $institution->image,
                                                    ])
                                                    <div class="help-block"></div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-2">
                                                    {{ __('catalog::dashboard.educational_institutions.form.status') }}
                                                </label>
                                                <div class="col-md-9">
                                                    <input type="checkbox" class="make-switch" id="test"
                                                        data-size="small" name="status"
                                                        {{ $institution->status == 1 ? ' checked="" ' : '' }}>
                                                    <div class="help-block"></div>
                                                </div>
                                            </div>

                                            @if ($institution->trashed())
                                                <div class="form-group">
                                                    <label class="col-md-2">
                                                        {{ __('catalog::dashboard.products.form.restore') }}
                                                    </label>
                                                    <div class="col-md-9">
                                                        <input type="checkbox" class="make-switch" id="test"
                                                            data-size="small" name="restore">
                                                        <div class="help-block"></div>
                                                    </div>
                                                </div>
                                            @endif

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
                                    <a href="{{ url(route('dashboard.educational_institutions.index')) }}"
                                        class="btn btn-lg red">
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

    <script></script>

@endsection
