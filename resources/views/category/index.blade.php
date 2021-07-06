@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Categories <small>List All Categories</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li class="active"><span>Categories</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Sections</h3>

                <div class="filter-block pull-right">
                    <a href="{{ route('category.create') }}" class="btn btn-primary pull-right">
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
                        @forelse ($categories as $category)
                            <tr>
                                <td>
                                    {!! $category->present()->icon !!}
                                    <a href="{{ route('item.index', $category->id) }}">{{ $category->title }}</a>
                                </td>

                                <td>
                                    {{ count($category->fields) }}
                                </td>

                                <td>
                                    @can('admin')
                                        <a href="{{ route('users.edit', $category->owner->id) }}">
                                            {{ $category->owner->name }}
                                        </a>
                                    @else
                                        {{ $category->owner->name }}
                                    @endcan
                                </td>

                                <td style="width: 1%; white-space: nowrap;">
                                    <a href="{{ route('category.show', $category->id) }}" class="table-link" title="View">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>

                                    @can('share', $category)
                                    <a href="#" class="table-link share-item" title="Share" data-id="{{ $category->id }}" data-human-id="{{ $category->title }}">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-share-alt fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>
                                    @endcan

                                    @can('manage', $category)
                                    <a href="{{ route('category.edit', $category->id) }}" class="table-link">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>
                                    @endcan

                                    @can('owner', $category)
                                    {!! Form::open(['route' => ['category.destroy', $category->id], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                                    <a href="#" class="table-link danger" onclick="if (confirm('Are you sure you want to delete this category?')) $(this).closest('form').submit(); return false;">
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
                                <td colspan="4" style="text-align:center;">
                                    There are currently no categories added. How about <a href="{{ route('category.create') }}">creating one</a> now?
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {!! $categories->render() !!}
            </div>
        </div>
    </section>

    @include('partials._share-modal', [
        'resource_type' => 'category',
        'create_permissions' => true,
    ])
@stop
