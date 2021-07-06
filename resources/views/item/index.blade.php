@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Devices <small>{{ $category->title }}</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><span>Devices</span></li>
            @if (empty($categoryLabel))
                <li class="active"><span>{{ $category->title }}</span></li>
            @else
                <li><a href="{{ route('item.index', $type) }}"><span>{{ $category->title }}</span></a></li>
                <li class="active"><span>{{ $categoryLabel }}</span></li>
            @endif
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">All {{ $category->title }}</h3>

                <div class="filter-block pull-right">
                    <?php $ithColumn = 0; ?>

                    @if (is_array($category->fields))
                        @foreach ($category->fields as $field)
                            @if ($field->showInDeviceList())
                                @if ($field->showFilter())
                                    <div class="multi-select pull-left" data-opener-id="{{ $field->getInputName() }}" data-ith="{{ $ithColumn }}">
                                        <a href="#" class="btn btn-default btn-sm" data-filter-name="{{ $field->getName() }}">
                                            <i class="fa fa-square-o"></i>
                                            {{ $field->getName() }}
                                            <i class="fa fa-chevron-down"></i>
                                        </a>

                                        <div class="multi-choices" data-auto-close="{{ $field->getInputName() }}">
                                            @foreach ($field->getNiceOptions() as $choice)
                                                <label>
                                                    <input type="checkbox" value="{{ $choice['label'] }}"{{ !isset($filters[$field->getInputName()]) || in_array($choice['label'], $filters[$field->getInputName()]) ? ' checked' : '' }}>
                                                    <span class="label label-{{ $choice['type'] }}">
                                                    {{ $choice['label'] }}
                                                </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <?php $ithColumn++; ?>
                            @endif
                        @endforeach
                    @endif

                    @can('create', $category)
                        @if (empty($categoryLabel))
                            <a href="{{ route('item.create', $category->id) }}" class="btn btn-primary btn-sm pull-left">
                        @else
                            <a href="{{ route('item.create', ['type' => $category->id, 'category' => $categoryId]) }}" class="btn btn-primary pull-left">
                                @endif
                                <i class="fa fa-plus-circle fa-lg"></i> Add device
                            </a>
                        @endcan
                </div>
            </div>

            <div class="box-body">
                <table class="table table-responsive table-hover table-striped{{ $items->count() ? ' datatable' : '' }}">
                    <thead>
                    <tr>
                        @foreach ($category->fields as $field)
                            @if ($field->showInDeviceList())
                                <th>{{ $field->getName() }}</th>
                            @endif
                        @endforeach

                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($items as $item)
                        <tr>
                            @foreach ($category->fields as $field)
                                @if ($field->showInDeviceList())
                                    <td>
                                        @if (method_exists($field, 'customDeviceListContent'))
                                            {!! $field->customDeviceListContent($item) !!}
                                        @elseif (isset($item->data[$field->getInputName()]))
                                            @if (is_array($item->data[$field->getInputName()]))
                                                {!! urlify(implode(', ', $item->data[$field->getInputName()])) !!}
                                            @else
                                                {!! urlify($item->data[$field->getInputName()]) !!}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endif
                            @endforeach

                            <td style="width: 1%; white-space: nowrap;">
                                <a href="{{ route('item.show', $item->id) }}" class="table-link" title="View">
                                    <span class="fa-stack">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>

                                @can('share', $item)
                                <a href="#" class="table-link share-item" title="Share" data-id="{{ $item->id }}" data-human-id="{{ $item->present()->humanIdField($category) }}">
                                    <span class="fa-stack">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-share-alt fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>
                                @endcan

                                @can('update', $item)
                                <a href="{{ route('item.edit', $item->id) }}" class="table-link" title="Edit">
                                    <span class="fa-stack">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>
                                @endcan

                                @can('delete', $item)
                                {!! Form::open(['route' => ['item.destroy', $item->id], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                                <a href="#" class="table-link danger" onclick="if (confirm('Are you sure you want to delete this item?')) $(this).closest('form').submit(); return false;" title="Delete">
                                    <span class="fa-stack">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>
                                {!! Form::close() !!}
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr class="norows">
                            <td colspan="{{ $colspan }}" style="text-align:center;">
                                There are currently no items added. How about

                                @if (empty($categoryLabel))
                                    <a href="{{ route('item.create', $category->id) }}">creating one</a>
                                @else
                                    <a href="{{ route('item.create', ['type' => $category->id, 'category' => $categoryId]) }}">creating one</a>
                                @endif

                                now?
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @include('partials._share-modal', [
        'resource_type' => 'device',
        'create_permissions' => false,
    ])
@stop
