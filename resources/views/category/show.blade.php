@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Category<small>View {{ Str::singular($category->title) }}</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><span>Categorys</span></li>
            <li><a href="{{ route('item.index', $category->id) }}"><span>{{ $category->title }}</span></a></li>
            <li class="active"><span>View {{ Str::singular($category->title) }}</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">{{ Str::singular($category->title) }} Details</h3>
            </div>

            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Field Name</th>
                            <th>Field Type</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach ($category->fields as $field)
                        <tr>
                            <td>{{ $field->getName() }}</td>
                            <td>{{ $field->getType() }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @can('owner', $category)
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Shared with</h3>
                </div>

                <div class="box-body">
                    <ul>
                        @forelse ($category->sharedWith() as $share)
                            <li>
                                {!! $share->present()->sharedWith !!}

                                ({!! $share->present()->grantThrough !!})
                            </li>
                        @empty
                            <li>Category is not shared</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Device sharing audit</h3>
                </div>

                <div class="box-body">
                    <ul>
                        @forelse ($category->devicesSharedWith() as $share)
                            <li>
                                {!! $share->present()->sharedWith !!}

                                ({!! $share->present()->grantThrough(true) !!})
                            </li>
                        @empty
                            <li>No devices in this section have been shared</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endcan

        <a href="{{ route('category.index') }}" class="btn btn-primary">Go Back</a>
    </section>
@stop
