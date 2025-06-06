@foreach ($mainCategories as $cat)
    @if ($category->id != $cat->id)
        <ul>
            <li id="{{ $cat->id }}"
                data-jstree='{"opened":true @if ($category->service_category_id == $cat->id) ,"selected":true @endif }'>
                {{ $cat->title }}
                @if ($cat->dashboardChildren->count() > 0)
                    @include('service::dashboard.tree.categories.edit', [
                        'mainCategories' => $cat->dashboardChildren,
                    ])
                @endif
            </li>
        </ul>
    @endif
@endforeach
