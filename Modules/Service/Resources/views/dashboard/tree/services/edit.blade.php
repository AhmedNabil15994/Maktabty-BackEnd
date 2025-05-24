@foreach ($mainCategories as $category)
    <ul>
        <li id="{{ $category->id }}"
            data-jstree='{"opened":true
		{{ $service->categories->contains($category->id) ? ',"selected":true' : '' }} }'>
            {{ $category->title }}
            @if ($category->dashboardChildren->count() > 0)
                @include('service::dashboard.tree.services.edit', [
                    'mainCategories' => $category->dashboardChildren,
                ])
            @endif
        </li>
    </ul>
@endforeach
