@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Device Sections <small>List All Device Sections</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li class="active"><span>Device Sections</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Sections</h3>

                <div class="filter-block pull-right">
                    <a href="{{ route('device-section.create') }}" class="btn btn-primary pull-right">
                        <i class="fa fa-plus-circle fa-lg"></i> Add section
                    </a>
                </div>
            </div>

            <div class="box-body">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Number of fields</th>
                        <th>Owner</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                        @php $visible = 0; @endphp
                        @foreach ($deviceSections as $deviceSection)
                            @can('list', $deviceSection)
                                @php $visible++; @endphp
                                <tr>
                                    <td>
                                        {!! $deviceSection->present()->icon !!}
                                        <a href="{{ route('device.index', $deviceSection->id) }}">{{ $deviceSection->title }}</a>
                                    </td>

                                    <td>
                                        {{ count($deviceSection->fields) }}
                                    </td>

                                    <td>
                                        @can('superadmin')
                                            <a href="{{ route('users.edit', $deviceSection->owner->id) }}">
                                                {{ $deviceSection->owner->name }}
                                            </a>
                                        @else
                                            {{ $deviceSection->owner->name }}
                                        @endcan
                                    </td>

                                    <td style="width: 1%; white-space: nowrap;">
                                        @can('manage', $deviceSection)
                                        <a href="{{ route('device-section.edit', $deviceSection->id) }}" class="table-link">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        @endcan

                                        @can('share', $deviceSection)
                                        <a href="{{ route('device-section.share', $deviceSection->id) }}" class="table-link share-item" title="Share" data-human-id="{{ $deviceSection->title }}">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-share-alt fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        @endcan

                                        @can('owner', $deviceSection)
                                        {!! Form::open(['route' => ['device-section.destroy', $deviceSection->id], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                                        <a href="#" class="table-link danger" onclick="if (confirm('Are you sure you want to delete this device section?')) $(this).closest('form').submit();">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        {!! Form::close() !!}
                                        @endcan
                                    </td>
                                </tr>
                            @endcan
                        @endforeach

                        @if (empty($visible))
                            <tr>
                                <td colspan="4" style="text-align:center;">
                                    There are currently no device sections added. How about <a href="{{ route('device-section.create') }}">creating one</a> now?
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                {!! $deviceSections->render() !!}
            </div>
        </div>
    </section>

    @include('partials._share-modal', [
        'resource_type' => App\Permission::RESOURCE_TYPE_DEVICE_SECTION,
        'create_permissions' => true,
    ])
@stop

@section('footer')
    <script>
        $.sharer = sharerUtil.init({
            type: 'device section',
        });
    </script>
@append
